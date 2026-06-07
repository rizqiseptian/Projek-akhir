<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MonthlyRevenueWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $currentMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();

        $currentMonthRevenue = Transaction::where('created_at', '>=', $currentMonth)->sum('total_amount');
        $lastMonthRevenue = Transaction::where('created_at', '>=', $lastMonth)
            ->where('created_at', '<', $currentMonth)
            ->sum('total_amount');

        $currentMonthTransactions = Transaction::where('created_at', '>=', $currentMonth)->count();
        $lastMonthTransactions = Transaction::where('created_at', '>=', $lastMonth)
            ->where('created_at', '<', $currentMonth)
            ->count();

        $revenueChange = $lastMonthRevenue > 0
            ? (($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100
            : 0;

        $transactionChange = $lastMonthTransactions > 0
            ? (($currentMonthTransactions - $lastMonthTransactions) / $lastMonthTransactions) * 100
            : 0;

        return [
            Stat::make('This Month Revenue', '$' . number_format($currentMonthRevenue, 2))
                ->description($revenueChange >= 0 ? "+{$revenueChange}%" : "{$revenueChange}% vs last month")
                ->descriptionIcon($revenueChange >= 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down')
                ->color($revenueChange >= 0 ? 'success' : 'danger'),

            Stat::make('This Month Transactions', $currentMonthTransactions)
                ->description($transactionChange >= 0 ? "+{$transactionChange}%" : "{$transactionChange}% vs last month")
                ->descriptionIcon($transactionChange >= 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down')
                ->color($transactionChange >= 0 ? 'success' : 'danger'),

            Stat::make('Average per Transaction', '$' . number_format($currentMonthTransactions > 0 ? $currentMonthRevenue / $currentMonthTransactions : 0, 2))
                ->description('This month average')
                ->descriptionIcon('heroicon-o-chart-bar')
                ->color('primary'),
        ];
    }
}
