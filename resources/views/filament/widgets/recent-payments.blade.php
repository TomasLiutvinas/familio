<x-filament-widgets::widget>
    <x-filament::section
        heading="Recent Payments"
        description="Latest 10 payment transactions"
        collapsible
        collapsed
        persist-collapsed
        id="familio-recent-payments"
        :has-content-el="false"
    >
        {{ $this->table }}
    </x-filament::section>
</x-filament-widgets::widget>
