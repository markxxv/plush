<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class OrdersWidget extends Widget
{
    protected string $view = 'filament.widgets.orders-widget';
    protected int|string|array $columnSpan = 'full';
}
