<?php

namespace Database\Factories;

use App\Models\Meter;
use Illuminate\Database\Eloquent\Factories\Factory;

class MeterFactory extends Factory
{
    // Specify the model that this factory is for
    protected $model = Meter::class;

    // Define the default state for the model
    public function definition()
    {
        return [
            // Generate a unique 10-digit MPXN
            'mpxn'                         => $this->faker->unique()->numerify('##########'),
            // Randomly select either 'electricity' or 'gas' for the type
            'type'                         => $this->faker->randomElement(['electricity', 'gas']),
            // Generate a random installation date
            'installation_date'            => $this->faker->date(),
            // Generate a random estimated annual consumption between 1000 and 5000
            'estimated_annual_consumption' => $this->faker->numberBetween(1000, 5000),
        ];
    }
}
