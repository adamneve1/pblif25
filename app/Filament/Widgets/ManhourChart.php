<?php
namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\Manhour;
use Carbon\Carbon;

class ManhourChart extends ChartWidget
{
    protected static ?string $heading = 'Manhour';

    protected function getData(): array
    {
        $dataLangsung = Trend::model(Manhour::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->sum('jumlah_tenaga_langsung');

        $dataTidakLangsung = Trend::model(Manhour::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->sum('jumlah_tenaga_tidak_langsung');

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Tenaga Langsung:',
                    'data' => $dataLangsung->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => '#4CAF50',
                ],
                [
                    'label' => 'Jumlah Tenaga Tidak Langsung',
                    'data' => $dataTidakLangsung->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => '#FF5722',
                ],
            ],
            'labels' => $dataLangsung->map(fn (TrendValue $value) => Carbon::parse($value->date)->format('F')),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
