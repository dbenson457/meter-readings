<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeterReading extends Model
{
    use HasFactory;

    // Define the fillable attributes for mass assignment
    protected $fillable = ['meter_id', 'reading_value', 'reading_date'];

    // Define the relationship with the Meter model
    public function meter()
    {
        // A meter reading belongs to a meter
        return $this->belongsTo(Meter::class);
    }
}
