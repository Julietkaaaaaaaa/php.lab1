<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TourAccessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Негативний сценарій
     */
    public function test_request_without_manager_role_is_blocked(): void
    {
        $response = $this->postJson('/tours', [
            'title' => 'Вікенд у Львові',
            'destination_country' => 'Україна',
            'start_date' => '2026-09-10',
            'duration_days' => 3,
            'base_price' => 5500,
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'status' => 'access_denied',
            ]);
    }

    /**
     * Позитивний сценарій
     */
    public function test_request_with_manager_role_passes_middleware(): void
    {
        $response = $this->withHeaders([
            'X-User-Role' => 'admin',
        ])->postJson('/tours', [
            'title' => 'Вікенд у Львові',
            'destination_country' => 'Україна',
            'start_date' => '2026-09-10',
            'duration_days' => 3,
            'base_price' => 5500,
        ]);

        $response->assertStatus(201)
            ->assertHeader('X-Security-Policy', 'Enforced');
    }
}
