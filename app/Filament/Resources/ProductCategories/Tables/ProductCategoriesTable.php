<?php

namespace App\Filament\Resources\ProductCategories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Auth;

class ProductCategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nama Toko')
                    ->hidden(fn() => Auth::user()->role === 'store'),
                TextColumn::make('name')
                    ->label('Nama Kategori'),
                ImageColumn::make('icon')
                    ->label('Ikon Kategori'),
            ])
            ->filters([
                SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->label('Toko')
                    ->hidden(fn() => Auth::user()->role === 'store'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                ViewAction::make(),
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
