<?php

use App\Models\Field;
use App\Models\ListValue;
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
        if (!Schema::hasColumn('caves', 'ENS')) {
            Schema::table('caves', function (Blueprint $table) {
                $table->boolean('ENS')->default(false)->after('PNR_SB');
            });
        }

        if (!Schema::hasColumn('caves', 'foret_domaniale')) {
            Schema::table('caves', function (Blueprint $table) {
                $table->boolean('foret_domaniale')->default(false)->after('ENS');
            });
        }

        if (!Schema::hasColumn('caves', 'cave_type')) {
            Schema::table('caves', function (Blueprint $table) {
                $table->unsignedTinyInteger('cave_type')->default(0)->after('pollution');
            });
        }

        if (!Schema::hasColumn('caves', 'cave_class')) {
            Schema::table('caves', function (Blueprint $table) {
                $table->unsignedTinyInteger('cave_class')->default(0)->after('cave_type');
            });
        }

        $fields = [
            'ENS' => [
                'data_type' => 'bool',
                'unit' => null,
                'storage_target' => 'cave.ENS',
                'storage_type' => 'column',
            ],
            'foret_domaniale' => [
                'data_type' => 'bool',
                'unit' => null,
                'storage_target' => 'cave.foret_domaniale',
                'storage_type' => 'column',
            ],
            'cave_type' => [
                'data_type' => 'number',
                'unit' => null,
                'storage_target' => 'cave.cave_type',
                'storage_type' => 'list',
            ],
            'cave_class' => [
                'data_type' => 'number',
                'unit' => null,
                'storage_target' => 'cave.cave_class',
                'storage_type' => 'list',
            ],
        ];

        

        $fieldIds = array();
        foreach($fields as $key => $field){
            $fieldIds [] = Field::firstOrCreate(
                [
                    'key' => $key,
                ],
                [
                    'data_type' => $field['data_type'],
                    'unit' => $field['unit'],
                    'storage_target' => $field['storage_target'],
                    'storage_type' => $field['storage_type'],
                    'created_at' => now(),
                    'updated_at' => null,
                ],
            );
        }

        $lists = [
            //cave class type
            [
                'list_name' => 'cave.cave_class',
                'value' => '0',
                'i18n_key' => 'varcave.general.undefined',
                'sort_order' => '0',
                'is_active' => '1' ,
            ],
            [
                'list_name' => 'cave.cave_class',
                'value' => '1',
                'i18n_key' => 'varcave.table_cave.cave_class_list.class1',
                'sort_order' => '1',
                'is_active' => '1' ,
            ],
            [
                'list_name' => 'cave.cave_class',
                'value' => '2',
                'i18n_key' => 'varcave.table_cave.cave_class_list.class2',
                'sort_order' => '2',
                'is_active' => '1' ,
            ],
            [
                'list_name' => 'cave.cave_class',
                'value' => '3',
                'i18n_key' => 'varcave.table_cave.cave_class_list.class3',
                'sort_order' => '3',
                'is_active' => '1' ,
            ],
            [
                'list_name' => 'cave.cave_class',
                'value' => '4',
                'i18n_key' => 'varcave.table_cave.cave_class_list.class4',
                'sort_order' => '4',
                'is_active' => '1' ,
            ],

            //cave type 
            [
                'list_name' => 'cave.cave_type',
                'value' => '0',
                'i18n_key' => 'varcave.general.undefined',
                'sort_order' => '0',
                'is_active' => '1' ,
            ],
            [
                'list_name' => 'cave.cave_type',
                'value' => '1',
                'i18n_key' => 'varcave.table_cave.cave_type_list.type1',
                'sort_order' => '1',
                'is_active' => '1' ,
            ],
            [
                'list_name' => 'cave.cave_type',
                'value' => '2',
                'i18n_key' => 'varcave.table_cave.cave_type_list.type2',
                'sort_order' => '2',
                'is_active' => '1' ,
            ],
        ];

        foreach($lists as $list) {
            ListValue::firstOrCreate(
            [
                'list_name' => $list['list_name'],
                'value' => $list['value'],
                'created_at' => now(),
                'updated_at' => null,
            ],
            $list);
        }


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
            foreach($fieldIds as $f){
                PageField::firstOrCreate([
                        'page_key' => $pageName,
                        'field_id' => $f->id,
                    ],
                    [ 
                        'section_key' => 'main',
                        'is_visible' => $pageData['is_visible'],
                        'sort_order' => $pageData['sort_order'],
                        'created_at' => now(),
                        'updated_at' => null,
                ]);
            }
        }


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('caves', function (Blueprint $table) {
            $table->dropColumn('ENS');
            $table->dropColumn('foret_domaniale');
            $table->dropColumn('cave_type');
            $table->dropColumn('cave_class');
        });

        DB::table('list_values')
        ->where('list_name', 'cave.cave_class')
        ->delete();

        DB::table('list_values')
        ->where('list_name', 'cave.cave_type')
        ->delete();

        //clean fields
        $fieldIds = DB::table('fields')
        ->whereIn('key', [
            'ENS',
            'foret_domaniale',
            'cave_type',
            'cave_class',
        ])
        ->pluck('id');

        DB::table('page_fields')
            ->whereIn('field_id', $fieldIds)
            ->delete();

        DB::table('fields')
            ->whereIn('key', [
                'ENS',
                'foret_domaniale',
                'cave_type',
                'cave_class',
            ])
            ->delete();

    }
};
