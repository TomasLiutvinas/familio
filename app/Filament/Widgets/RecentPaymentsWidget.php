<?php

namespace App\Filament\Widgets;

use App\Models\MemberPayment;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentPaymentsWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    protected string $view = 'filament.widgets.recent-payments';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                MemberPayment::query()
                    ->with(['person', 'charge.subscription'])
                    ->orderBy('paid_on', 'desc'),
            )
            ->columns([
                TextColumn::make('person.name')
                    ->label('Person')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('charge.subscription.service_name')
                    ->label('Subscription')
                    ->sortable()
                    ->default('—'),

                TextColumn::make('amount_eur')
                    ->label('Amount')
                    ->money('EUR')
                    ->sortable(),

                TextColumn::make('paid_on')
                    ->label('Paid On')
                    ->date()
                    ->sortable(),

                TextColumn::make('notes')
                    ->label('Notes')
                    ->limit(30)
                    ->default('—'),
            ])
            ->heading(null)
            ->paginated([10])
            ->defaultPaginationPageOption(10);
    }
}
