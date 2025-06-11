<?php

namespace App\Filament\Resources\FakturResource\Pages;

use App\Filament\Resources\FakturResource;
use App\Models\penjualan;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateFaktur extends CreateRecord
{
    protected static string $resource = FakturResource::class;

     protected function afterCreate(): void
    {
        penjualan::create([
            'kode' => $this->record->kode_faktur,
            'tanggal' => $this->record->tanggal_faktur,
            'jumlah' => $this->record->total,
            'customer_id' => $this->record->customer_id,
            'faktur_id' => $this->record->id,
            'keterangan' => $this->record->keterangan,
            'status' => 0,
        ]);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Berhasil dibuat')
            ->body('Faktur telah berhasil dibuat.')
            ->icon('heroicon-o-clipboard-document-list');
    }
}

