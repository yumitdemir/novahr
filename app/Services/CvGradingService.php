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
    public function getPrompt(string $cvText, string $jobDescription): string
    {

        return <<<EOT
            You are a career analysis assistant. You are provided with a CV and a job description. Your task is to objectively analyze the provided documents, focusing solely on career-related information. Do not consider or include any irrelevant details such as personal attributes or non-career-related information.

            Important:
            Do not provide any text or explanation other than the JSON structure specified below.

            Steps to Follow:

            Experience Analysis:
            Determine the total number of years of relevant work experience from the CV that directly match the job requirements.

            Compatibility Rating:
            Provide a general compatibility rating of the CV relative to the job description on a scale from 1 (least compatible) to 10 (most compatible).

            Field Extraction:
            Extract the following fields from the CV if they exist:
            - email
            - phone
            - linkedin
            - location
            - current job title
            - current employer
            - highest degree
            - university
            - certifications
            - technical skills
            - soft skills
            - languages spoken

            Output Format:
            Return your answer as a valid JSON object with keys:
            - "years_of_experience"
            - "compatibility_rating"
            - "compatibility_rating_reason"
            - "short_summary"
            and the additional fields if they exist in the CV.
            Only include keys for the additional fields if the information is present in the CV.

            Example Output:
            {
              "years_of_experience": 10,
              "compatibility_rating": 8,
              "email": "john.doe@example.com",
              "phone": "123-456-7890",
              "linkedin": "https://linkedin.com/in/johndoe",
              "location": "New York, NY",
              "current_job_title": "Senior Software Engineer",
              "current_employer": "Tech Corp",
              "highest_degree": "Bachelor's in Computer Science",
              "university": "MIT",
              "certifications": "Certified Scrum Master",
              "technical_skills": "PHP, JavaScript, Cloud Systems",
              "soft_skills": "Leadership, Communication",
              "languages_spoken": "English, Spanish",
              "compatibility_rating_reason": "The candidate has extensive experience in software engineering and possesses the required technical skills. However, the candidate lacks experience in cloud systems, which is a key requirement for the job.",
              "short_summary": "John Doe is a Senior Software Engineer with 10 years of experience. He holds a Bachelor's in Computer Science from MIT and is certified as a Scrum Master. He has expertise in PHP, JavaScript, and leadership skills. He is fluent in English and Spanish."
            }

            Input Documents:
            CV:
            {$cvText}

            Job Description:
            {$jobDescription}
EOT;
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
            'compatibility_rating_reason' => $response->compatibility_rating_reason ?? $record->compatibility_rating_reason,
            'short_summary' => $response->short_summary ?? $record->short_summary,
        ]);
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
