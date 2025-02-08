<?php

namespace App\Filament\Resources\EmployeeResource\Widgets;

use App\Models\Employee;
use App\Models\EmployeeChangeLog;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;

class SalaryTrend extends ChartWidget
{

    use HasWidgetShield;

    protected static ?string $heading = 'Salary Trend';

    public ?Employee $record;


    protected function getData(): array
    {
        $changeLogs = EmployeeChangeLog::query()
            ->where('employee_id', $this->record->id)
            ->where('change_type', 'salary')
            ->orderBy('changed_at')
            ->get(['changed_at', 'new_value']);

        return [
            'labels' => $changeLogs->pluck('changed_at'),
            'datasets' => [
                [
                    'label' => $this->record->name,
                    'data' => $changeLogs->pluck('new_value'),
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
