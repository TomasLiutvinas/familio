<?php

namespace App\Filament\Widgets;

use App\Models\Person;
use App\Services\ChargeBalances;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Collection;

class PendingPaymentsWidget extends TableWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected ?Collection $pending = null;

    protected function balances(): Collection
    {
        return $this->pending ??= app(ChargeBalances::class)->pendingByPerson();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Person::query()->whereIn('id', $this->balances()->keys())->orderBy('name'))
            ->heading('Pending Payments')
            ->description('Remaining shares on recorded charges. Your owner share is already covered; partial payments reduce the amount owed.')
            ->emptyStateHeading('Everyone is settled up')
            ->emptyStateDescription('No outstanding member shares on recorded charges.')
            ->columns([
                TextColumn::make('name')->label('Person')->searchable(),
                TextColumn::make('remaining')->label('Still owed')->money('EUR')
                    ->getStateUsing(fn (Person $record) => $this->balances()->get($record->id)['total_cents'] / 100),
                TextColumn::make('breakdown')->label('Subscriptions / years')->listWithLineBreaks()
                    ->getStateUsing(fn (Person $record) => array_map(
                        fn ($charge) => $charge['label'].' — €'.number_format($charge['outstanding_cents'] / 100, 2),
                        $this->balances()->get($record->id)['charges'])),
            ]);
    }
}
