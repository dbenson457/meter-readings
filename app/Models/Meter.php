<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meter extends Model
{
    protected $fillable = ['mpxn', 'installation_date', 'type'];

    public function readings()
    {
        return $this->hasMany(MeterReading::class);
    }
}