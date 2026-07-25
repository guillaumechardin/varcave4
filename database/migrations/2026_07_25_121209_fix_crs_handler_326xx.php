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
        DB::table('coordinate_system_handlers')
        ->where('epsg_code', '326')
        ->update([
            'js_handler_path' => '/varcave/proj4-crs-handler/326xx.js',
        ]);//
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('coordinate_system_handlers')
        ->where('epsg_code', '326')
        ->update([
            'js_handler_path' => '/BAD/FILEPATH/FROM/DB/coordinate_system_handlers/326xx.js',
        ]);//
    }
};
