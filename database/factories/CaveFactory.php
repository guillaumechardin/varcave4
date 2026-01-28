<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cave>
 */
class CaveFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $prefixes = ['le', "l'", 'l\'aven', 'aven de', 'gouffre de',
            'grotte de', 'garagai du', 'event des', 'Source du', 'Aven du', 
            'gouffre du', 'grotte des', 'garagai du'];

        return [
            'uuid' => $this->faker->uuid(),

            'name' => ucfirst(
                $this->faker->randomElement($prefixes)
                . ' '
                . $this->faker->word()
            ),
            
            'cave_ref' => random_int(2016008, 2516388),

            'explorers' => collect(
                    range(1, $this->faker->numberBetween(1, 3))
                )->map(fn () => $this->faker->name('fr_FR'))
                ->implode(', '),

            'addendum' => 'G.2026',

            'CO2' => $this->faker->boolean(),

            'access_text' => $this->faker->paragraph(2),

            'length' => $this->faker->randomFloat(
                1,    // décimales
                5,    // min
                1500  // max
            ),

            'max_depth' => $this->faker->randomFloat(
                1,
                5,
                350
            ),

            'pollution' => $this->faker->numberBetween(0, 3),

            'coords_GPS_checked' => true,

            'description' => $this->faker->paragraphs(3, true),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function ($cave) {
            // randomness : 1 => 60%, 2 => 30%, 3 => 10%
            $rand = mt_rand(1, 100);
            if ($rand <= 60) {
                $pointsCount = 1;
            } elseif ($rand <= 90) {
                $pointsCount = 2;
            } else {
                $pointsCount = 3;
            }

            \App\Models\CaveCoordinates::factory()
                ->count($pointsCount)
                ->create([
                    'cave_id' => $cave->id,
                ]);
        });
    }

} 

