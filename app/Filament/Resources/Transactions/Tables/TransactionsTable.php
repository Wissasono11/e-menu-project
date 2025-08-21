<?php

namespace App\Filament\Resources\Transactions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Schemas\Components\View;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class TransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nama Toko')
                    ->hidden(fn() => Auth::user()->role === 'store'),
                TextColumn::make('code')
                    ->label('Kode Transaksi'),
                TextColumn::make('name')
                    ->label('Nama Customer'),
                TextColumn::make('phone_number')
                    ->label('Nomor Hp Customer'),
                TextColumn::make('table_number')
                    ->label('Nomor Meja'),
                TextColumn::make('payment_method')
                    ->label('Metode Pembayaran'),
                TextColumn::make('total_price')
                    ->label('Total Harga')
                    ->formatStateUsing(fn(string $state) => 'Rp. ' . number_format($state)),
                TextColumn::make('status')
                    ->label('Status Pembayaran'),
                TextColumn::make('created_at')
                    ->label('Tanggal Transaksi')
                    ->dateTime(),
            ])
            ->filters([
                SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->label('Toko')
                    ->hidden(fn() => Auth::user()->role === 'store'),
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
