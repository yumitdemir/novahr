<?php

namespace App\Filament\Resources\JobApplicationResource\Pages;

use App\Filament\Resources\JobApplicationResource;
use App\Services\CvGradingServiceInterface;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateJobApplication extends CreateRecord
{
    protected static string $resource = JobApplicationResource::class;

    protected CvGradingServiceInterface $cvGradingService;
    public function __construct($id = null)
    {
        $this->cvGradingService = app(CvGradingServiceInterface::class);
    }

    protected function afterCreate(): void
    {
        $this->cvGradingService->startGrading($this->record);
    }
}
