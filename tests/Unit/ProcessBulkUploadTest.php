<?php

namespace Tests\Unit;

use App\Jobs\ProcessBulkUpload;
use App\Models\Meter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProcessBulkUploadTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itProcessesBulkUploadAndCreatesMeterReadings()
    {
        // Fake the storage
        Storage::fake('local');

        // Create a meter with a specific MPXN
        $meter = Meter::factory()->create(['mpxn' => '12345678']);
        $filePath = 'private/uploads/readings.csv';
        $invalidLinesFilePath = 'private/uploads/invalid_lines.txt';

        // Create CSV content with valid readings
        $csvContent = "12345678,100,2025-01-01\n12345678,200,2025-02-01";
        Storage::disk('local')->put($filePath, $csvContent);

        // Dispatch the job and ensure it is processed immediately
        ProcessBulkUpload::dispatch(
            storage_path('app/private/' . $filePath),
            storage_path('app/' . $invalidLinesFilePath)
        )->onQueue('default');

        // Process the queue
        $this->artisan('queue:work', ['--once' => true]);

        // Assert that the meter_readings table has the new readings
        $this->assertDatabaseHas('meter_readings', [
            'meter_id'      => $meter->id,
            'reading_value' => 100,
            'reading_date'  => '2025-01-01',
        ]);

        $this->assertDatabaseHas('meter_readings', [
            'meter_id'      => $meter->id,
            'reading_value' => 200,
            'reading_date'  => '2025-02-01',
        ]);

        // Assert that the CSV file has been deleted
        Storage::assertMissing($filePath);
    }

    /** @test */
    public function itHandlesInvalidLinesInBulkUpload()
    {
        // Fake the storage
        Storage::fake('local');

        // Create a meter with a specific MPXN
        $meter = Meter::factory()->create(['mpxn' => '12345678']);
        $filePath = 'private/uploads/readings.csv';
        $invalidLinesFilePath = 'private/uploads/invalid_lines.txt';

        // Create CSV content with an invalid line
        $csvContent = "12345678,100,2025-01-01\ninvalid_line";
        Storage::disk('local')->put($filePath, $csvContent);

        // Dispatch the job and ensure it is processed immediately
        ProcessBulkUpload::dispatch(
            storage_path('app/' . $filePath),
            storage_path('app/' . $invalidLinesFilePath)
        )->onQueue('default');

        // Process the queue
        $this->artisan('queue:work', ['--once' => true]);

        // Assert that the meter_readings table has the valid reading
        $this->assertDatabaseHas('meter_readings', [
            'meter_id'      => $meter->id,
            'reading_value' => 100,
            'reading_date'  => '2025-01-01',
        ]);

        // Assert that the invalid lines file exists and contains the invalid line
        $this->assertFileExists(storage_path('app/' . $invalidLinesFilePath));
        $this->assertStringContainsString('invalid_line', Storage::disk('local')->get($invalidLinesFilePath));
    }
}
