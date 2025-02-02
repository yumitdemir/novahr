<?php

namespace App\Services;

use App\Models\JobApplication;

interface CvGradingServiceInterface
{
    public function startGrading(JobApplication $record): void;

    public function getPrompt(string $cvText, string $jobDescription): string;

    public function extractJsonFromResponse(string $content): object;

    public function updateApplication(JobApplication $record, object $response): void;

    public function extractTextFromCv(string $cv): string;
}
