<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'username' => 'admin',
            'firstname' => 'Guillaume',
            'lastname' => 'CHARDIN',
            'email' => 'guillaume.chardin@speleo83cds.fr',
            'password' => Hash::make('speleo2025'),
        ]);
    }
}
