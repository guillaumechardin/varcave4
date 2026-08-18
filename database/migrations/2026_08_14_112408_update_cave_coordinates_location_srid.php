<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('
            UPDATE cave_coordinates
            SET location = ST_SRID(location, 4326)
            WHERE ST_SRID(location) = 0
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('
            UPDATE cave_coordinates
            SET location = ST_SRID(location, 0)
            WHERE ST_SRID(location) = 4326
        ');
    }
};
