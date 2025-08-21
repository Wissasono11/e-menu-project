<?php

namespace App\Filament\Resources\Subscriptions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class SubscriptionsTable
{
    public static function configure(Table $table): Table
    {

        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nama Toko')
                    ->hidden(fn() => Auth::user()->role === 'store'),
                TextColumn::make('created_at')
                    ->label('Tanggal Mulai')
                    ->dateTime(),
                TextColumn::make('end_date')
                    ->label('Tanggal Berakhir')
                    ->dateTime(),
                ImageColumn::make('subscriptionPayment.proof')
                    ->label('Bukti Pembayaran'),
                TextColumn::make('subscriptionPayment.status')
                    ->badge()
                    ->label('Status Pembayaran')
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'success' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending' => 'Pending',
                        'success' => 'Success',
                        'failed' => 'Failed',
                        default => $state,
                    })
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ...(Auth::user()->role === 'admin' ? [EditAction::make()] : []),
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
