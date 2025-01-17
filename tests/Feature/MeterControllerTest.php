<?php

namespace Tests\Feature;

use App\Models\Meter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MeterControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanShowTheMetersIndexPage()
    {
        // Send a GET request to the meters index route
        $response = $this->get(route('meters.index'));

        // Assert that the response status is 200 (OK)
        $response->assertStatus(200);
        // Assert that the view returned is 'meters.index'
        $response->assertViewIs('meters.index');
    }

    /** @test */
    public function itCanShowTheCreateMeterForm()
    {
        // Send a GET request to the create meter form route
        $response = $this->get(route('meters.create'));

        // Assert that the response status is 200 (OK)
        $response->assertStatus(200);
        // Assert that the view returned is 'meters.create'
        $response->assertViewIs('meters.create');
    }

    /** @test */
    public function itCanStoreANewMeter()
    {
        // Send a POST request to store a new meter with valid data
        $response = $this->post(route('meters.store'), [
            'mpxn' => '123456789', // Ensure this value matches the validation rule for the selected type
            'type' => 'gas',
            'installation_date' => '2025-01-01',
            'estimated_annual_consumption' => 3000, // Ensure this value is between 2000 and 8000
        ]);

        // Assert that the response redirects to the meters index route
        $response->assertRedirect(route('meters.index'));
        // Assert that the session has a success message
        $response->assertSessionHas('success', 'Meter created successfully.');

        // Assert that the meters table has the new meter data
        $this->assertDatabaseHas('meters', [
            'mpxn' => '123456789',
            'type' => 'gas',
            'installation_date' => '2025-01-01',
            'estimated_annual_consumption' => 3000,
        ]);
    }

    /** @test */
    public function itCanShowAMeter()
    {
        // Create a new meter using the factory
        $meter = Meter::factory()->create();

        // Send a GET request to show the meter
        $response = $this->get(route('meters.show', $meter));

        // Assert that the response status is 200 (OK)
        $response->assertStatus(200);
        // Assert that the view returned is 'meters.show'
        $response->assertViewIs('meters.show');
        // Assert that the view has the meter data
        $response->assertViewHas('meter', $meter);
    }

    /** @test */
    public function itCanDeleteAMeter()
    {
        // Create a new meter using the factory
        $meter = Meter::factory()->create();

        // Send a DELETE request to delete the meter
        $response = $this->delete(route('meters.destroy', $meter));

        // Assert that the response redirects to the meters index route
        $response->assertRedirect(route('meters.index'));
        // Assert that the session has a success message
        $response->assertSessionHas('success', 'Meter deleted successfully.');

        // Assert that the meters table does not have the deleted meter data
        $this->assertDatabaseMissing('meters', [
            'id' => $meter->id,
        ]);
    }
}
