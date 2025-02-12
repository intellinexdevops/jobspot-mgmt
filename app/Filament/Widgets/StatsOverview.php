<?php

namespace App\Filament\Widgets;


use App\Models\Company;
use App\Models\User;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static bool $isLazy = false;
    protected ?string $heading = 'Analytics';
 
    protected ?string $description = 'An overview of some analytics.';
    protected function getStats(): array
    {
        return [
            Stat::make("Users", User::count() -1)
                ->description('3% Increase')
                ->color('success')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->chartColor('success'),

            Stat::make("Transactions", Transaction::count())
                ->description('3% Increase')
                ->color('success')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->chartColor('success'),

            Stat::make("Companies", Company::count())
                ->description('3% Increase')
                ->color('success')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->chartColor('success'),
        ];
    }
}
