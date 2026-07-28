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
        Schema::dropIfExists('end_user_fields');
        Schema::dropIfExists('glog_data');
        Schema::dropIfExists('acl');
        Schema::dropIfExists('dbversion');
        Schema::dropIfExists('glog_meta');

        DB::unprepared('DROP TRIGGER IF EXISTS acl_insEditdate');
        DB::unprepared('DROP TRIGGER IF EXISTS acl_updEditdate');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
