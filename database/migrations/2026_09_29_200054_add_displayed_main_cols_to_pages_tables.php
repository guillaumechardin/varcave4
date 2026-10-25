<?php

use App\Models\Page;
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
        Schema::table('pages', function (Blueprint $table) {
            $table->unsignedSmallInteger('display_columns')->nullable()->after('description');
        });

        DB::table('pages')
        ->where('key', 'display')
        ->update(['display_columns' => 5]);

        DB::table('pages')
        ->where('key', 'pdf')
        ->update(['display_columns' => 3]);

        DB::table('pages')
        ->where('key', 'edit')
        ->update(['display_columns' => 5]);

        DB::table('pages')
        ->where('key', 'search')
        ->update(['display_columns' => null]);

        DB::table('pages')
        ->where('key', 'searchResultsColumns')
        ->update(['display_columns' => null]);

        DB::table('pages')
        ->where('key', 'gpx-build')
        ->update(['display_columns' => null]);

        DB::table('pages')
        ->where('key', 'spatialsearchColumns')
        ->update(['display_columns' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('display_columns');
        });
    }
};
