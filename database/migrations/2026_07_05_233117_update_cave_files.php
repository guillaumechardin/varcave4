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
        Schema::table('cave_files', function (Blueprint $table) {
            $table->bigInteger('id')->autoIncrement()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cave_files', function (Blueprint $table) {
            DB::statement("ALTER TABLE cave_files MODIFY id BIGINT UNSIGNED NOT NULL");
        });
    }
};
