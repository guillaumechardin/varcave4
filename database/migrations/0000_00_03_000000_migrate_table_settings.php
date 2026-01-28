<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            DB::table('settings')->insert([
                'name' =>'default_coordinates',
                'value' => '43.107107,5.9103941',
                'type'  => 'text',
                'category' => 'configSite',
                'is_advanced_option' => 1,
                'legacy_mtime' => 0,

            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
