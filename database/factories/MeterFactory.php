<?php

namespace Database\Factories;

use App\Models\Meter;
use Illuminate\Database\Eloquent\Factories\Factory;

class MeterFactory extends Factory
{
    protected $model = Meter::class;

    public function definition()
    {
        return [
            'mpxn' => $this->faker->unique()->numerify('##########'),
            'type' => $this->faker->randomElement(['electricity', 'gas']),
            'installation_date' => $this->faker->date(),
            'estimated_annual_consumption' => $this->faker->numberBetween(1000, 5000),
        ];
    }
}