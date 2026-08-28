<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('
            ALTER TABLE cave_coordinates
            MODIFY location POINT SRID 4326 NOT NULL
        ');

        Schema::table('cave_coordinates', function (Blueprint $table) {
            $table->spatialIndex('location', 'idx_cave_coordinates_location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cave_coordinates', function (Blueprint $table) {
            $table->dropIndex('idx_cave_coordinates_location');

        });
        
        DB::statement('
            ALTER TABLE cave_coordinates
            MODIFY location POINT NOT NULL
        ');

        
    }
};
