<?php

namespace Database\Factories;

use App\Models\MeterReading;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class MeterReadingFactory extends Factory
{
    protected $model = MeterReading::class;

    public function definition()
    {
        return [
            'meter_id' => \App\Models\Meter::factory(),
            'reading_value' => $this->faker->numberBetween(100, 10000),
            'reading_date' => Carbon::now()->subDays(rand(1, 365)),
        ];
    }
}