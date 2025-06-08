<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Faktur;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\FakturResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\FakturResource\RelationManagers;
use Illuminate\Database\Eloquent\Factories\Relationship;

class FakturResource extends Resource
{
    protected static ?string $model = Faktur::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('kode_faktur'),
                DatePicker::make('tanggal_faktur'),
                TextInput::make('kode_customer'),
                Select::make('customer_id')
                    ->required()
                    ->relationship('customer', 'nama'),
                Repeater::make('detail')
                    ->relationship()
                    ->schema([
                        Select::make('barang_id')
                            ->relationship('barang', 'nama'),
                        TextInput::make('diskon')
                            ->numeric(),
                        TextInput::make('harga')
                            ->numeric(),
                        TextInput::make('nama_barang'),
                        TextInput::make('subtotal')
                            ->numeric(),
                        TextInput::make('qty')
                            ->numeric(),
                        TextInput::make('hasil_qty')
                            ->numeric(),
                    ]),
                TextInput::make('keterangan'),
                TextInput::make('total'),
                TextInput::make('nominal_charge'),
                TextInput::make('charge'),
                TextInput::make('total_final'),
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
                TextColumn::make('total'),
                TextColumn::make('nominal_charge'),
                TextColumn::make('charge'),
                TextColumn::make('total_final'),
            ])
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
