<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Meter;

class MeterControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_show_the_meters_index_page()
    {
        $response = $this->get(route('meters.index'));

        $response->assertStatus(200);
        $response->assertViewIs('meters.index');
    }

    /** @test */
    public function it_can_show_the_create_meter_form()
    {
        $response = $this->get(route('meters.create'));

        $response->assertStatus(200);
        $response->assertViewIs('meters.create');
    }

    /** @test */
    public function it_can_store_a_new_meter()
    {
        $response = $this->post(route('meters.store'), [
            'mpxn' => '123456789', // Ensure this value matches the validation rule for the selected type
            'type' => 'gas',
            'installation_date' => '2025-01-01',
            'estimated_annual_consumption' => 3000, // Ensure this value is between 2000 and 8000
        ]);
    
        $response->assertRedirect(route('meters.index'));
        $response->assertSessionHas('success', 'Meter created successfully.');
    
        $this->assertDatabaseHas('meters', [
            'mpxn' => '123456789',
            'type' => 'gas',
            'installation_date' => '2025-01-01',
            'estimated_annual_consumption' => 3000,
        ]);
    }

    /** @test */
    public function it_can_show_a_meter()
    {
        $meter = Meter::factory()->create();

        $response = $this->get(route('meters.show', $meter));

        $response->assertStatus(200);
        $response->assertViewIs('meters.show');
        $response->assertViewHas('meter', $meter);
    }

    /** @test */
    public function it_can_delete_a_meter()
    {
        $meter = Meter::factory()->create();

        $response = $this->delete(route('meters.destroy', $meter));

        $response->assertRedirect(route('meters.index'));
        $response->assertSessionHas('success', 'Meter deleted successfully.');

        $this->assertDatabaseMissing('meters', [
            'id' => $meter->id,
        ]);
    }
}