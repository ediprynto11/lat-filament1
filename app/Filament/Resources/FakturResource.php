<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Barang;
use App\Models\Faktur;
use Filament\Forms\Get;
use App\Models\Customer;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\FakturResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Factories\Relationship;
use App\Filament\Resources\FakturResource\RelationManagers;
use NunoMaduro\Collision\Adapters\Phpunit\State;

class FakturResource extends Resource
{
    protected static ?string $model = Faktur::class;

    protected static ?string $slug = 'faktur';

    protected static ?string $navigationGroup = 'Kelola';

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('kode_faktur')
                    ->columnSpan(2),
                DatePicker::make('tanggal_faktur')
                    ->columnSpan([
                        'default' => 2,
                        'md' => 1,
                        'lg' => 1,
                        'xl' => 1,
                    ]),
                Select::make('customer_id')
                    ->reactive()
                    ->required()
                    ->relationship('customer', 'nama')
                    ->columnSpan([
                        'default' => 2,
                        'md' => 1,
                        'lg' => 1,
                        'xl' => 1,
                    ])
                    ->afterStateUpdated(function ($state, callable $set) {
                        $customer = Customer::find($state);
                        if ($customer) {
                            $set('kode_customer', $customer->kode);
                        }
                    })
                    ->afterStateHydrated(function ($state, callable $set) {
                        $customer = Customer::find($state);
                        if ($customer) {
                            $set('kode_customer', $customer->kode);
                        }
                    }),
                TextInput::make('kode_customer')
                    ->disabled()
                    ->dehydrated()
                    ->columnSpan(2),
                Repeater::make('detail')
                    ->relationship()
                    ->schema([
                        Select::make('barang_id')
                            ->columnSpan([
                                'default' => 2,
                                'md' => 1,
                                'lg' => 1,
                                'xl' => 1,
                            ])
                            ->reactive()
                            ->relationship('barang', 'nama')
                            ->afterStateUpdated(function ($state, callable $set) {
                                $barang = Barang::find($state);
                                if ($barang) {
                                    $set('harga', $barang->harga);
                                    $set('nama_barang', $barang->nama);
                                }
                            }),
                        TextInput::make('nama_barang')
                            ->columnSpan(2)
                            ->columnSpan([
                                'default' => 2,
                                'md' => 1,
                                'lg' => 1,
                                'xl' => 1,
                            ]),
                        TextInput::make('harga')
                            ->prefix('Rp')
                            ->columnSpan([
                                'default' => 2,
                                'md' => 1,
                                'lg' => 1,
                                'xl' => 1,
                            ]),
                        TextInput::make('qty')
                            ->numeric()
                            ->columnSpan([
                                'default' => 2,
                                'md' => 1,
                                'lg' => 1,
                                'xl' => 1,
                            ])
                            ->reactive()
                            ->afterStateUpdated(function (Set $set, $state, Get $get) {
                                $tampungHarga = $get('harga');
                                $set('hasil_qty', intval($state * $tampungHarga));
                            }),
                        TextInput::make('hasil_qty')
                            ->numeric()
                            ->prefix('Rp')
                            ->columnSpan([
                                'default' => 2,
                                'md' => 1,
                                'lg' => 1,
                                'xl' => 1,
                            ]),
                        TextInput::make('diskon')
                            ->reactive()
                            ->afterStateUpdated(function (Set $set, $state, Get $get) {
                                $hasilQty = $get('hasil_qty');
                                $diskon = $hasilQty * ($state / 100);
                                $hasil = $hasilQty - $diskon;

                                $set('subtotal', $hasil);
                            })
                            ->numeric()
                            ->columnSpan([
                                'default' => 2,
                                'md' => 1,
                                'lg' => 1,
                                'xl' => 1,
                            ]),
                        TextInput::make('subtotal')
                            ->numeric()
                            ->prefix('Rp')
                            ->columnSpan([
                                'default' => 2,
                                'md' => 1,
                                'lg' => 1,
                                'xl' => 1,
                            ]),
                    ])
                    ->live()
                    ->columnSpan(2),
                Textarea::make('keterangan')
                    ->columnSpan(2),
                TextInput::make('total')
                    ->prefix('Rp')
                    ->placeholder(function (Set $set, Get $get) {
                        $detail = collect($get('detail',))->pluck('subtotal')->sum();
                        if ($detail == null) {
                            $set('total', 0);
                        } else {
                            $set('total', $detail);
                        }
                    })
                    ->columnSpan([
                        'default' => 2,
                        'md' => 1,
                        'lg' => 1,
                        'xl' => 1,
                    ]),
                TextInput::make('nominal_charge')
                    ->columnSpan([
                        'default' => 2,
                        'md' => 1,
                        'lg' => 1,
                        'xl' => 1,
                    ])
                    ->reactive()
                    ->afterStateUpdated(function (Set $set, $state, Get $get) {
                        $total = $get('total');
                        $charge = $total * ($state / 100);
                        $hasil = $total + $charge;

                        $set('total_final', $hasil);
                        $set('charge', $charge);
                    }),
                TextInput::make('charge')
                    ->disabled()
                    ->dehydrated()
                    ->columnSpan(2)
                    ->prefix('Rp'),
                   TextInput::make('total_final')
                    ->columnSpan(2)
                    ->prefix('Rp'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_faktur')
                    ->searchable(),
                TextColumn::make('tanggal_faktur'),
                TextColumn::make('kode_customer')
                    ->searchable(),
                TextColumn::make('customer.nama')
                    ->searchable(),
                TextColumn::make('keterangan'),
                TextColumn::make('total')
                    ->formatStateUsing(fn(Faktur $record): string => 'Rp ' . number_format($record->total, 0, '.', '.')),
                TextColumn::make('nominal_charge'),
                TextColumn::make('charge')
                    ->formatStateUsing(fn(Faktur $record): string => 'Rp ' . number_format($record->charge, 0, '.', '.')),
                TextColumn::make('total_final')
                    ->formatStateUsing(fn(Faktur $record): string => 'Rp ' . number_format($record->total_final, 0, '.', '.')),
            ])
             ->emptyStateHeading('Tidak ada data faktur')
             ->emptyStateDescription('Silahkan buat faktur terlebih dahulu')
             ->emptyStateIcon('heroicon-o-clipboard-document-list')
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListFakturs::route('/'),
            'create' => Pages\CreateFaktur::route('/create'),
            'edit' => Pages\EditFaktur::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
