<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

class CaveCoordinatesFactory extends Factory
{
    protected $model = \App\Models\CaveCoordinates::class;

    public function definition(): array
    {
        $lat = $this->faker->latitude(43, 43.5); //France, Var
        $lon = $this->faker->longitude(5.39, 6.56);  //France, Var

        return [
            'location' => DB::raw(
                "ST_GeomFromText('POINT($lon $lat)', 4326)"
            ),
            'z' => $this->faker->numberBetween(0, 865 ),
        ];
    }
}