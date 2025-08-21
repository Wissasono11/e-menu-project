<?php

namespace App\Filament\Resources\Transactions\Schemas;

use App\Filament\Resources\Transactions\TransactionResource;
use App\Models\Product;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('Toko')
                    ->relationship('user', 'name')
                    ->hidden(fn() => Auth::user()->role === 'store')
                    ->reactive()
                    ->required(),
                TextInput::make('code')
                    ->label('Kode Transaksi')
                    ->default(fn() => 'TRX-' . mt_rand(10000, 999999))
                    ->readOnly()
                    ->required(),
                TextInput::make('name')
                    ->label('Nama Customer')
                    ->required(),
                TextInput::make('phone_number')
                    ->label('Nomor Hp Customer')
                    ->maxLength(15)
                    ->required(),
                TextInput::make('table_number')
                    ->label('Nomor Meja'),
                Select::make('payment_method')
                    ->label('Metode Pembayaran')
                    ->options([
                        'cash' => 'Tunai',
                        'midtrans' => 'Midtrans',
                    ])
                    ->required(),
                Select::make('status')
                    ->label('Status Pembayaran')
                    ->options([
                        'pending' => 'Tertunda',
                        'success' => 'Berhasil',
                        'failed' => 'Gagal',
                    ])
                    ->required(),
                Repeater::make('transactionDetails')
                    ->label('Detail Transaksi')
                    ->relationship()
                    ->schema([
                        Select::make('product_id')
                            ->relationship('product', 'name')
                            ->options(function (callable $get) {
                                if (Auth::user()->role === 'admin') {
                                    return Product::all()->mapWithKeys(function ($product) {
                                        return [$product->id => "$product->name (Rp " . number_format((float)$product->price) . ")"];
                                    });
                                }
                                return Product::where('user_id', Auth::user()->id)->get()->mapWithKeys(function ($product) {
                                    return [$product->id => "$product->name (Rp " . number_format((float)$product->price) . ")"];
                                });
                            })
                            ->required(),
                        TextInput::make('quantity')
                            ->label('Jumlah')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->default(1),
                        TextInput::make('note')
                            ->label('Catatan'),
                    ])->columnSpanFull()
                    ->live()
                    ->afterStateUpdated(fn (Get $get, Set $set) => TransactionResource::updateTotals($get, $set))
                    ->reorderable(false),
                TextInput::make('total_price')
                    ->label('Total Harga')
                    ->required()
                    ->prefix('Rp ')
                    ->readOnly(),
            ]);
    }
}
