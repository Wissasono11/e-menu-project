<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\ProductCategory;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make("user_id")
                    ->label('Toko')
                    ->relationship('user', 'name')
                    ->required()
                    ->reactive()
                    ->hidden(fn() => Auth::user()->role === 'store'),
                Select::make('product_category_id')
                    ->label('Kategori Produk')
                    ->required()
                    ->disabled(fn(callable $get) => $get('user_id') === null)
                    ->relationship('productCategory', 'name')
                    ->options(function (callable $get) {
                        $userId = $get('user_id');

                        if (!$userId) {
                            return [];
                        }
                        return ProductCategory::where('user_id', $userId)->pluck('name', 'id');
                    })
                    ->hidden(fn() => Auth::user()->role === 'store'),
                Select::make('product_category_id')
                    ->label('Kategori Produk')
                    ->required()
                    ->relationship('productCategory', 'name')
                    ->options(function (callable $get) {
                        return ProductCategory::where('user_id', Auth::user()->id)
                            ->pluck('name', 'id');
                    })
                    ->hidden(fn() => Auth::user()->role === 'admin'),
                FileUpload::make('image')
                    ->label('Foto Menu')
                    ->image()
                    ->required(),
                TextInput::make('name')
                    ->label('Nama Menu')
                    ->required(),
                Textarea::make('description')
                    ->label('Deskripsi Menu')
                    ->required(),
                TextInput::make('price')
                    ->label('Harga Menu')
                    ->required()
                    ->numeric()
                    ->minValue(1),
                TextInput::make('rating')
                    ->label('Rating Menu')
                    ->required()
                    ->numeric(),
                Toggle::make('is_popular')
                    ->label('Menu Populer')
                    ->required(),
                    Repeater::make('productIngredients')
                    ->label('Bahan Baku Menu')
                    ->relationship('productIngredients')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Bahan')
                    ])->columnSpanFull()
            ]);
    }
}
