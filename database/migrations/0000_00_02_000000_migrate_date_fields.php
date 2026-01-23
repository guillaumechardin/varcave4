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
        //Table CAVE
        DB::unprepared('DROP TRIGGER IF EXISTS `editDateAuIns`');
        DB::unprepared('DROP TRIGGER IF EXISTS `editDateAuUpd`');
        DB::table('caves')
        ->whereNotNull('edit_date')
        ->update([
            'updated_at' => DB::raw("
                CASE
                    WHEN edit_date IS NULL OR edit_date = 0
                    THEN FROM_UNIXTIME(edit_date)
                    ELSE FROM_UNIXTIME(edit_date)
                END
            "),
        ]);

        Schema::table('caves', function (Blueprint $table) {
            $table->dropColumn(['edit_date']);
        });
        //END CAVE table

        //Table HOME ANNOUCEMENTS
        DB::unprepared('DROP TRIGGER IF EXISTS `news_insCreationDate`');
        DB::unprepared('DROP TRIGGER IF EXISTS `news_updEditdate`');

        DB::table('home_announcements')
        ->whereNotNull('legacy_created_at')
        ->update([
            'created_at' => DB::raw('FROM_UNIXTIME(legacy_created_at)'),
            'updated_at' => DB::raw("
                CASE
                    WHEN legacy_updated_at IS NULL OR legacy_updated_at = 0
                    THEN FROM_UNIXTIME(legacy_created_at)
                    ELSE FROM_UNIXTIME(legacy_updated_at)
                END
            "),
        ]);
        
        Schema::table('home_announcements', function (Blueprint $table) {
            $table->dropColumn(['legacy_created_at', 'legacy_updated_at']);
        });
        //END HOME ANNOUCEMENTS

        //Table CAVE CHANGELOGS
        DB::unprepared('UPDATE cave_changelogs SET created_at = date');
        
        Schema::table('cave_changelogs', function (Blueprint $table) {
            $table->dropColumn(['date']);
        });
        //END CAVE CHANGELOGS

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        /*Schema::dropIfExists('caves'); //simpler to delete... And restore
        Schema::dropIfExists('cave_files');
        Schema::dropIfExists('home_announcements');*/
	}

};
