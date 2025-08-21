<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use App\Models\SubscriptionPayment;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class DashboardOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalTransaction = 0;
        $totalAmount = 0;

        $totalTransaction = Transaction::where('user_id', Auth::user()->id)->where('status', 'success')->count();
        $totalAmount = Transaction::where('user_id', Auth::user()->id)->where('status', 'success')->sum('total_price');

        if (Auth::user()->role === 'admin') {
            // Data bulan ini
            $currentMonthUsers = User::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count();

            $currentMonthSubscriptions = SubscriptionPayment::where('status', 'success')
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count();

            $currentMonthProducts = Product::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count();

            // Data hari ini
            $todayUsers = User::whereDate('created_at', Carbon::today())->count();
            $todaySubscriptions = SubscriptionPayment::where('status', 'success')
                ->whereDate('created_at', Carbon::today())
                ->count();
            $todayProducts = Product::whereDate('created_at', Carbon::today())->count();

            // Data kemarin
            $yesterdayUsers = User::whereDate('created_at', Carbon::yesterday())->count();
            $yesterdaySubscriptions = SubscriptionPayment::where('status', 'success')
                ->whereDate('created_at', Carbon::yesterday())
                ->count();
            $yesterdayProducts = Product::whereDate('created_at', Carbon::yesterday())->count();

            // Data bulan lalu
            $lastMonthUsers = User::whereMonth('created_at', Carbon::now()->subMonth()->month)
                ->whereYear('created_at', Carbon::now()->subMonth()->year)
                ->count();

            $lastMonthSubscriptions = SubscriptionPayment::where('status', 'success')
                ->whereMonth('created_at', Carbon::now()->subMonth()->month)
                ->count();

            $lastMonthProducts = Product::whereMonth('created_at', Carbon::now()->subMonth()->month)
                ->whereYear('created_at', Carbon::now()->subMonth()->year)
                ->count();

            // Hitung persentase perubahan harian
            $dailyUserGrowth = $yesterdayUsers > 0 ? (($todayUsers - $yesterdayUsers) / $yesterdayUsers) * 100 : 0;
            $dailySubscriptionGrowth = $yesterdaySubscriptions > 0 ? (($todaySubscriptions - $yesterdaySubscriptions) / $yesterdaySubscriptions) * 100 : 0;
            $dailyProductGrowth = $yesterdayProducts > 0 ? (($todayProducts - $yesterdayProducts) / $yesterdayProducts) * 100 : 0;

            // Hitung persentase perubahan bulanan
            $userGrowth = $lastMonthUsers > 0 ? (($currentMonthUsers - $lastMonthUsers) / $lastMonthUsers) * 100 : 0;
            $subscriptionGrowth = $lastMonthSubscriptions > 0 ? (($currentMonthSubscriptions - $lastMonthSubscriptions) / $lastMonthSubscriptions) * 100 : 0;
            $productGrowth = $lastMonthProducts > 0 ? (($currentMonthProducts - $lastMonthProducts) / $lastMonthProducts) * 100 : 0;

            return [
                Stat::make('Total Pengguna', User::count())
                    ->description(abs(round($dailyUserGrowth, 1)) . '% ' . ($dailyUserGrowth >= 0 ? 'peningkatan harian' : 'penurunan harian'))
                    ->descriptionIcon($dailyUserGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                    ->color($dailyUserGrowth >= 0 ? 'success' : 'danger'),
                Stat::make('Total Pendapatan Langganan', 'Rp ' . number_format(SubscriptionPayment::where('status', 'success')->count() * 200000))
                    ->description(abs(round($dailySubscriptionGrowth, 1)) . '% ' . ($dailySubscriptionGrowth >= 0 ? 'peningkatan harian' : 'penurunan harian'))
                    ->descriptionIcon($dailySubscriptionGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                    ->color($dailySubscriptionGrowth >= 0 ? 'success' : 'danger'),
                Stat::make('Total Produk', Product::count())
                    ->description(abs(round($dailyProductGrowth, 1)) . '% ' . ($dailyProductGrowth >= 0 ? 'peningkatan harian' : 'penurunan harian'))
                    ->descriptionIcon($dailyProductGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                    ->color($dailyProductGrowth >= 0 ? 'success' : 'danger'),

            ];
        } else {
            // Data hari ini untuk user
            $todayTransactions = Transaction::where('user_id', Auth::user()->id)
                ->where('status', 'success')
                ->whereDate('created_at', Carbon::today())
                ->count();

            $todayAmount = Transaction::where('user_id', Auth::user()->id)
                ->where('status', 'success')
                ->whereDate('created_at', Carbon::today())
                ->sum('total_price');

            // Data kemarin untuk user
            $yesterdayTransactions = Transaction::where('user_id', Auth::user()->id)
                ->where('status', 'success')
                ->whereDate('created_at', Carbon::yesterday())
                ->count();

            $yesterdayAmount = Transaction::where('user_id', Auth::user()->id)
                ->where('status', 'success')
                ->whereDate('created_at', Carbon::yesterday())
                ->sum('total_price');

            // Data bulan ini untuk user
            $currentMonthTransactions = Transaction::where('user_id', Auth::user()->id)
                ->where('status', 'success')
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count();

            $currentMonthAmount = Transaction::where('user_id', Auth::user()->id)
                ->where('status', 'success')
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->sum('total_price');

            // Data bulan lalu untuk user
            $lastMonthTransactions = Transaction::where('user_id', Auth::user()->id)
                ->where('status', 'success')
                ->whereMonth('created_at', Carbon::now()->subMonth()->month)
                ->whereYear('created_at', Carbon::now()->subMonth()->year)
                ->count();

            $lastMonthAmount = Transaction::where('user_id', Auth::user()->id)
                ->where('status', 'success')
                ->whereMonth('created_at', Carbon::now()->subMonth()->month)
                ->whereYear('created_at', Carbon::now()->subMonth()->year)
                ->sum('total_price');

            // Hitung persentase perubahan harian
            $dailyTransactionGrowth = $yesterdayTransactions > 0 ? (($todayTransactions - $yesterdayTransactions) / $yesterdayTransactions) * 100 : 0;
            $dailyAmountGrowth = $yesterdayAmount > 0 ? (($todayAmount - $yesterdayAmount) / $yesterdayAmount) * 100 : 0;
            
            $todayAverage = $todayTransactions > 0 ? $todayAmount / $todayTransactions : 0;
            $yesterdayAverage = $yesterdayTransactions > 0 ? $yesterdayAmount / $yesterdayTransactions : 0;
            $dailyAverageGrowth = $yesterdayAverage > 0 ? (($todayAverage - $yesterdayAverage) / $yesterdayAverage) * 100 : 0;

            return [
                Stat::make('Total Transaksi', $totalTransaction)
                    ->description(abs(round($dailyTransactionGrowth, 1)) . '% ' . ($dailyTransactionGrowth >= 0 ? 'peningkatan harian' : 'penurunan harian'))
                    ->descriptionIcon($dailyTransactionGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                    ->color($dailyTransactionGrowth >= 0 ? 'success' : 'danger'),

                Stat::make('Total Pendapatan', 'Rp ' . number_format($totalAmount))
                    ->description(abs(round($dailyAmountGrowth, 1)) . '% ' . ($dailyAmountGrowth >= 0 ? 'peningkatan harian' : 'penurunan harian'))
                    ->descriptionIcon($dailyAmountGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                    ->color($dailyAmountGrowth >= 0 ? 'success' : 'danger'),
                Stat::make('Rata-rata Transaksi', 'Rp ' . ($totalTransaction > 0 ? number_format($totalAmount / $totalTransaction) : '0'))
                    ->description(abs(round($dailyAverageGrowth, 1)) . '% ' . ($dailyAverageGrowth >= 0 ? 'peningkatan harian' : 'penurunan harian'))
                    ->descriptionIcon(($dailyAverageGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down'))
                    ->color($dailyAverageGrowth >= 0 ? 'success' : 'danger'),
            ];
        }
    }
}
