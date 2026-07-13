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
        DB::table('settings')
        ->where('name', 'noAccessDisclaimer')
        ->update([
            'name' => 'no_access_message',
        ]);
        
        DB::table('settings')
        ->where('name', 'randCoordDisclaimer')
        ->update([
            'name' => 'location_protected_message',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //rollback migr
    }
};
