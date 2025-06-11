<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateCustomer extends CreateRecord
{
    protected static string $resource = CustomerResource::class;
    protected function getCreatedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title('Berhasil dibuat')
            ->body('Data customer telah berhasil dibuat.')
            ->icon('heroicon-o-users');
    }
}
