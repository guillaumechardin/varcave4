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
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        DB::statement("
            ALTER TABLE `caves`
            MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT
        ");

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        Schema::table('caves', function (Blueprint $table) {
            $table->boolean('ENS')->default(0)->change();
            $table->boolean('foret_domaniale')->default(0)->change();
            $table->integer('cave_type')->nullable()->change();
        });

        /**
         * Update settings for cave copy
         */
        DB::table('settings')
        ->where('name', 'excludedcopyfields')
        ->update([
            'value' => json_encode([
                'id',
                'uuid',
                'name',
                'cave_ref',
                'description',
                'created_at',
                'updated_at',
                'deleted_at'
            ]),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Schema::table('caves', function (Blueprint $table) {
            DB::statement("ALTER TABLE caves MODIFY id BIGINT UNSIGNED NOT NULL");
        });
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        DB::table('settings')
        ->where('name', 'excludedcopyfields')
        ->update([
            'value' => json_encode([])]);
    }
};
