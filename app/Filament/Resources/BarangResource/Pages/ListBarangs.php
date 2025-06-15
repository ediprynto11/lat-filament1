<?php

namespace App\Filament\Resources\BarangResource\Pages;

use App\Filament\Resources\BarangResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\Barang;

class ListBarangs extends ListRecords
{
    protected static string $resource = BarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('Export CSV')
                ->label('Unduh')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function (): StreamedResponse {
                    $filename = 'barang_export.csv';

                    $headers = [
                        'Content-Type' => 'text/csv',
                        'Content-Disposition' => "attachment; filename=\"$filename\"",
                    ];

                    $callback = function () {
                        $handle = fopen('php://output', 'w');
                        // Header CSV
                        fputcsv($handle, ['No', 'Nama', 'Kode', 'Stok', 'Harga']);

                        $barangs = Barang::all();
                        foreach ($barangs as $index => $barang) {
                            fputcsv($handle, [
                                $index + 1,
                                $barang->nama,
                                $barang->kode,
                                $barang->stok,
                                $barang->harga,
                            ]);
                        }

                        fclose($handle);
                    };

                    return response()->stream($callback, 200, $headers);
                }),
        ];
    }

    // protected function tableContentFooter(): ?\Illuminate\Contracts\View\View
    // {
    //     return view('filament.tables.custom-footer');
    // }
}