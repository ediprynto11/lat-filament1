<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class BlogPostsChart extends ChartWidget
{
    protected static ?string $heading = 'Penjualan';

    protected function getData(): array
    {
        return [
             'datasets' => [
            [
                'label' => ' Terjual ',
                'data' => [50, 70, 65, 90, 100, 77, 85, 94, 85, 95, 98, 89],
            ],
        ],
        'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
