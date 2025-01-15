<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeterReading extends Model
{
    protected $fillable = ['meter_id', 'reading_value', 'reading_date'];

    public function meter()
    {
        return $this->belongsTo(Meter::class);
    }
}