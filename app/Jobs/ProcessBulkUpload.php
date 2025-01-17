<?php
namespace App\Jobs;

use App\Models\Meter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ProcessBulkUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;
    protected $invalidLinesFilePath;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($filePath, $invalidLinesFilePath)
    {
        $this->filePath = $filePath;
        $this->invalidLinesFilePath = $invalidLinesFilePath;
    }

    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $invalidLines = [];

        Log::info('Processing bulk upload', ['filePath' => $this->filePath]);

        try {
            if (file_exists($this->filePath)) {
                Log::info('File exists', ['filePath' => $this->filePath]);
                $file = file_get_contents($this->filePath);
                $lines = explode(PHP_EOL, $file);

                foreach ($lines as $line) {
                    Log::info('Processing line', ['line' => $line]);
                    $data = str_getcsv($line);
                    if (count($data) < 3) {
                        $reason = 'Invalid line format';
                        Log::warning($reason, ['line' => $line]);
                        $invalidLines[] = $line . ' - ' . $reason;
                        continue;
                    }

                    $meterIdentifier = $data[0];
                    $readingValue = $data[1];
                    $readingDate = $data[2];

                    // Validate date
                    if (!strtotime($readingDate) || Carbon::parse($readingDate)->isAfter(Carbon::today())) {
                        $reason = 'Invalid date';
                        Log::warning($reason, ['readingDate' => $readingDate]);
                        $invalidLines[] = $line . ' - ' . $reason;
                        continue;
                    }

                    // Validate reading value
                    if (!is_numeric($readingValue)) {
                        $reason = 'Invalid reading value';
                        Log::warning($reason, ['readingValue' => $readingValue]);
                        $invalidLines[] = $line . ' - ' . $reason;
                        continue;
                    }

                    $meter = Meter::where('mpxn', $meterIdentifier)->first();

                    if ($meter) {
                        // Calculate the expected reading based on EAC and the specific day of the year
                        $installationDate = Carbon::parse($meter->installation_date);
                        $readingDateCarbon = Carbon::parse($readingDate);
                        $daysSinceInstallation = $installationDate->diffInDays($readingDateCarbon);

                        if ($daysSinceInstallation < 0) {
                            $reason = 'Reading date is before installation date';
                            Log::warning($reason, ['readingDate' => $readingDate, 'installationDate' => $meter->installation_date]);
                            $invalidLines[] = $line . ' - ' . $reason;
                            continue;
                        }

                        $dailyConsumption = $meter->estimated_annual_consumption / 365;
                        $expectedReading = $dailyConsumption * $daysSinceInstallation;

                        $minAcceptable = $expectedReading * 0.75;
                        $maxAcceptable = $expectedReading * 1.25;

                        if ($readingValue < $minAcceptable || $readingValue > $maxAcceptable) {
                            $reason = 'Reading value out of acceptable range';
                            Log::warning($reason, ['readingValue' => $readingValue, 'minAcceptable' => $minAcceptable, 'maxAcceptable' => $maxAcceptable]);
                            $invalidLines[] = $line . ' - ' . $reason;
                            continue;
                        }

                        Log::info('Adding reading', ['meterIdentifier' => $meterIdentifier, 'readingValue' => $readingValue, 'readingDate' => $readingDate, 'minAcceptable' => $minAcceptable, 'maxAcceptable' => $maxAcceptable]);
                        $meter->readings()->create([
                            'reading_value' => $readingValue,
                            'reading_date' => $readingDate,
                        ]);
                    } else {
                        $reason = 'Meter not found';
                        Log::warning($reason, ['meterIdentifier' => $meterIdentifier]);
                        $invalidLines[] = $line . ' - ' . $reason;
                    }
                }
            } else {
                Log::error('Failed to open file', ['filePath' => $this->filePath]);
            }

            // Store invalid lines in a temporary file
            if (!empty($invalidLines)) {
                file_put_contents($this->invalidLinesFilePath, implode("\n", $invalidLines));
            }

            // Delete the file after processing
            Log::info('Deleting file', ['filePath' => $this->filePath]);
            unlink($this->filePath);

            // Log job completion
            Log::info('Bulk upload job completed', ['filePath' => $this->filePath]);
        } catch (\Exception $e) {
            Log::error('Error processing bulk upload', ['message' => $e->getMessage(), 'filePath' => $this->filePath]);
        }
    }
}