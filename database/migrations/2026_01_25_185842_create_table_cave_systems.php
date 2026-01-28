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
        Schema::create('cave_systems', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->timestamps();
        });

        Schema::table('caves', function (Blueprint $table) {
            $table->unsignedBigInteger('cave_system_id')->nullable()->after('PNR_SB');
            $table->foreign('cave_system_id')
                    ->references('id')
                    ->on('cave_systems')
                    ->nullOnDelete();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_cave_systems');
    }
};
