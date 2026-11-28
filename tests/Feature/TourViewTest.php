<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Tour;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TourViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_page_displays_tours(): void
    {
        Tour::factory()->create([
            'title' => 'Унікальний Тестовий Тур',
            'destination_country' => 'Італія',
            'start_date' => '2026-07-10',
            'duration_days' => 7,
            'base_price' => 15000,
        ]);

        $response = $this->get('/tours');

        $response->assertStatus(200)
            ->assertSee('Унікальний Тестовий Тур')
            ->assertSee('+ Додати новий тур');
    }

    public function test_create_form_validates_input(): void
    {
        $response = $this->post('/tours', [
            'title' => '',
            'base_price' => 'invalid',
        ]);

        $response->assertSessionHasErrors([
            'title',
            'destination_country',
            'start_date',
            'duration_days',
            'base_price',
        ]);
    }
}