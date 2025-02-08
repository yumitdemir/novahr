<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use App\Filament\Widgets\HiringTrend;
use App\Models\Employee;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewEmployee extends ViewRecord
{
    protected static string $resource = EmployeeResource::class;

    protected function getFooterWidgets(): array
    {
        return [
            EmployeeResource\Widgets\SalaryTrend::class
        ];
    }

}
