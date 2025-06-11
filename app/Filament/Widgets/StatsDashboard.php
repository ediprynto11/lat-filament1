<?php

namespace App\Filament\Widgets;

use App\Models\Barang;
use App\Models\Faktur;
use App\Models\Customer;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsDashboard extends BaseWidget
{
    protected function getStats(): array
    {
        $countCustomer = Customer::count();
        $countBarang = Barang::count();
        $countFaktur = Faktur::count();
        return [
            Stat::make('Total Customer', $countCustomer .' Customer'),
            Stat::make('Total Barang', $countBarang .' Barang'),
            Stat::make('Total Faktur', $countFaktur .' Faktur'),
        ];
    }
}
