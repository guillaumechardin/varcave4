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
        //Add pdf author
        DB::table('settings')->insert([
            'name' =>'pdf_author',
            'value' => 'CDS 83',
            'type'  => 'string',
            'category' => 'pdf',
            'is_advanced_option' => 0,
            'legacy_mtime' => 0,
        ]);

        //optionnal keywords
        DB::table('settings')->insert([
            'name' =>'keywords',
            'value' => '',
            'type'  => 'string',
            'category' => 'pdf',
            'is_advanced_option' => 0,
            'legacy_mtime' => 0,
        ]);

        //optionnal pdf header title
        DB::table('settings')->insert([
            'name' =>'pdf_header_title',
            'value' => 'Fichier des Cavités du Var',
            'type'  => 'string',
            'category' => 'pdf',
            'is_advanced_option' => 0,
            'legacy_mtime' => 0,
        ]);

        
        DB::table('settings')->insert([
            'name' =>'pdf_map_zoom',
            'value' => 16,
            'type'  => 'numeric',
            'category' => 'pdf',
            'is_advanced_option' => 0,
            'legacy_mtime' => 0,
        ]);

        DB::table('settings')->insert([
            'name' =>'pdf_map_cache_delay',
            'value' => 24,
            'type'  => 'numeric',
            'category' => 'pdf',
            'is_advanced_option' => 0,
            'legacy_mtime' => 0,
        ]);

        DB::table('settings')->insert([
            'name' =>'pdf_file_cache_delay',
            'value' => 24,
            'type'  => 'numeric',
            'category' => 'pdf',
            'is_advanced_option' => 0,
            'legacy_mtime' => 0,
        ]);

        DB::table('settings')->insert([
            'name' =>'pdf_minimap_service',
            'value' => 'setting.opentopomap',
            'type'  => 'list',
            'category' => 'pdf',
            'is_advanced_option' => 0,
            'legacy_mtime' => 0,
        ]);

        DB::table('list_values')->insert([
            // TILE SRC URL
            ['list_name' =>'setting.pdf_minimap_service', 'value' => 0, 'i18n_key' => 'mapnik', 'sort_order' => 0, 'is_active' => 1],
            ['list_name' =>'setting.pdf_minimap_service', 'value' => 1, 'i18n_key' => 'osmarenderer', 'sort_order' => 0, 'is_active' => 1],
            ['list_name' =>'setting.pdf_minimap_service', 'value' => 2, 'i18n_key' => 'cycle', 'sort_order' => 0, 'is_active' => 1],
            ['list_name' =>'setting.pdf_minimap_service', 'value' => 3, 'i18n_key' => 'opentopomap', 'sort_order' => 0, 'is_active' => 1],
            ['list_name' =>'setting.pdf_minimap_service', 'value' => 4, 'i18n_key' => 'outdoor', 'sort_order' => 0, 'is_active' => 1],
            //CRS 
            ['list_name' =>'setting.pdf_coords_system', 'value' => 0, 'i18n_key' => 'varcave.coordinateSystems.wgs84', 'sort_order' => 0, 'is_active' => 1],
            ['list_name' =>'setting.pdf_coords_system', 'value' => 1, 'i18n_key' => 'varcave.coordinateSystems.lambert3', 'sort_order' => 0, 'is_active' => 1],
            ['list_name' =>'setting.pdf_coords_system', 'value' => 2, 'i18n_key' => 'varcave.coordinateSystems.lambert93', 'sort_order' => 0, 'is_active' => 1],
            ['list_name' =>'setting.pdf_coords_system', 'value' => 3, 'i18n_key' => 'varcave.coordinateSystems.utm', 'sort_order' => 0, 'is_active' => 1],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')
            ->whereIn('name', ['keywords', 'pdf_author',
                        'pdf_header_title', 'pdf_map_zoom',
                        'pdf_map_cache_delay', 'pdf_file_cache_delay',
                        'pdf_minimap_service',
            ])
            ->delete();

        DB::table('list_values')
            ->where('list_name', 'setting.pdf_minimap_service', 'setting.pdf_coords_system')
            ->delete();
    }
};
