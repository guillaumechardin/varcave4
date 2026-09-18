<?php

use App\Models\ListValue;
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
        $maxListValue = DB::table('list_values')
        ->where('list_name', 'cave_files.file_type')
        ->max('value');

        $maxListValue++;

        DB::table('list_values')->insert([
            'list_name' => 'cave_files.file_type',
            'value' => $maxListValue,
            'i18n_key' => 'varcave.cave_files.cave_traces',
            'sort_order' => 0,
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => null,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('list_values')
            ->where('list_name', 'cave_files.file_type')
            ->where('i18n_key', 'varcave.cave_files.cave_traces')
            ->delete();
    }
};
