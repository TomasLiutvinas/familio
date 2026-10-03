<?php

namespace App\Filament\Actions;

use App\Models\Subscription;
use App\Services\SubscriptionPriceChange;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class ChangeSubscriptionPrice
{
    public static function make(): Action
    {
        return Action::make('changePrice')
            ->label('Change monthly price')
            ->icon('heroicon-o-currency-euro')
            ->visible(fn (Subscription $record) => $record->billing_period === 'yearly')
            ->modalHeading('Change monthly price')
            ->modalDescription('Adjust annual collections only for the affected months. Existing payments stay attached. Apply successive changes in date order; edit a charge directly to correct an older period.')
            ->modalSubmitActionLabel('Apply price change')
            ->schema([
                TextInput::make('monthly_price')->label('New monthly price (€)')->numeric()->minValue(0.01)
                    ->step('0.01')->required()->live(onBlur: true),
                DatePicker::make('effective_from')->label('First month at the new price')
                    ->default(now()->startOfMonth())->required()->live()
                    ->helperText('Choose the first day of the month.'),
                Text::make(function (Get $get, Subscription $record): string {
                    if (! $get('monthly_price') || ! $get('effective_from')) {
                        return 'Enter the new price and month to preview the annual collections.';
                    }
                    try {
                        $preview = app(SubscriptionPriceChange::class)->preview($record, $get('effective_from'), (float) $get('monthly_price'));
                    } catch (ValidationException $exception) {
                        return collect($exception->errors())->flatten()->implode(' ');
                    }
                    $lines = ['Future full-year price: €'.number_format($preview['annual_cents'] / 100, 2).'.'];
                    foreach ($preview['changes'] as $change) {
                        $lines[] = sprintf('%s collection: €%.2f → €%.2f (%d months at the new rate).',
                            $change['coverage'], $change['before_cents'] / 100, $change['after_cents'] / 100, $change['months']);
                    }
                    if (! $preview['changes']) {
                        $lines[] = 'Existing annual collections are unchanged.';
                    }

                    return implode(' ', $lines);
                }),
            ])
            ->action(function (Subscription $record, array $data, Component $livewire): void {
                app(SubscriptionPriceChange::class)->apply($record, $data['effective_from'], (float) $data['monthly_price']);
                if ($livewire instanceof EditRecord) {
                    $livewire->getRecord()->refresh();
                    $livewire->refreshFormData(['default_amount_eur']);
                }
                Notification::make()->title('Subscription price updated')->success()->send();
            });
    }
}
