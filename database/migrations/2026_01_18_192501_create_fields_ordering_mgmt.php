<?php

use App\Models\Field;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // -------------------------
        // Table fields
        // -------------------------
        Schema::create('fields', function (Blueprint $table) {
            $table->id();
            $table->string('key', 64)->unique();
            $table->enum('data_type', ['string','number','bool','date','json','geo']);
            $table->enum('storage_type', ['column','relation','json','computed', 'list']);
            $table->string('storage_target', 128);
            $table->timestamps();
        });

        // Seed automatique à partir des colonnes de cave
        $columns = [
            'uuid'                  => ['data_type'=>'string', 'storage_type'=>'column'],
            'name'                  => ['data_type'=>'string', 'storage_type'=>'column'],
            'addendum'              => ['data_type'=>'string', 'storage_type'=>'column'],
            'edit_year'             => ['data_type'=>'string', 'storage_type'=>'column'],
            'bibliography'          => ['data_type'=>'string', 'storage_type'=>'column'],
            'map_name'              => ['data_type'=>'string', 'storage_type'=>'column'],
            'town'                  => ['data_type'=>'string', 'storage_type'=>'column'],
            'CO2'                   => ['data_type'=>'bool',   'storage_type'=>'column'],
            'access_text'           => ['data_type'=>'string', 'storage_type'=>'column'],
            'airflow_date'          => ['data_type'=>'string', 'storage_type'=>'column'],
            'explore_date'          => ['data_type'=>'string', 'storage_type'=>'column'],
            'description'           => ['data_type'=>'string', 'storage_type'=>'column'],
            'document_of_origin'    => ['data_type'=>'string', 'storage_type'=>'column'],
            'length'                => ['data_type'=>'number', 'storage_type'=>'column'],
            'explorers'             => ['data_type'=>'string', 'storage_type'=>'column'],
            //'edit_date'             => ['data_type'=>'number', 'storage_type'=>'column'], //no more exist, migrated to updated_at
            'geology'               => ['data_type'=>'string', 'storage_type'=>'column'],
            'hydrology'             => ['data_type'=>'string', 'storage_type'=>'column'],
            'inventor'              => ['data_type'=>'string', 'storage_type'=>'column'],
            'place'                 => ['data_type'=>'string', 'storage_type'=>'column'],
            'mountain_range'        => ['data_type'=>'string', 'storage_type'=>'column'],
            'airflow'               => ['data_type'=>'bool',   'storage_type'=>'column'],
            'numero_arrondissement' => ['data_type'=>'string', 'storage_type'=>'column'],
            'numero_commune'        => ['data_type'=>'string', 'storage_type'=>'column'],
            'numero_departement'    => ['data_type'=>'string', 'storage_type'=>'column'],
            'cave_ref'              => ['data_type'=>'string', 'storage_type'=>'column'],
            'depth'                 => ['data_type'=>'string', 'storage_type'=>'column'],
            'max_depth'             => ['data_type'=>'number', 'storage_type'=>'column'],
            'area'                  => ['data_type'=>'string', 'storage_type'=>'column'],
            'topographer'           => ['data_type'=>'string', 'storage_type'=>'column'],
            'pollution'             => ['data_type'=>'number', 'storage_type'=>'column'],
            'random_coordinates'    => ['data_type'=>'bool',   'storage_type'=>'column'],
            'coords_GPS_checked'    => ['data_type'=>'bool',   'storage_type'=>'column'],
            'zone_natura_2000'      => ['data_type'=>'bool',   'storage_type'=>'column'],
            'anchors'               => ['data_type'=>'bool',   'storage_type'=>'column'],
            'no_access'             => ['data_type'=>'bool',   'storage_type'=>'column'],
            'PNR_SB'                => ['data_type'=>'bool',   'storage_type'=>'column'],
            'created_at'            => ['data_type'=>'date',   'storage_type'=>'column'],
            'updated_at'            => ['data_type'=>'date',   'storage_type'=>'column'],
            'deleted_at'            => ['data_type'=>'date',   'storage_type'=>'column'],
        ];

        $now = now();
        $insertData = [];
        
        foreach ($columns as $key => $props) {
            $insertData[] = [
                'key' => $key,
                'data_type' => $props['data_type'],
                'storage_type' => $props['storage_type'],
                'storage_target' => 'cave.' . $key,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        $insertData = array_merge($insertData, [ 
            [
                'key' => 'photos', 
                'data_type' => 'string',
                'storage_type' => 'relation',
                'storage_target' => 'cave_files',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'sketch_access', 
                'data_type' => 'string',
                'storage_type' => 'relation',
                'storage_target' => 'cave_files',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'key' => 'bio_documents', 
                'data_type' => 'string',
                'storage_type' => 'relation',
                'storage_target' => 'cave_files',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'rescue_data', 
                'data_type' => 'string',
                'storage_type' => 'relation',
                'storage_target' => 'cave_files',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'coordinates', 
                'data_type' => 'string',
                'storage_type' => 'relation',
                'storage_target' => 'cave_coordinates',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        DB::table('fields')->insert($insertData);

        // -------------------------
        // Table pages
        // -------------------------
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('key', 32)->unique();
            $table->string('description', 255)->nullable();
            $table->timestamps();
        });

        // Seed example pages
        DB::table('pages')->insert([
            ['key' => 'display', 'description' => 'Affichage public de la cavité', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'pdf',     'description' => 'Export PDF', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'edit',    'description' => 'Édition cavité', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'search',  'description' => 'Recherche cavité', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // -------------------------
        // Table page_fields with section_key
        // -------------------------
        Schema::create('page_fields', function (Blueprint $table) {
            $table->id();
            $table->string('page_key', 32);   // display, pdf, edit, search
            $table->foreignId('field_id')->constrained('fields')->cascadeOnDelete();
            $table->string('section_key', 32); // main, other, files, map
            $table->boolean('is_visible')->default(0);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['page_key','field_id']);
        });

        // Seed example page_fields for display.php
        $insertArray = array();
        $pages = ['display', 'pdf', 'edit', 'search'];
        $fields = Field::all();
        foreach($pages as $page){
            foreach($fields as $field){
                $insertArray[] = [
                    'page_key' => $page,
                    'field_id' => $field['id'],
                    'section_key' => 'main',
                    'is_visible' => 1,
                    'sort_order' => 100,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
        }
        DB::table('page_fields')->insert($insertArray);

        //populate db with real usage info
        $updates = [
            'uuid' => ['is_visible' => 0],
            'name' => ['is_visible' => 0, 'sort_order' => 1],
            'bibliography' => ['section_key' => 'bibliography', 'is_visible' => 0],
            'town' => ['sort_order' => 2],
            'access_text' => ['section_key' => 'access', 'is_visible' => 0],
            'description' => ['section_key' => 'description', 'is_visible' => 0],
            'cave_ref' => ['sort_order' => 1],
            'mountain_range' => ['sort_order' => 3],
            'deleted_at' => ['is_visible' => 0], //deleted_at

            //change section for non main fields
            'photos' => ['section_key' => 'photos'],
            'bio_documents' => ['section_key' => 'bio_documents'],
            'rescue_data' => ['section_key' => 'rescue_data'],
            'coordinates' => ['section_key' => 'coordinates'],
        ];

        foreach ($updates as $fieldKey => $values) {
            DB::table('page_fields')
        ->whereIn('field_id', function ($query) use ($fieldKey) {
            $query->select('id')
                  ->from('fields')
                  ->where('key', $fieldKey);
        })
        ->update($values);
        }

    }

    public function down(): void
    {
        Schema::dropIfExists('page_fields');
        Schema::dropIfExists('pages');
        Schema::dropIfExists('fields');
    }
};
