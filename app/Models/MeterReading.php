<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MeterReading extends Model
{
    protected $fillable = ['meter_id', 'reading_value', 'reading_date'];
    use HasFactory;

    public function meter()
    {
        return $this->belongsTo(Meter::class);
    }
}