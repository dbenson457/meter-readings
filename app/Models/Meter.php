<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Meter extends Model
{
    protected $fillable = ['mpxn', 'installation_date', 'type', 'estimated_annual_consumption'];

    public function readings()
    {
        return $this->hasMany(MeterReading::class);
    }

    public function calculateEstimatedReading($previousReading, $previousDate, $estimateDate)
    {
        $daysBetween = Carbon::parse($previousDate)->diffInDays(Carbon::parse($estimateDate));
        $dailyConsumption = $this->estimated_annual_consumption / 365;
        $estimatedReading = $previousReading + ($dailyConsumption * $daysBetween);

        return round($estimatedReading);
    }
}