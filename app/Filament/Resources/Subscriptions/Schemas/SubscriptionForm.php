<?php

namespace App\Filament\Resources\Subscriptions\Schemas;

use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class SubscriptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('Nama Toko')
                    ->options(User::all()->pluck('name', 'id')->toArray())
                    ->required()
                    ->hidden(fn() => Auth::user()->role === 'store'),
                Toggle::make('is_active')
                    ->label('Aktifkan Subscription')
                    ->required()
                    ->hidden(fn() => Auth::user()->role === 'store'),
                Repeater::make('subscriptionPayment')
                    ->label('Pembayaran Subscription')
                    ->relationship()
                    ->schema([
                        FileUpload::make('proof')
                            ->label('Bukti Transfer Ke Rekening 1590012373253 A/N Bayu Wicaksono Sebesar Rp 200.000')
                            ->required()
                            ->columnSpanFull(),
                        Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'success' => 'Success',
                                'failed' => 'Failed',
                            ])
                            ->required()
                            ->label('Status Pembayaran')
                            ->columnSpanFull()
                            ->hidden(fn() => Auth::user()->role === 'store'),
                    ])
                    ->columnSpanFull()
                    ->addable(false)

            ]);
    }
}
