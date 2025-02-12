<?php

namespace App\Filament\Resources\TransactionResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Transaction;

class SubscriptionStats extends BaseWidget
{
    protected static bool $isLazy = false;
    protected function getStats(): array
    {
        return [
            Stat::make('Total Incomes', "$ " . number_format((Transaction::where('currency', 'USD')->sum('amount') + Transaction::where('currency', 'KHR')->sum('amount') / 4100), 2))
                ->description('32k increase')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),
            Stat::make('Incomes in KHR', "៛ " . number_format(Transaction::where('currency', 'KHR')->sum('amount'), 2))
                ->description('32k increase')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),
            Stat::make('Incomes in USD', "$ " . number_format(Transaction::where('currency', 'USD')->sum('amount'), 2))
                ->description('32k increase')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),
        ];
    }
}

