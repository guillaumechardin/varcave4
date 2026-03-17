<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        /*Schema::table('settings', function (Blueprint $table) {
            $table->renameColumn('returnSearchFields', 'search_table_columns');
            $table->unsignedTinyInteger('sort_order', 'category');
            
        });*/

        DB::unprepared('
            UPDATE `settings` SET `category` = "config_email" where category = "configemail";
            UPDATE `settings` SET `category` = "google_captcha" where category = "Captcha";
            UPDATE `settings` SET `category` = "general" where category = "configSite";
            UPDATE `settings` SET `category` = "pdf" where category = "PDF";
            UPDATE `settings` SET `category` = "geo_api" where category = "geoAPI";
            UPDATE `settings` SET `category` = "config_site_stats" where category = "configSiteStats";
            UPDATE `settings` SET `type` = "boolean" where `type` = "bool";
            UPDATE `settings` SET `type` = "string" where `type` = "text";
            UPDATE `settings` SET `type` = "numeric" where `type` = "dec";
        ');

        $queries = <<<EOF
        UPDATE `settings` 
            SET `value`='["127.0.0.1","192.168.100.50","109.190.52.101","78.229.153.121","109.21.143.179","92.150.218.220"]', type='json'
            WHERE name="adminIP";
        
        UPDATE `settings` 
            SET `value`='["fichiertopo@speleo83cds.fr"]', type='json'
            WHERE name='smtp_cave_edit_recipients';

        UPDATE `settings` 
            SET `value`='["guillaume.chardin@speleo83cds.fr","fichiertopo@speleo83cds.fr"]', type='json'
            WHERE name='smtp_general_inquiry_recipient';

        UPDATE `settings` 
            SET `value`='["name","guidv4","caveRef","town","mountainRange","area","place","depth","maxDepth","length","airflow","CO2"]', type='json'
            WHERE name='excludedcopyfields';
        
EOF;
        DB::unprepared($queries);

        DB::table('settings')->insert([
            'name' =>'default_coordinates',
            'value' => '43.107107,5.9103941',
            'type'  => 'string',
            'category' => 'general',
            'is_advanced_option' => 1,
            'legacy_mtime' => 0,

        ]);

        
        /*DB::table('settings')
            ->where('name', 'search_table_columns')
            ->update([
                'value' => 'uuid,name,cave_ref,town,mountain_range,area,place,depth,max_depth,length,airflow,CO2',
        ]);*/
        
        $deleteRowNames = [
            'httpdomain', 'httpwebroot', 
            'sessionlifetime', 'maxSearchResults_default',
            'fallbackLanguage', 'RWfolders',
            'caves_files_path', 'use_anon_auth',
            'cache_dir', 'dynamic_rights',
            'anon_get_obfsuc_coords','returnSearchFields',
            'BING_AERIAL_API_KEY',
            'IGN_PHOTOS_IGN_API_KEY',
            'IGN_PLAN_IGN_API_KEY',
            'TDF_LANDSCAPE_TDF_API_KEY',
            'TDF_OUTDOOR_TDF_API_KEY',
            'badPwdCount',
            'ressources_stor_dir',
        ];

        DB::table('settings')
        ->whereIn('name', $deleteRowNames)
        ->delete();
    }  

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
