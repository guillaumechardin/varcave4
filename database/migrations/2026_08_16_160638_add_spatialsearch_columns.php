<?php

use App\Models\Field;
use App\Models\Page;
use App\Models\PageField;
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
        $fields = Field::all()->select('id', 'key','data_type');

        foreach($fields as $f){
            PageField::insert(
            [
                'page_key' => 'spatialsearchColumns',
                'field_id' => $f['id'],
                'section_key' => 'main',
                'is_visible' => in_array($f['key'], ['name', 'town', 'max_depth', 'length']),
                'sort_order' => 0,
                'created_at' => now(),
                'updated_at' => null,
            ]);
        }

        Page::insert(
            [
                'key' => 'spatialsearchColumns',
                'description' => 'Recherche spatiale',
                'created_at' => now(),
                'updated_at' => null,
            ],
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('page_fields')
        ->where('page_key', 'spatialsearchColumns')
        ->delete();
    }
};
