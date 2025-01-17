<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meter extends Model
{
    use HasFactory;

    // Define the fillable attributes for mass assignment
    protected $fillable = ['mpxn', 'installation_date', 'type', 'estimated_annual_consumption'];

    // Define the relationship with the MeterReading model
    public function readings()
    {
        // A meter can have many readings
        return $this->hasMany(MeterReading::class);
    }

    // Calculate the estimated reading based on previous reading and dates
    public function calculateEstimatedReading($previousReading, $previousDate, $estimateDate)
    {
        // Calculate the number of days between the previous reading date and the estimate date
        $daysBetween = Carbon::parse($previousDate)->diffInDays(Carbon::parse($estimateDate));

        // Calculate the daily consumption based on the estimated annual consumption
        $dailyConsumption = $this->estimated_annual_consumption / 365;

        // Calculate the estimated reading by adding the daily consumption over the days between the readings
        $estimatedReading = $previousReading + ($dailyConsumption * $daysBetween);

        // Return the estimated reading rounded to the nearest integer
        return round($estimatedReading);
    }
}
