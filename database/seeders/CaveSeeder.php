<?php

namespace Database\Seeders;

use App\Models\Cave;
use App\Models\CaveCoordinates;
use Illuminate\Database\Seeder;

class CaveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Cave::factory(30)->create();

        $cave = Cave::create([
            'uuid' => '00000000-0000-0000-0000-000000000000',
            'name' => 'Gouffre de Test',
            'explorers' => 'Jean Dupont, Marie Martin',
            'addendum' => 'G.2026',
            'CO2' => true,
            'access_text' => 'Entrée par la vallée, attention éboulis.',
            'length' => 250.5,
            'max_depth' => 83,
            'pollution' => 1,
            'coords_GPS_checked' => true,
            'description' => 'Cave de test pour validation seed. Ne pas tenir compte de cette description',
        ]);

        // Add random points
        CaveCoordinates::factory()->count(3)->create([  
            'cave_id' => $cave->id,
        ]);

        /*
         * add specific points
            CaveCoordinates::create([
                'cave_id' => $cave->id,
                'location' => DB::raw("ST_GeomFromText('POINT(5.123 44.567)', 4326)"),
                'z' => 120,
            ]);
        */

            $cave = Cave::create([
            'uuid' => '00000000-0000-0000-0000-000000000001',
            'name' => 'Gouffre de Test numéro 2',
            'explorers' => 'Julien Dupont, Franck Moreno',
            'addendum' => 'G.2026',
            'CO2' => true,
            'length' => 250.5,
            'max_depth' => 83,
            'pollution' => 1,
            'coords_GPS_checked' => true,
            'access_text' => 'Accès depuis le chemin forestier nord, prudence sur les rochers instables.',

            'description' => 'Cave de test pour validation seed. L’entrée principale se situe au nord du chemin forestier, accessible seulement à pied après une courte descente raide. Le porche est étroit et nécessite de se pencher pour pénétrer à l’intérieur. À l’intérieur, plusieurs galeries s’ouvrent à différents niveaux, certaines partiellement effondrées, d’autres offrant un passage continu jusqu’aux salles supérieures. La cavité présente également des concrétions anciennes et des traces d’anciens explorateurs. Ne pas tenir compte de cette description pour usage réel, elle sert uniquement à tester le seeder et la génération de données.',

        ]);

    }
}
