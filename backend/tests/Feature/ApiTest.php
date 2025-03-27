<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use WithoutMiddleware;

    /**
     * Test if the API is working.
     */
    public function test_api_is_working(): void
    {
        $response = $this->get('/api/test');

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Backend API is working!'
                 ]);
    }
}
