<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BarangResource\Pages;
use App\Filament\Resources\BarangResource\RelationManagers;
use App\Models\Barang;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class BarangResource extends Resource
{
    protected static ?string $model = Barang::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $slug = 'kelola-barang';

    protected static ?string $navigationGroup = 'Kelola';

    protected static ?string $navigationLabel = 'Barang';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('kode')
                    ->required()
                    ->maxLength(255)
                    ->live()
                    ->unique(ignoreRecord: true)
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state !== null && preg_match('/[a-z]/', $state)) {
                            Notification::make()
                                ->title('Huruf kecil akan diubah menjadi huruf besar.')
                                ->warning()
                                ->send();

                            $set('kode', strtoupper($state));
                        }
                    })
                    ->rules(['regex:/^[A-Z0-9]*$/'])
                    ->validationMessages([
                        'regex' => 'Kolom kode hanya boleh berisi huruf besar dan angka.',
                        'unique' => 'Kode barang sudah digunakan, silakan masukkan kode lain.',
                    ]),
                Forms\Components\Textarea::make('deskripsi')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('stok')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('harga')
                    ->required()
                    ->prefix('Rp')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no')
                    ->label('No')
                    ->state(function ($record, $livewire) {
                        return ($livewire->getTableRecords()->firstItem() ?? 0) + $livewire->getTableRecords()->search(fn($item) => $item->id === $record->id);
                    }),
                Tables\Columns\TextColumn::make('nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kode')
                    ->searchable(),
                Tables\Columns\TextColumn::make('stok')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn(Barang $record): string => number_format($record->stok, 0, '.', '.')),
                Tables\Columns\TextColumn::make('harga')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn(Barang $record): string => 'Rp ' . number_format($record->harga, 0, '.', '.')),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->emptyStateHeading('Tidak ada data barang')
            ->emptyStateDescription('Silahkan tambahkan barang terlebih dahulu')
            ->emptyStateIcon('heroicon-o-cube')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListBarangs::route('/'),
            'create' => Pages\CreateBarang::route('/create'),
            'edit' => Pages\EditBarang::route('/{record}/edit'),
        ];
    }
}
