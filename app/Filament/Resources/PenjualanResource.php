<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenjualanResource\Pages;
use App\Filament\Resources\PenjualanResource\RelationManagers;
use App\Models\Penjualan;
use Dom\Text;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PenjualanResource extends Resource
{
    protected static ?string $model = Penjualan::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $slug = 'laporan-penjualan';

    protected static ?string $navigationLabel = 'Laporan Penjualan';

    protected static ?string $label = 'Laporan Penjualan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no')
                    ->label('No')
                    ->state(function ($record, $livewire) {
                        return ($livewire->getTableRecords()->firstItem() ?? 0) + $livewire->getTableRecords()->search(fn($item) => $item->id === $record->id);
                    }),
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->sortable()
                    ->searchable()
                    ->date('d/m/Y'),
                TextColumn::make('kode')
                    ->label('Kode Faktur')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('jumlah')
                    ->label('Jumlah')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn(Penjualan $record): string => 'Rp ' . number_format($record->jumlah, 0, '.', '.')),
                TextColumn::make('customer.nama')
                    ->label('Nama Customer')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        '0' => 'danger',
                        '1' => 'info',
                    })
                    ->formatStateUsing(fn(Penjualan $record): string => $record->status == 0 ? 'Belum Lunas' : 'Lunas')
                    ->label('Status'),
            ])
            ->emptyStateHeading('Tidak ada data penjualan')
            ->emptyStateDescription('Silahkan tambahkan faktur terlebih dahulu')
            ->emptyStateIcon('heroicon-o-chart-bar')
            ->emptyStateActions([
                Action::make('create')
                    ->label('Buat Faktur')
                    ->url(route('filament.admin.resources.faktur.create'))
                    ->icon('heroicon-m-plus')
                    ->button(),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPenjualans::route('/'),
            'create' => Pages\CreatePenjualan::route('/create'),
            'edit' => Pages\EditPenjualan::route('/{record}/edit'),
        ];
    }
}
