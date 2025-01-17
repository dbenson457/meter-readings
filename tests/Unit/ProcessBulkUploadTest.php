<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Queue;
use App\Jobs\ProcessBulkUpload;
use App\Models\Meter;
use App\Models\MeterReading;
use Illuminate\Support\Facades\Log;

class ProcessBulkUploadTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_processes_bulk_upload_and_creates_meter_readings()
    {
        Storage::fake('local');

        $meter = Meter::factory()->create(['mpxn' => '12345678']);
        $filePath = 'private/uploads/readings.csv';
        $invalidLinesFilePath = 'private/uploads/invalid_lines.txt';

        $csvContent = "12345678,100,2025-01-01\n12345678,200,2025-02-01";
        Storage::disk('local')->put($filePath, $csvContent);

        // Dispatch the job and ensure it is processed immediately
        ProcessBulkUpload::dispatch(storage_path('app/private/' . $filePath), storage_path('app/' . $invalidLinesFilePath))->onQueue('default');

        // Process the queue
        $this->artisan('queue:work', ['--once' => true]);

        $this->assertDatabaseHas('meter_readings', [
            'meter_id' => $meter->id,
            'reading_value' => 100,
            'reading_date' => '2025-01-01',
        ]);

        $this->assertDatabaseHas('meter_readings', [
            'meter_id' => $meter->id,
            'reading_value' => 200,
            'reading_date' => '2025-02-01',
        ]);

        Storage::assertMissing($filePath);
    }

    /** @test */
    public function it_handles_invalid_lines_in_bulk_upload()
    {
        Storage::fake('local');

        $meter = Meter::factory()->create(['mpxn' => '12345678']);
        $filePath = 'private/uploads/readings.csv';
        $invalidLinesFilePath = 'private/uploads/invalid_lines.txt';

        $csvContent = "12345678,100,2025-01-01\ninvalid_line";
        Storage::disk('local')->put($filePath, $csvContent);

        // Dispatch the job and ensure it is processed immediately
        ProcessBulkUpload::dispatch(storage_path('app/' . $filePath), storage_path('app/' . $invalidLinesFilePath))->onQueue('default');

        // Process the queue
        $this->artisan('queue:work', ['--once' => true]);

        $this->assertDatabaseHas('meter_readings', [
            'meter_id' => $meter->id,
            'reading_value' => 100,
            'reading_date' => '2025-01-01',
        ]);

        $this->assertFileExists(storage_path('app/' . $invalidLinesFilePath));
        $this->assertStringContainsString('invalid_line', Storage::disk('local')->get($invalidLinesFilePath));
    }
}