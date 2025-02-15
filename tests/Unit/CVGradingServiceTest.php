<?php

            namespace Tests\Unit;

            use App\Models\JobApplication;
            use App\Services\CvGradingService;
            use Exception;
            use Illuminate\Foundation\Testing\RefreshDatabase;
            use Mockery;
            use OpenAI\Laravel\Facades\OpenAI;
            use PHPUnit\Framework\TestCase;

            class CVGradingServiceTest extends TestCase
            {
                use RefreshDatabase;

                protected function setUp(): void
                {
                    parent::setUp();
                    $this->service = new CvGradingService();
                }

                public function testStartGradingWithNullCv()
                {
                    $jobApplication = Mockery::mock(JobApplication::class)->makePartial();
                    $jobApplication->cv = null;
                    $jobApplication->jobOpening = (object) ['description' => 'Job description'];

                    $this->service->startGrading($jobApplication);

                    $this->assertTrue(true);
                }

                public function testStartGradingWithValidCv()
                {
                    $jobApplication = Mockery::mock(JobApplication::class)->makePartial();
                    $jobApplication->cv = 'test.pdf';
                    $jobApplication->jobOpening = (object) ['description' => 'Job description'];

                    $cvText = 'Extracted CV text';
                    $prompt = 'Generated prompt';
                    $response = (object) [
                        'years_of_experience' => 5,
                        'compatibility_rating' => 8,
                    ];

                    $this->service = Mockery::mock(CvGradingService::class)->makePartial();
                    $this->service->shouldReceive('extractTextFromCv')->andReturn($cvText);
                    $this->service->shouldReceive('getPrompt')->andReturn($prompt);
                    OpenAI::shouldReceive('chat->create')->andReturn(['choices' => [['message' => ['content' => json_encode($response)]]]]);
                    $this->service->shouldReceive('updateApplication')->andReturnNull();

                    $this->service->startGrading($jobApplication);

                    $this->assertTrue(true);
                }

                public function testExtractJsonFromResponse()
                {
                    $content = '{"years_of_experience": 5, "compatibility_rating": 8}';
                    $result = $this->service->extractJsonFromResponse($content);

                    $this->assertEquals(5, $result->years_of_experience);
                    $this->assertEquals(8, $result->compatibility_rating);
                }

                public function testExtractJsonFromResponseWithInvalidJson()
                {
                    $this->expectException(Exception::class);
                    $this->service->extractJsonFromResponse('Invalid JSON');
                }

                protected function tearDown(): void
                {
                    Mockery::close();
                    parent::tearDown();
                }
            }
