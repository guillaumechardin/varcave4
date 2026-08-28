<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cave_coordinates', function (Blueprint $table) {
            $table->point('location', 4326)->change();
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
            $table->point('location', 4326)->change();
        });
    }
};
