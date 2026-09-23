<x-filament-widgets::widget>
    <x-filament::section>
        <livewire:admin.orders-widget />

        <style>
            .active_pill {
                background: #000;
                color: #fff;
            }
            .st_paid {
                background: oklch(98.2% 0.018 155.826);
                color: oklch(59.6% 0.145 163.225);
            }
            .st_pending {
                background:oklch(98.7% 0.022 95.277);
                color: oklch(66.6% 0.179 58.318);
            }
        </style>
    </x-filament::section>
</x-filament-widgets::widget>
