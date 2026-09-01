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
            DB::statement("
                UPDATE cave_coordinates
                SET location = ST_SwapXY(location)
            ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Swap X and Y again to restore the original coordinates.
        DB::statement("
            UPDATE cave_coordinates
            SET location = ST_SwapXY(location)
        ");
    }
};
