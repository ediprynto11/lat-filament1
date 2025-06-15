<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;


class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $slug = 'kelola-customer';

    protected static ?string $navigationGroup = 'Kelola';

    protected static ?string $navigationLabel = 'Customer';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('kode')
                    ->maxLength(255)
                    ->required()
                    ->unique(ignoreRecord: true)
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
                Forms\Components\Textarea::make('alamat')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('email')
                    ->required()
                    ->email()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->validationMessages([
                        'unique' => 'Email sudah digunakan, silakan gunakan email lain.',
                    ]),
                Forms\Components\TextInput::make('telepon')
                    ->required()
                    ->maxLength(20)
                    ->tel()
                    ->default(''),
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
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kode')
                    ->searchable(),
                Tables\Columns\TextColumn::make('alamat'),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->emptyStateHeading('Tidak ada data customer')
            ->emptyStateDescription('Silahkan tambahkan customer terlebih dahulu')
            ->emptyStateIcon('heroicon-o-users')
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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
