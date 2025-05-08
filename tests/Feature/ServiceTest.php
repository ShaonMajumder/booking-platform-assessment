<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Service;

class ServiceTest extends TestCase
{
    public function test_service_listing_returns_paginated_services()
    {
        Service::factory()->count(15)->create();

        $response = $this->getJson('/api/services?page=1&per_page=10');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'current_page',
                     'data',
                     'total',
                     'per_page',
                 ]);
    }
}
