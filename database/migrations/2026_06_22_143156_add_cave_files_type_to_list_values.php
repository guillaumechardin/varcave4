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
        Schema::table('list_values', function (Blueprint $table) {
            $files_types= [
                'cave_maps'         => 0,
                'photos'            => 1,
                'sketch_access'     => 2,
                'biologyDocuments'  => 3,
                'documents'         => 4,
                'rescue_data'       => 5,
            ];

            foreach($files_types as $name => $val){
                DB::table('list_values')->insert([
                    'list_name' => 'cave_files.file_type',
                    'value' => $val,
                    'i18n_key' => 'varcave.cave_files.' . $name,
                    'sort_order' => 0,
                    'is_active' => 1,
                    'created_at' => now(),
                    'updated_at' => null,
                ]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('list_values', function (Blueprint $table) {
            DB::table('list_values')
                ->where('list_name', 'cave_files.file_type')
                ->delete();
        });
    }
};
