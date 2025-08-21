<?php

namespace App\Filament\Resources\Products;

use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Filament\Resources\Products\Schemas\ProductForm;
use App\Filament\Resources\Products\Tables\ProductsTable;
use App\Models\Product;
use App\Models\Subscription;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingBag;

    protected static ?string $navigationLabel = 'Produk';

    protected static string|UnitEnum|null $navigationGroup = 'Manajemen Menu';

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return parent::getEloquentQuery();
        }

        return parent::getEloquentQuery()->where('user_id', $user->id);
    }

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
    public static function form(Schema $schema): Schema
    {
        return ProductForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductsTable::configure($table);
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
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
