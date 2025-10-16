<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class UsersChart extends ChartWidget
{
    protected static ?string $heading = 'Chart';
    protected int|string|array $columnSpan = 'full';
    /* protected ?string $pollingInterval = '10s'; */

    /* protected function getFilters(): ?array */
    /* { */
    /* return [ */
    /* 'today' => 'Today', */
    /* 'week' => 'Last week', */
    /* 'month' => 'Last month', */
    /* 'year' => 'This year', */
    /* ]; */
    /* } */

    /**/
    protected function getData(): array
    {
        $polri = Trend::query(User::where('programme', '=', 'polri'))
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();
        $tni = Trend::query(User::where('programme', '=', 'tni'))
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();

        $kedinasan = Trend::query(User::where('programme', '=', 'kedinasan'))
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Polisi',
                    'data' => $polri->map(fn(TrendValue $value) => $value->aggregate),
                    'backgroundColor' => '#8cd1ff',
                    'borderColor' => '#8cd1ff',
                ],
                [
                    'label' => 'TNI',
                    'data' => $tni->map(fn(TrendValue $value) => $value->aggregate),
                    'backgroundColor' => '#f7d17e',
                    'borderColor' => '#f7d17e',
                ],
                [
                    'label' => 'Kedinasan',
                    'data' => $kedinasan->map(fn(TrendValue $value) => $value->aggregate),
                    'backgroundColor' => '#a7fac2',
                    'borderColor' => '#a7fac2',
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
