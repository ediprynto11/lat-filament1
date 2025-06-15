<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use App\Models\Customer;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
              Action::make('Export CSV')
                ->label('Unduh')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function (): StreamedResponse {
                    $filename = 'customers_export.csv';

                    $headers = [
                        'Content-Type' => 'text/csv',
                        'Content-Disposition' => "attachment; filename=\"$filename\"",
                    ];

                    $callback = function () {
                        $handle = fopen('php://output', 'w');

                        // Header kolom CSV
                        fputcsv($handle, ['No', 'Nama', 'Kode Customer', 'Alamat', 'Email']);

                        $customers = Customer::all();
                        foreach ($customers as $index => $customer) {
                            fputcsv($handle, [
                                $index + 1,
                                $customer->nama ?? '',
                                $customer->kode ?? '',
                                $customer->alamat ?? '',
                                $customer->email ?? '',
                            ]);
                        }

                        fclose($handle);
                    };

                    return response()->stream($callback, 200, $headers);
                }),
        ];
    }
}
