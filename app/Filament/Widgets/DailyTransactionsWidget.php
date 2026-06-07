<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class DailyTransactionsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $today = now()->startOfDay();
        $yesterday = now()->subDay()->startOfDay();

        $todayTransactions = Transaction::where('created_at', '>=', $today)->count();
        $yesterdayTransactions = Transaction::where('created_at', '>=', $yesterday)
            ->where('created_at', '<', $today)
            ->count();

        $todayRevenue = Transaction::where('created_at', '>=', $today)->sum('total_amount');
        $yesterdayRevenue = Transaction::where('created_at', '>=', $yesterday)
            ->where('created_at', '<', $today)
            ->sum('total_amount');

        $transactionChange = $yesterdayTransactions > 0
            ? (($todayTransactions - $yesterdayTransactions) / $yesterdayTransactions) * 100
            : 0;

        $revenueChange = $yesterdayRevenue > 0
            ? (($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100
            : 0;

        return [
            Stat::make('Today\'s Transactions', $todayTransactions)
                ->description($transactionChange >= 0 ? "+{$transactionChange}%" : "{$transactionChange}%")
                ->descriptionIcon($transactionChange >= 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down')
                ->color($transactionChange >= 0 ? 'success' : 'danger'),

            Stat::make('Today\'s Revenue', '$' . number_format($todayRevenue, 2))
                ->description($revenueChange >= 0 ? "+{$revenueChange}%" : "{$revenueChange}%")
                ->descriptionIcon($revenueChange >= 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down')
                ->color($revenueChange >= 0 ? 'success' : 'danger'),

            Stat::make('Average Transaction', '$' . number_format($todayTransactions > 0 ? $todayRevenue / $todayTransactions : 0, 2))
                ->description('Per transaction today')
                ->descriptionIcon('heroicon-o-calculator')
                ->color('primary'),
        ];
    }
}
