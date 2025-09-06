<?php

namespace App\Filament\Resources\Products\Tables;

use App\Models\ProductCategory;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nama Toko')
                    ->hidden(fn() => Auth::user()->role === 'store'),
                TextColumn::make('productCategory.name')
                    ->label('Kategori Menu'),
                TextColumn::make('name')
                    ->label('Nama Menu'),
                ImageColumn::make('image')
                    ->label('Foto Menu')
                    ->disk('public'),
                TextColumn::make('price')
                    ->label('Harga Menu')
                    ->formatStateUsing(fn(string $state) => 'Rp ' . number_format($state)),
                TextColumn::make('rating')
                    ->label('Rating Menu'),
                ToggleColumn::make('is_popular')
                    ->label('Menu Populer')
            ])
            ->filters([
                SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->label('Toko')
                    ->hidden(fn() => Auth::user()->role === 'store'),
                SelectFilter::make('product_category_id')
                    ->options(function () {
                        if (Auth::user()->role === 'admin') {
                            return ProductCategory::pluck('name', 'id');
                        }

                        return ProductCategory::where('user_id', Auth::user()->id)
                            ->pluck('name', 'id');
                    })
                    ->label('Kategori Menu'),
            ])
            ->recordActions([
                EditAction::make(),
                ViewAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
