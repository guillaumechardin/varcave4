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
        if (Schema::hasTable('cave_changelogs')) {
            $indexExists = DB::select("
                SELECT COUNT(*) AS count
                FROM information_schema.statistics
                WHERE table_schema = DATABASE()
                AND table_name = 'cave_changelogs'
                AND index_name = 'indexid_caves'
            ")[0]->count;

            if ($indexExists) {
                Schema::table('cave_changelogs', function (Blueprint $table) {
                    $table->dropIndex('indexid_caves');
                });
            }
        }


        Schema::table('cave_changelogs', function (Blueprint $table) {
            $table->renameColumn('is_visible', 'is_homepage_visible');
            $table->boolean('is_deleted')->after('is_homepage_visible')->default(0);

            $table->bigInteger('id')
            ->unsigned()
            ->autoIncrement()
            ->first()
            ->change();

            $table->bigInteger('cave_id')
            ->unsigned()
            ->change();

            $table->foreign('cave_id')
                    ->references('id')
                    ->on('caves')
                    ->cascadeOnDelete();
        });    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cave_changelogs', function (Blueprint $table) {
            $table->renameColumn('is_homepage_visible', 'is_visible' );
            $table->dropColumn('is_deleted');
            $table->dropForeign(['cave_id']);
        });
    }
};
