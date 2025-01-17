<?php

namespace Database\Factories;

use App\Models\MeterReading;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class MeterReadingFactory extends Factory
{
    // Specify the model that this factory is for
    protected $model = MeterReading::class;

    // Define the default state for the model
    public function definition()
    {
        return [
            // Associate the meter reading with a meter
            'meter_id'      => \App\Models\Meter::factory(),
            // Generate a random reading value between 100 and 10000
            'reading_value' => $this->faker->numberBetween(100, 10000),
            // Generate a random reading date within the past year
            'reading_date'  => Carbon::now()->subDays(rand(1, 365)),
        ];
    }
}
