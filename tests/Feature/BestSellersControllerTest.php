<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BestSellersControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        $this->jsonResponse = file_get_contents(base_path('tests/Feature/Data/response.json'));
    }

    public function test_successful_response_without_cache()
    {
        Http::fake([
            'https://api.nytimes.com/*' => Http::response(json_decode($this->jsonResponse, true), 200),
        ]);

        $response = $this->getJson('/api/nyt/v1/best-sellers');

        $response->assertStatus(200)
            ->assertJson(json_decode($this->jsonResponse, true), 200);
    }

    public function test_successful_response_with_cache()
    {
        Cache::shouldReceive('remember')
            ->once()
            ->andReturn(json_decode($this->jsonResponse, true));

        $response = $this->getJson('/api/nyt/v1/best-sellers');

        $response->assertStatus(200)
            ->assertJson(json_decode($this->jsonResponse, true));
    }

    public function test_api_error_response()
    {
        Http::fake([
            'https://api.nytimes.com/*' => Http::response('Something went wrong', 500),
        ]);

        $response = $this->getJson('/api/nyt/v1/best-sellers');

        $response->assertStatus(500)
            ->assertJson([
                "error" => "Failed to fetch data from NYT API.",
                "message" => "HTTP request returned status code 500:\nSomething went wrong\n",
            ]);
    }

    public function test_validation_error_response()
    {
        $response = $this->getJson('/api/nyt/v1/best-sellers?isbn[]=invalid_isbn&offset=15');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['isbn.0', 'offset']);
    }


}
