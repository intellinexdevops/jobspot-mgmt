<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class SubscriptionCharts extends ChartWidget
{
    protected static ?string $heading = 'Revenue';
    protected static bool $isLazy = false;
    protected static ?string $description = 'Revenue for each month';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Revenue for each month',
                    'data' => [0, 10, 5, 2, 21, 32, 45, 74, 65, 45, 77, 89],
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
