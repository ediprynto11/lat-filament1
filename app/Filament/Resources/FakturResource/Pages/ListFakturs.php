<?php

namespace App\Filament\Resources\FakturResource\Pages;

use App\Filament\Resources\FakturResource;
use Filament\Actions;
use App\Models\Faktur;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Symfony\Component\HttpFoundation\StreamedResponse;


class ListFakturs extends ListRecords
{
    protected static string $resource = FakturResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
             Action::make('Export CSV')
                ->label('Unduh')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function (): StreamedResponse {
                    $filename = 'faktur_export.csv';

                    $headers = [
                        'Content-Type' => 'text/csv',
                        'Content-Disposition' => "attachment; filename=\"$filename\"",
                    ];

                    $callback = function () {
                        $handle = fopen('php://output', 'w');
                        // Header kolom CSV
                        fputcsv($handle, ['No', 'Kode Faktur', 'Tanggal', 'Customer', 'Total']);

                        $fakturs = Faktur::with('customer')->get();

                        foreach ($fakturs as $index => $faktur) {
                            fputcsv($handle, [
                                $index + 1,
                                $faktur->kode_faktur,
                                $faktur->tanggal_faktur,
                                $faktur->customer->nama ?? '-', // pastikan relasi customer
                                $faktur->total,
                            ]);
                        }

                        fclose($handle);
                    };

                    return response()->stream($callback, 200, $headers);
                }),
        ];
    }
}
