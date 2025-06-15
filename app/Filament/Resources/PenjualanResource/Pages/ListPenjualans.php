<?php

namespace App\Filament\Resources\PenjualanResource\Pages;

use App\Filament\Resources\PenjualanResource;
use Filament\Actions;
use App\Models\Penjualan;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListPenjualans extends ListRecords
{
    protected static string $resource = PenjualanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
            Action::make('Export CSV')
                ->label('Unduh')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function (): StreamedResponse {
                    $filename = 'penjualan_export.csv';

                    $headers = [
                        'Content-Type' => 'text/csv',
                        'Content-Disposition' => "attachment; filename=\"$filename\"",
                    ];

                    $callback = function () {
                        $handle = fopen('php://output', 'w');
                        // Header kolom CSV
                        fputcsv($handle, ['No', 'Kode', 'Tanggal', 'Jumlah']);

                        $penjualans = Penjualan::all();

                        foreach ($penjualans as $index => $penjualan) {
                            fputcsv($handle, [
                                $index + 1,
                                $penjualan->kode,
                                $penjualan->tanggal,
                                $penjualan->jumlah,
                            ]);
                        }

                        fclose($handle);
                    };

                    return response()->stream($callback, 200, $headers);
                }),
        ];
    }
}
