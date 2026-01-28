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
            $table->enum('data_type', ['string','number','bool','date','json','geo', 'delimitedArray']);
            $table->enum('storage_type', ['column','relation','json','computed', 'list']);
            $table->char('unit', length: 10)->nullable()->default(null);
            $table->string('storage_target', 128);
            $table->timestamps();
        });

        // Seed automatique à partir des colonnes de cave
        $columns = [
    'uuid' => [
        'data_type' => 'string', 
        'storage_type' => 'column',
        'pages' => [
            'display' => [
                'section_key' => 'main',
                'is_visible' => 0,
                'sort_order' => 1,
            ],
            'pdf' => [
                'section_key' => 'main',
                'is_visible' => 0,
                'sort_order' => 1,
            ],
            'edit' => [
                'section_key' => 'main',
                'is_visible' => 0,
                'sort_order' => 1,
            ],
            'search' => [
                'section_key' => 'main',
                'is_visible' => 0,
                'sort_order' => 1,
            ],
        ],
    ],
    
    'name' => [
        'data_type' => 'string', 
        'storage_type' => 'column',
        'pages' => [
            'display' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 10,
            ],
            'pdf' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 10,
            ],
            'edit' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 10,
            ],
            'search' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 10,
            ],
        ],
    ],
    
    'addendum' => [
        'data_type' => 'string', 
        'storage_type' => 'column',
        'pages' => [
            'display' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 20,
            ],
            'pdf' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 20,
            ],
            'edit' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 20,
            ],
            'search' => [
                'section_key' => 'main',
                'is_visible' => 0,
                'sort_order' => 20,
            ],
        ],
    ],
    
    'edit_year' => [
        'data_type' => 'string', 
        'storage_type' => 'column',
        'pages' => [
            'display' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 30,
            ],
            'pdf' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 30,
            ],
            'edit' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 30,
            ],
            'search' => [
                'section_key' => 'main',
                'is_visible' => 0,
                'sort_order' => 30,
            ],
        ],
    ],
    
    'bibliography' => [
        'data_type' => 'delimitedArray', 
        'storage_type' => 'column',
        'pages' => [
            'display' => [
                'section_key' => 'bibliography',
                'is_visible' => 1,
                'sort_order' => 10,
            ],
            'pdf' => [
                'section_key' => 'bibliography',
                'is_visible' => 1,
                'sort_order' => 10,
            ],
            'edit' => [
                'section_key' => 'bibliography',
                'is_visible' => 1,
                'sort_order' => 10,
            ],
            'search' => [
                'section_key' => 'bibliography',
                'is_visible' => 0,
                'sort_order' => 10,
            ],
        ],
    ],
    
    'map_name' => [
        'data_type' => 'string', 
        'storage_type' => 'column',
        'pages' => [
            'display' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 10,
            ],
            'pdf' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 10,
            ],
            'edit' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 10,
            ],
            'search' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 10,
            ],
        ],
    ],
    
    'town' => [
        'data_type' => 'string', 
        'storage_type' => 'column',
        'pages' => [
            'display' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 12,
            ],
            'pdf' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 12,
            ],
            'edit' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 12,
            ],
            'search' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 12,
            ],
        ],
    ],
    
    'CO2' => [
        'data_type' => 'bool', 
        'storage_type' => 'column',
        'pages' => [
            'display' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 10,
            ],
            'pdf' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 10,
            ],
            'edit' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 10,
            ],
            'search' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 10,
            ],
        ],
    ],
    
    'access_text' => [
        'data_type' => 'string', 
        'storage_type' => 'column',
        'pages' => [
            'display' => [
                'section_key' => 'access',
                'is_visible' => 1,
                'sort_order' => 10,
            ],
            'pdf' => [
                'section_key' => 'access',
                'is_visible' => 1,
                'sort_order' => 10,
            ],
            'edit' => [
                'section_key' => 'access',
                'is_visible' => 1,
                'sort_order' => 10,
            ],
            'search' => [
                'section_key' => 'access',
                'is_visible' => 0,
                'sort_order' => 10,
            ],
        ],
    ],
    
    'airflow_date' => [
        'data_type' => 'string', 
        'storage_type' => 'column',
        'pages' => [
            'display' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 30,
            ],
            'pdf' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 30,
            ],
            'edit' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 30,
            ],
            'search' => [
                'section_key' => 'main',
                'is_visible' => 0,
                'sort_order' => 30,
            ],
        ],
    ],
    
    'explore_date' => [
        'data_type' => 'string', 
        'storage_type' => 'column',
        'pages' => [
            'display' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 10,
            ],
            'pdf' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 10,
            ],
            'edit' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 10,
            ],
            'search' => [
                'section_key' => 'main',
                'is_visible' => 0,
                'sort_order' => 10,
            ],
        ],
    ],
    
    'description' => [
        'data_type' => 'string', 
        'storage_type' => 'column',
        'pages' => [
            'display' => [
                'section_key' => 'description',
                'is_visible' => 1,
                'sort_order' => 40,
            ],
            'pdf' => [
                'section_key' => 'description',
                'is_visible' => 1,
                'sort_order' => 40,
            ],
            'edit' => [
                'section_key' => 'description',
                'is_visible' => 1,
                'sort_order' => 40,
            ],
            'search' => [
                'section_key' => 'description',
                'is_visible' => 0,
                'sort_order' => 40,
            ],
        ],
    ],
    
    'document_of_origin' => [
        'data_type' => 'string', 
        'storage_type' => 'column',
        'pages' => [
            'display' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 20,
            ],
            'pdf' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 20,
            ],
            'edit' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 20,
            ],
            'search' => [
                'section_key' => 'main',
                'is_visible' => 0,
                'sort_order' => 20,
            ],
        ],
    ],
    
    'length' => [
        'data_type' => 'number', 
        'storage_type' => 'column', 
        'unit' => 'm',
        'pages' => [
            'display' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 10,
            ],
            'pdf' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 10,
            ],
            'edit' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 10,
            ],
            'search' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 10,
            ],
        ],
    ],
    
    'explorers' => [
        'data_type' => 'string', 
        'storage_type' => 'column',
        'pages' => [
            'display' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 20,
            ],
            'pdf' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 20,
            ],
            'edit' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 20,
            ],
            'search' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 20,
            ],
        ],
    ],
    
    'geology' => [
        'data_type' => 'string', 
        'storage_type' => 'column',
        'pages' => [
            'display' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 10,
            ],
            'pdf' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 10,
            ],
            'edit' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 10,
            ],
            'search' => [
                'section_key' => 'main',
                'is_visible' => 0,
                'sort_order' => 10,
            ],
        ],
    ],
    
    'hydrology' => [
        'data_type' => 'string', 
        'storage_type' => 'column',
        'pages' => [
            'display' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 20,
            ],
            'pdf' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 20,
            ],
            'edit' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 20,
            ],
            'search' => [
                'section_key' => 'main',
                'is_visible' => 0,
                'sort_order' => 20,
            ],
        ],
    ],
    
    'inventor' => [
        'data_type' => 'string', 
        'storage_type' => 'column',
        'pages' => [
            'display' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 30,
            ],
            'pdf' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 30,
            ],
            'edit' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 30,
            ],
            'search' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 30,
            ],
        ],
    ],
    
    'place' => [
        'data_type' => 'string', 
        'storage_type' => 'column',
        'pages' => [
            'display' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 30,
            ],
            'pdf' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 30,
            ],
            'edit' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 30,
            ],
            'search' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 30,
            ],
        ],
    ],
    
    'mountain_range' => [
        'data_type' => 'string', 
        'storage_type' => 'column',
        'pages' => [
            'display' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 13,
            ],
            'pdf' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 13,
            ],
            'edit' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 13,
            ],
            'search' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 13,
            ],
        ],
    ],
    
    'airflow' => [
        'data_type' => 'bool', 
        'storage_type' => 'column',
        'pages' => [
            'display' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 20,
            ],
            'pdf' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 20,
            ],
            'edit' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 20,
            ],
            'search' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 20,
            ],
        ],
    ],
    
    'numero_arrondissement' => [
        'data_type' => 'string', 
        'storage_type' => 'column',
        'pages' => [
            'display' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 10,
            ],
            'pdf' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 10,
            ],
            'edit' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 10,
            ],
            'search' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 10,
            ],
        ],
    ],
    
    'numero_commune' => [
        'data_type' => 'string', 
        'storage_type' => 'column',
        'pages' => [
            'display' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 20,
            ],
            'pdf' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 20,
            ],
            'edit' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 20,
            ],
            'search' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 20,
            ],
        ],
    ],
    
    'numero_departement' => [
        'data_type' => 'string', 
        'storage_type' => 'column',
        'pages' => [
            'display' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 30,
            ],
            'pdf' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 30,
            ],
            'edit' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 30,
            ],
            'search' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 30,
            ],
        ],
    ],
    
    'cave_ref' => [
        'data_type' => 'string', 
        'storage_type' => 'column',
        'pages' => [
            'display' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 9,
            ],
            'pdf' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 9,
            ],
            'edit' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 9,
            ],
            'search' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 9,
            ],
        ],
    ],
    
    'depth' => [
        'data_type' => 'string', 
        'storage_type' => 'column',
        'pages' => [
            'display' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 20,
            ],
            'pdf' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 20,
            ],
            'edit' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 20,
            ],
            'search' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 20,
            ],
        ],
    ],
    
    'max_depth' => [
        'data_type' => 'number', 
        'storage_type' => 'column', 
        'unit' => 'm',
        'pages' => [
            'display' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 30,
            ],
            'pdf' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 30,
            ],
            'edit' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 30,
            ],
            'search' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 30,
            ],
        ],
    ],
    
    'area' => [
        'data_type' => 'string', 
        'storage_type' => 'column',
        'pages' => [
            'display' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 50,
            ],
            'pdf' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 50,
            ],
            'edit' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 50,
            ],
            'search' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 50,
            ],
        ],
    ],
    
    'topographer' => [
        'data_type' => 'string', 
        'storage_type' => 'column',
        'pages' => [
            'display' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 30,
            ],
            'pdf' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 30,
            ],
            'edit' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 30,
            ],
            'search' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 30,
            ],
        ],
    ],
    
    'pollution' => [
        'data_type' => 'number', 
        'storage_type' => 'column',
        'pages' => [
            'display' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 10,
            ],
            'pdf' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 10,
            ],
            'edit' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 10,
            ],
            'search' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 10,
            ],
        ],
    ],
    
    'random_coordinates' => [
        'data_type' => 'bool', 
        'storage_type' => 'column',
        'pages' => [
            'display' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 60,
            ],
            'pdf' => [
                'section_key' => 'main',
                'is_visible' => 0,
                'sort_order' => 60,
            ],
            'edit' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 60,
            ],
            'search' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 60,
            ],
        ],
    ],
    
    'coords_GPS_checked' => [
        'data_type' => 'bool', 
        'storage_type' => 'column',
        'pages' => [
            'display' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 70,
            ],
            'pdf' => [
                'section_key' => 'main',
                'is_visible' => 0,
                'sort_order' => 70,
            ],
            'edit' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 70,
            ],
            'search' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 70,
            ],
        ],
    ],
    
    'zone_natura_2000' => [
        'data_type' => 'bool', 
        'storage_type' => 'column',
        'pages' => [
            'display' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 20,
            ],
            'pdf' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 20,
            ],
            'edit' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 20,
            ],
            'search' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 20,
            ],
        ],
    ],
    
    'anchors' => [
        'data_type' => 'bool', 
        'storage_type' => 'column',
        'pages' => [
            'display' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 20,
            ],
            'pdf' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 20,
            ],
            'edit' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 20,
            ],
            'search' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 20,
            ],
        ],
    ],
    
    'no_access' => [
        'data_type' => 'bool', 
        'storage_type' => 'column',
        'pages' => [
            'display' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 30,
            ],
            'pdf' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 30,
            ],
            'edit' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 30,
            ],
            'search' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 30,
            ],
        ],
    ],
    
    'PNR_SB' => [
        'data_type' => 'bool', 
        'storage_type' => 'column',
        'pages' => [
            'display' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 30,
            ],
            'pdf' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 30,
            ],
            'edit' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 30,
            ],
            'search' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 30,
            ],
        ],
    ],
    
    'coordinates' => [
        'data_type' => 'string', 
        'storage_type' => 'relation',
        'storage_target' => 'cave_coordinates',
        'pages' => [
            'display' => [
                'section_key' => 'coordinates',
                'is_visible' => 1,
                'sort_order' => 80,
            ],
            'pdf' => [
                'section_key' => 'coordinates',
                'is_visible' => 1,
                'sort_order' => 80,
            ],
            'edit' => [
                'section_key' => 'coordinates',
                'is_visible' => 1,
                'sort_order' => 80,
            ],
            'search' => [
                'section_key' => 'coordinates',
                'is_visible' => 0,
                'sort_order' => 80,
            ],
        ],
    ],
    
    'bio_documents' => [
        'data_type' => 'string', 
        'storage_type' => 'relation',
        'storage_target' => 'cave_files',
        'pages' => [
            'display' => [
                'section_key' => 'bio_documents',
                'is_visible' => 1,
                'sort_order' => 40,
            ],
            'pdf' => [
                'section_key' => 'bio_documents',
                'is_visible' => 1,
                'sort_order' => 40,
            ],
            'edit' => [
                'section_key' => 'bio_documents',
                'is_visible' => 1,
                'sort_order' => 40,
            ],
            'search' => [
                'section_key' => 'bio_documents',
                'is_visible' => 0,
                'sort_order' => 40,
            ],
        ],
    ],
    
    'photos' => [
        'data_type' => 'string', 
        'storage_type' => 'relation',
        'storage_target' => 'cave_files',
        'pages' => [
            'display' => [
                'section_key' => 'photos',
                'is_visible' => 1,
                'sort_order' => 10,
            ],
            'pdf' => [
                'section_key' => 'photos',
                'is_visible' => 1,
                'sort_order' => 10,
            ],
            'edit' => [
                'section_key' => 'photos',
                'is_visible' => 1,
                'sort_order' => 10,
            ],
            'search' => [
                'section_key' => 'photos',
                'is_visible' => 0,
                'sort_order' => 10,
            ],
        ],
    ],
    
    'rescue_data' => [
        'data_type' => 'string', 
        'storage_type' => 'relation',
        'storage_target' => 'cave_files',
        'pages' => [
            'display' => [
                'section_key' => 'rescue_data',
                'is_visible' => 1,
                'sort_order' => 10,
            ],
            'pdf' => [
                'section_key' => 'rescue_data',
                'is_visible' => 1,
                'sort_order' => 10,
            ],
            'edit' => [
                'section_key' => 'rescue_data',
                'is_visible' => 1,
                'sort_order' => 10,
            ],
            'search' => [
                'section_key' => 'rescue_data',
                'is_visible' => 0,
                'sort_order' => 10,
            ],
        ],
    ],
    
    'sketch_access' => [
        'data_type' => 'string', 
        'storage_type' => 'relation',
        'storage_target' => 'cave_files',
        'pages' => [
            'display' => [
                'section_key' => 'access',
                'is_visible' => 1,
                'sort_order' => 20,
            ],
            'pdf' => [
                'section_key' => 'access',
                'is_visible' => 1,
                'sort_order' => 20,
            ],
            'edit' => [
                'section_key' => 'access',
                'is_visible' => 1,
                'sort_order' => 20,
            ],
            'search' => [
                'section_key' => 'access',
                'is_visible' => 0,
                'sort_order' => 20,
            ],
        ],
    ],
    'deleted_at' => [
        'data_type' => 'date', 
        'storage_type' => 'column',
        'storage_target' => 'cave.deleted_at',
        'pages' => [
            'display' => [
                'section_key' => 'main',
                'is_visible' => 0,
                'sort_order' => 99,
            ],
            'pdf' => [
                'section_key' => 'main',
                'is_visible' => 0,
                'sort_order' => 99,
            ],
            'edit' => [
                'section_key' => 'main',
                'is_visible' => 0,
                'sort_order' => 99,
            ],
            'search' => [
                'section_key' => 'main',
                'is_visible' => 0,
                'sort_order' => 99,
            ],
        ],
    ],
    'updated_at' => [
        'data_type' => 'date', 
        'storage_type' => 'column',
        'storage_target' => 'cave.updated_at',
        'pages' => [
            'display' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 98,
            ],
            'pdf' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 98,
            ],
            'edit' => [
                'section_key' => 'main',
                'is_visible' => 0,
                'sort_order' => 98,
            ],
            'search' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 98,
            ],
        ],
    ],
    'created_at' => [
        'data_type' => 'date', 
        'storage_type' => 'column',
        'storage_target' => 'cave.created_at',
        'pages' => [
            'display' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 97,
            ],
            'pdf' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 97,
            ],
            'edit' => [
                'section_key' => 'main',
                'is_visible' => 0,
                'sort_order' => 97,
            ],
            'search' => [
                'section_key' => 'main',
                'is_visible' => 1,
                'sort_order' => 97,
            ],
        ],
    ],
];

        $now = now();
        $insertData = [];
        
        foreach ($columns as $key => $props) {
            $insertData[] = [
                'key' => $key,
                'data_type' => $props['data_type'],
                'storage_type' => $props['storage_type'],
                'storage_target' => 'cave.' . $key,
                'unit' => $props['unit'] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

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
            ['key' => 'display', 'description' => 'Affichage de la cavité', 'created_at' => now(), 'updated_at' => now()],
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
        $fields = new Field();
        $fields = $fields->where('id','>',0)->get();;
        foreach ($fields as $field) {
            foreach ($pages as $page) {
                $insertArray[] = [
                    'page_key' => $page,
                    'field_id' => $field['id'],
                    'section_key' => $columns[$field->key]['pages'][$page]['section_key'],
                    'is_visible'  => $columns[$field->key]['pages'][$page]['is_visible'],
                    'sort_order'  => $columns[$field->key]['pages'][$page]['sort_order'],
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
        }
    
        DB::table('page_fields')->insert($insertArray);

    }

    public function down(): void
    {
        Schema::dropIfExists('page_fields');
        Schema::dropIfExists('pages');
        Schema::dropIfExists('fields');
    }
};
