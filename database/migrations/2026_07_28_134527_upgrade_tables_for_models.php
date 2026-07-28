<?php

use App\Models\Field;
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
         DB::table('pages')->insert([
            [
                'key' => 'gpx-build',
                'description' => 'Pour génération gpx-build',
                'created_at' => now(),
                'updated_at' => null,
            ],
        ]);

        $visibles = ['name', 'length', 'max_depth'];

        $fields = Field::all();
        foreach($fields as $field){
            DB::table('page_fields')->insert(
            [
                'page_key' => 'gpx-build',
                'field_id' => $field->id,
                'section_key' => 'main',
                'is_visible' => in_array($field->key, $visibles) ?: 0 ,
                'sort_order' => 0,
                'created_at' => now(),
                'updated_at' => null,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         DB::table("page_fields")
            ->where("page_key", "gpx-build")
            ->delete();

        DB::table("pages")
            ->where("key", "gpx-build")
            ->delete();
    }
};
