<?php

use App\Models\Field;
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
        Schema::table('cave_systems', function (Blueprint $table) {
            $table->dropColumn('code');
        });
        
        DB::table('fields')
        ->where('key', 'photos')
        ->orWhere('key', 'coordinates')
        ->orWhere('key', 'bio_documents')
        ->orWhere('key', 'rescue_data')
        ->orWhere('key', 'sketch_access')
        ->delete();

        $fieldCave = Field::firstOrcreate(
            [
                'key' => 'cave_system_id'
            ],
            [
                'key' => 'cave_system_id',
                'data_type ' => 'number',
                'unit' => null,
                'storage_target' => 'CaveSystem.name',
                'storage_type' => 'relation',
                'created_at' => now(),
        ]);

        $pages = [
            'display'=> [
                'is_visible' => 1,
                'sort_order' => 99,
            ],
            'edit'=> [
                'is_visible' => 1,
                'sort_order' => 99,
            ],
            'gpx-build'=> [
                'is_visible' => 0,
                'sort_order' => 0,
            ],
            'pdf'=> [
                'is_visible' => 1,
                'sort_order' => 99,
            ],
            'search'=> [
                'is_visible' => 1,
                'sort_order' => 99,
            ],
            'searchResultsColumns'=> [
                'is_visible' => 0,
                'sort_order' => 0,
            ],
        ];

        foreach($pages as $pageName => $pageData) {
            PageField::firstOrCreate([
                    'page_key' => $pageName,
                    'field_id' => $fieldCave->id,
                ],
                [ 
                    'section_key' => 'main',
                    'is_visible' => $pageData['is_visible'],
                    'sort_order' => $pageData['sort_order'],
                    'created_at' => now(),
                    'updated_at' => NULL,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cave_systems', function (Blueprint $table) {
            $table->string('code')->nullable();
        });
    }
};
