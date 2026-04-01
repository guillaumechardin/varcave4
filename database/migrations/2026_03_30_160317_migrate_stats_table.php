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
        Schema::create('cave_stats', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cave_id')
                ->constrained()
                ->restrictOnDelete();

            $table->unsignedInteger('auth_views')->default(0);
            $table->unsignedInteger('anon_views')->default(0);

            $table->timestamps();
            // Ensure one stat row per cave
            $table->unique('cave_id');
        });
    }

    public function down(): void
    {
        
    }
};
