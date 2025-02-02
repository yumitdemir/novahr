<?php

namespace App\Services;

use App\Models\JobApplication;
use Filament\Notifications\Notification;
use OpenAI\Laravel\Facades\OpenAI;
use Exception;

class CvGradingService implements CvGradingServiceInterface
{
    public function __construct()
    {
        // Constructor logic if needed
    }

    public function startGrading(JobApplication $record): void
    {

        $jobDescription =  $record->jobOpening->description;
        if ($record->cv === null || $jobDescription === null) {
            return;
        }

        $cvText = $this->extractTextFromCv($record->cv);

        Notification::make()
            ->title('Started Cv Grading for record for application')
            ->send();


        $prompt = $this->getPrompt($cvText, $jobDescription, []);

        try {
            $result = OpenAI::chat()->create([
                'model' => 'gpt-4o',
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.5,
            ]);

            $content = $result['choices'][0]['message']['content'] ?? '';
            $response = $this->extractJsonFromResponse($content);

            $this->updateApplication($record, $response);
        } catch (Exception $e) {
            $this->handleErrorResponse($e->getMessage());
        }
    }
    public function getPrompt(string $cvText, string $jobDescription, array $additionalFields = []): string
    {
        $additionalFieldsJson = json_encode($additionalFields, JSON_PRETTY_PRINT);

        return "You are a career analysis assistant. Given the following CV, job description, and additional fields, please analyze and provide:
        1. The total number of years of relevant experience that match the job requirements.
        2. A general grading (on a scale from 1 to 10) of the CV’s compatibility with the job description.
        3. The following additional fields if they exist in the CV: email, phone, linkedin, location, current job title, current employer, highest degree, university, certifications, technical skills, soft skills, languages spoken.

        Return your answer as a valid JSON object with keys 'years_of_experience', 'compatibility_rating', and the additional fields if they exist.

        For example, your output should look like:
        {
          \"years_of_experience\": 10,
          \"compatibility_rating\": 8,
          \"email\": \"john.doe@example.com\",
          \"phone\": \"123-456-7890\",
          \"linkedin\": \"https://linkedin.com/in/johndoe\",
          \"location\": \"New York, NY\",
          \"current_job_title\": \"Senior Software Engineer\",
          \"current_employer\": \"Tech Corp\",
          \"university\": \"MIT\",
          \"certifications\": \"Certified Scrum Master\",
          \"technical_skills\": \"PHP, JavaScript, Cloud Systems\",
          \"soft_skills\": \"Leadership, Communication\",
          \"languages_spoken\": \"English, Spanish\"
        }

        CV:
        {$cvText}

        Job Description:
        {$jobDescription}

        Additional Fields:
        {$additionalFieldsJson}";
    }

    public function extractJsonFromResponse(string $content): object
    {
        $jsonStart = strpos($content, '{');
        $jsonEnd = strrpos($content, '}');

        if ($jsonStart !== false && $jsonEnd !== false) {
            $jsonString = substr($content, $jsonStart, $jsonEnd - $jsonStart + 1);
            $jsonResult = json_decode($jsonString);

            if (json_last_error() === JSON_ERROR_NONE) {
                return $jsonResult;
            }
        }

        throw new Exception('Unable to parse JSON response');
    }

    public function updateApplication(JobApplication $record, object $response): void
    {
        $record->update([
            'years_of_experience' => $response->years_of_experience,
            'compatibility_rating' => $response->compatibility_rating,
            'email' => $response->email ?? $record->email,
            'phone' => $response->phone ?? $record->phone,
            'linkedin' => $response->linkedin ?? $record->linkedin,
            'location' => $response->location ?? $record->location,
            'current_job_title' => $response->current_job_title ?? $record->current_job_title,
            'current_employer' => $response->current_employer ?? $record->current_employer,
            'university' => $response->university ?? $record->university,
            'certifications' => $response->certifications ?? $record->certifications,
            'technical_skills' => $response->technical_skills ?? $record->technical_skills,
            'soft_skills' => $response->soft_skills ?? $record->soft_skills,
            'languages_spoken' => $response->languages_spoken ?? $record->languages_spoken,
        ]);

        Notification::make()
            ->title('Cv Grading Completed')
            ->success()
            ->body('Compatibility rating: ' . $response->compatibility_rating)
            ->send();
    }

    private function handleErrorResponse(string $errorMessage): void
    {
        Notification::make()
            ->title('Cv Grading Error')
            ->warning()
            ->body($errorMessage)
            ->send();
    }

    public function extractTextFromCv(string $cv): string
    {
        $filePath = storage_path('app/public/'.$cv);

        if (!file_exists($filePath)) {
            throw new Exception('CV file not found.');
        }

        // Read the file content
        $fileContent = file_get_contents($filePath);

        if ($fileContent === false) {
            throw new Exception('Unable to read CV file.');
        }

        // Ensure the content is properly encoded to UTF-8
        $fileContent = mb_convert_encoding($fileContent, 'UTF-8', 'auto');

        return $fileContent;
    }
}
