<?php

namespace App\Filament\Resources\EmployeeResource\Widgets;

use App\Models\Employee;
use App\Models\EmployeeChangeLog;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SalaryTrend extends BaseWidget
{

    use HasWidgetShield;


    public ?Employee $record;


protected function getStats(): array
{
    $changeLogs = EmployeeChangeLog::query()
        ->where('employee_id', $this->record->id)
        ->where('change_type', 'salary')
        ->orderBy('changed_at')
        ->get(['changed_at', 'old_value', 'new_value']);

    if ($changeLogs->isNotEmpty()) {
        $oldestChange = $changeLogs->first();
        $newestChange = $changeLogs->last();

        $oldValue = $oldestChange->old_value;
        $newValue = $newestChange->new_value;

        if ($oldValue != 0) {
            $percentageChange = number_format((($newValue - $oldValue) / $oldValue) * 100, 2);
        } else {
            $percentageChange = 0;
        }

        $lastChangedAt = $newestChange->changed_at->format(' Y.m.d -  H:i ');
    } else {
        $percentageChange = 0;
        $lastChangedAt = 'N/A';
    }

    return [
        Stat::make('Salary Increase', $percentageChange . '%'),
        Stat::make('Salary Updated At', $lastChangedAt),
    ];
}


}
