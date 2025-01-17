<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Queue;
use App\Models\Meter;
use App\Models\MeterReading;
use App\Jobs\ProcessBulkUpload;
use Carbon\Carbon;

class MeterReadingControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_show_the_bulk_upload_form()
    {
        $response = $this->get(route('meters.bulkUploadForm'));

        $response->assertStatus(200);
        $response->assertViewIs('meters.bulk-upload');
    }

    /** @test */
    public function it_can_upload_a_csv_file_and_dispatch_the_job()
    {
        Storage::fake('local');
        Queue::fake();

        $file = UploadedFile::fake()->create('readings.csv', 100, 'text/csv');

        $response = $this->post(route('meters.bulkUpload'), [
            'csv_file' => $file,
        ]);

        $response->assertRedirect(route('meters.bulkUploadForm'));
        $response->assertSessionHas('success', 'File has been processed successfully.');

        Queue::assertPushed(ProcessBulkUpload::class, function ($job) use ($file) {
            return $job->getFilePath() === storage_path('app/private/uploads/' . $file->hashName());
        });
    }

    /** @test */
    public function it_can_show_invalid_lines_on_the_bulk_upload_form()
    {
        $invalidLines = "Invalid line 1\nInvalid line 2";

        session()->flash('invalid_lines', $invalidLines);

        $response = $this->get(route('meters.bulkUploadForm'));

        $response->assertStatus(200);
        $response->assertViewIs('meters.bulk-upload');
        $response->assertSee('Invalid Readings:');
        $response->assertSee('Invalid line 1');
        $response->assertSee('Invalid line 2');
    }

    /** @test */
    public function it_can_store_a_meter_reading()
    {
        $meter = Meter::factory()->create();

        $response = $this->post(route('meter.readings.store', $meter), [
            'reading_value' => 100,
            'reading_date' => '2025-01-01',
        ]);

        $response->assertRedirect(route('meters.show', $meter));
        $response->assertSessionHas('success', 'Reading added successfully.');

        $this->assertDatabaseHas('meter_readings', [
            'meter_id' => $meter->id,
            'reading_value' => 100,
            'reading_date' => '2025-01-01',
        ]);
    }

    /** @test */
    public function it_can_estimate_a_meter_reading()
    {
        $meter = Meter::factory()->create();
        MeterReading::factory()->create([
            'meter_id' => $meter->id,
            'reading_value' => 100,
            'reading_date' => '2025-01-01',
        ]);

        $estimateDate = Carbon::now()->subDay()->format('Y-m-d');

        $response = $this->post(route('meter.readings.estimate', $meter), [
            'estimate_date' => $estimateDate,
        ]);

        $response->assertRedirect(route('meters.show', $meter));
        $response->assertSessionHas('success', 'Estimated reading generated successfully.');

        $this->assertDatabaseHas('meter_readings', [
            'meter_id' => $meter->id,
            'reading_date' => $estimateDate,
        ]);
    }

    /** @test */
    public function it_can_delete_a_meter_reading()
    {
        $meter = Meter::factory()->create();
        $reading = MeterReading::factory()->create(['meter_id' => $meter->id]);

        $response = $this->delete(route('meter.readings.destroy', [$meter, $reading]));

        $response->assertRedirect(route('meters.show', $meter));
        $response->assertSessionHas('success', 'Reading deleted successfully.');

        $this->assertDatabaseMissing('meter_readings', [
            'id' => $reading->id,
        ]);
    }
}