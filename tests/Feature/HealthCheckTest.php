<?php

namespace Tests\Feature;

use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    public function test_health_endpoint_returns_ok_status_and_database_info(): void
    {
        $response = $this->getJson('/api/v1/health');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'timestamp',
                'services' => [
                    'database',
                ],
            ])
            ->assertJson([
                'status' => 'ok',
            ]);
    }

    public function test_non_existent_api_route_returns_uniform_json_404(): void
    {
        $response = $this->getJson('/api/v1/ruta-falsa');

        $response->assertStatus(404)
            ->assertJsonStructure([
                'message',
                'errors',
            ]);
    }
}

