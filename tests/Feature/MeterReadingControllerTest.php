<?php

namespace Tests\Feature;

use App\Jobs\ProcessBulkUpload;
use App\Models\Meter;
use App\Models\MeterReading;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MeterReadingControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanShowTheBulkUploadForm()
    {
        // Send a GET request to the bulk upload form route
        $response = $this->get(route('meters.bulkUploadForm'));

        // Assert that the response status is 200 (OK)
        $response->assertStatus(200);
        // Assert that the view returned is 'meters.bulk-upload'
        $response->assertViewIs('meters.bulk-upload');
    }

    /** @test */
    public function itCanUploadACsvFileAndDispatchTheJob()
    {
        // Fake the storage and queue
        Storage::fake('local');
        Queue::fake();

        // Create a fake CSV file
        $file = UploadedFile::fake()->create('readings.csv', 100, 'text/csv');

        // Send a POST request to upload the CSV file
        $response = $this->post(route('meters.bulkUpload'), [
            'csv_file' => $file,
        ]);

        // Assert that the response redirects to the bulk upload form route
        $response->assertRedirect(route('meters.bulkUploadForm'));
        // Assert that the session has a success message
        $response->assertSessionHas('success', 'File has been processed successfully.');

        // Assert that the ProcessBulkUpload job was pushed to the queue
        Queue::assertPushed(ProcessBulkUpload::class, function ($job) use ($file) {
            return $job->getFilePath() === storage_path('app/private/uploads/' . $file->hashName());
        });
    }

    /** @test */
    public function itCanShowInvalidLinesOnTheBulkUploadForm()
    {
        // Set invalid lines in the session
        $invalidLines = "Invalid line 1\nInvalid line 2";
        session()->flash('invalid_lines', $invalidLines);

        // Send a GET request to the bulk upload form route
        $response = $this->get(route('meters.bulkUploadForm'));

        // Assert that the response status is 200 (OK)
        $response->assertStatus(200);
        // Assert that the view returned is 'meters.bulk-upload'
        $response->assertViewIs('meters.bulk-upload');
        // Assert that the view contains the invalid lines
        $response->assertSee('Invalid Readings:');
        $response->assertSee('Invalid line 1');
        $response->assertSee('Invalid line 2');
    }

    /** @test */
    public function itCanStoreAMeterReading()
    {
        // Create a new meter using the factory
        $meter = Meter::factory()->create();

        // Send a POST request to store a new meter reading with valid data
        $response = $this->post(route('meter.readings.store', $meter), [
            'reading_value' => 100,
            'reading_date'  => '2025-01-01',
        ]);

        // Assert that the response redirects to the meter show route
        $response->assertRedirect(route('meters.show', $meter));
        // Assert that the session has a success message
        $response->assertSessionHas('success', 'Reading added successfully.');

        // Assert that the meter_readings table has the new reading data
        $this->assertDatabaseHas('meter_readings', [
            'meter_id'      => $meter->id,
            'reading_value' => 100,
            'reading_date'  => '2025-01-01',
        ]);
    }

    /** @test */
    public function itCanEstimateAMeterReading()
    {
        // Create a new meter and a reading using the factory
        $meter = Meter::factory()->create();
        MeterReading::factory()->create([
            'meter_id'      => $meter->id,
            'reading_value' => 100,
            'reading_date'  => '2025-01-01',
        ]);

        // Set the estimate date to one day before today
        $estimateDate = Carbon::now()->subDay()->format('Y-m-d');

        // Send a POST request to estimate a meter reading
        $response = $this->post(route('meter.readings.estimate', $meter), [
            'estimate_date' => $estimateDate,
        ]);

        // Assert that the response redirects to the meter show route
        $response->assertRedirect(route('meters.show', $meter));
        // Assert that the session has a success message
        $response->assertSessionHas('success', 'Estimated reading generated successfully.');

        // Assert that the meter_readings table has the estimated reading data
        $this->assertDatabaseHas('meter_readings', [
            'meter_id'     => $meter->id,
            'reading_date' => $estimateDate,
        ]);
    }

    /** @test */
    public function itCanDeleteAMeterReading()
    {
        // Create a new meter and a reading using the factory
        $meter = Meter::factory()->create();
        $reading = MeterReading::factory()->create(['meter_id' => $meter->id]);

        // Send a DELETE request to delete the meter reading
        $response = $this->delete(route('meter.readings.destroy', [$meter, $reading]));

        // Assert that the response redirects to the meter show route
        $response->assertRedirect(route('meters.show', $meter));
        // Assert that the session has a success message
        $response->assertSessionHas('success', 'Reading deleted successfully.');

        // Assert that the meter_readings table does not have the deleted reading data
        $this->assertDatabaseMissing('meter_readings', [
            'id' => $reading->id,
        ]);
    }
}
