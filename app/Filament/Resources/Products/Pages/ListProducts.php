<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Models\Product;
use App\Models\Subscription;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Support\Facades\Auth;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function canCreateProduct(): bool
    {
        if (Auth::user()->role === 'admin') {
            return true;
        }

        $countProduct = Product::where('user_id', Auth::user()->id)->count();


        if ($countProduct < 5) {
            return true;
        }


        $subscription = Subscription::where('user_id', Auth::user()->id)
            ->where('end_date', '>', now())
            ->where('is_active', true)
            ->whereHas('subscriptionPayment', function ($query) {
                $query->where('status', 'success');
            })
            ->latest()
            ->first();


        return $subscription !== null;
    }

    protected function getHeaderActions(): array
    {
        $actions = [];

        if ($this->canCreateProduct()) {
            $actions[] = CreateAction::make();
        } else {
            $countProduct = Product::where('user_id', Auth::user()->id)->count();

            if ($countProduct >= 5 && Auth::user()->role !== 'admin') {
                $actions[] = Action::make('subscription_alert')
                    ->label('Produk Kamu Melebihi Batas Penggunaan Gratis, Silahkan Berlangganan')
                    ->color('danger')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->url('/admin/subscriptions');
            }
        }

        return $actions;
    }
}
