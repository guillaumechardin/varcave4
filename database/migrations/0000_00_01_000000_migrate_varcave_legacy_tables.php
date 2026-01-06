<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Format old style Varcave database to new style/laravel compatible
     */
    public function up(): void
    {
		Schema::rename('users', 'varcave4_users');
        Schema::table('caves', function (Blueprint $table) {
            $table->renameColumn('indexid', 'id');
             $table->renameColumn('guidv4', 'uuid');
            //$table->tinyText('name');
            //$table->tinyText('addendum');
            $table->renameColumn('editYear', 'edit_year');
            //$table->text('bibliography');
            $table->renameColumn('mapName', 'map_name');
            //$table->tinyText('town');
            //$table->boolean('CO2');
            $table->renameColumn('accessSketchText', 'access_text');
            $table->renameColumn('airflowDate', 'airflow_date'); 
            $table->renameColumn('exploreDate', 'explore_date');
            $table->renameColumn('shortDescription', 'description');
            $table->renameColumn('documentOfOrigin', 'document_of_origin');
            //$table->decimal('length', $precision = 7, $scale = 2)->nullable();
            //$table->tinyText('explorers');
            //$table->tinyText('geology');
            //$table->tinyText('hydrology');
            //$table->tinyText('inventor');
            //$table->tinyText('place');
            $table->renameColumn('mountainRange', 'mountain_range');
            //$table->boolean('airflow');
            //$table->tinyText('numero_arrondissement');
            //$table->tinyText('numero_commune');
            //$table->tinyText('numero_departement');
            $table->renameColumn('caveRef', 'cave_ref');
            //$table->tinyText('depth');
            $table->renameColumn('maxDepth', 'max_depth');
            //$table->tinyText('area');
            //$table->tinyText('topographer');
            //$table->boolean('random_coordinates');
            //$table->boolean('coords_GPS_checked');
            //$table->boolean('zone_natura_2000');
            //$table->boolean('anchors');
            $table->renameColumn('noAccess', 'no_access');
            //$table->boolean('PNR_SB');
			
            // vérif on 12/2025
			/*
             $table->dropColumn('json_coords_old');
			 $table->dropColumn('documents');
			 $table->dropColumn('biologyDocuments');
			 $table->dropColumn('files');
             */

			//a confirmer si champ inutile
            //$table->dropColumn('editYear');
            $table->timestamps();
        });

        /*** CAVE COORDINATES  ***/
        Schema::rename('caves_coordinates', 'cave_coordinates');
        Schema::table('cave_coordinates', function (Blueprint $table) {
             $table->renameColumn('caveid', 'cave_id');
             $table->timestamps();
        });

        /*** CAVE FILES  ***/
        Schema::rename('caves_files', 'cave_files');
        Schema::table('cave_files', function (Blueprint $table) {
            //$table->renameColumn('indexid', 'id');
             $table->renameColumn('caveid', 'cave_id');
             $table->timestamps();
        });

        /*** ABOUT PAGE  ***/
        Schema::table('about_page', function (Blueprint $table) {
            $table->renameColumn('indexid', 'id');
            $table->timestamps();
        });

        
        /*** changelog  ***/
        Schema::rename('changelog', 'cave_changelogs');
        Schema::table('cave_changelogs', function (Blueprint $table) {
            $table->renameColumn('indexid', 'id');
            $table->renameColumn('indexid_caves', 'cave_id');
            $table->renameColumn('chgLogTxt', 'modification_note');
            $table->renameColumn('isVisible', 'is_visible');
            $table->timestamps();
        });

        /*** CONFIG  ***/
        Schema::rename('config', 'settings');
        Schema::table('settings', function (Blueprint $table) {
            $table->renameColumn('configIndexid', 'id');
            $table->renameColumn('configItem', 'name');
            $table->renameColumn('configItemType', 'type');
            $table->renameColumn('configItemValue', 'value');
            $table->renameColumn('configItemGroup', 'category');
            $table->renameColumn('configItemAdminOnly', 'is_advanced_option');
            $table->renameColumn('configItemMtime', 'legacy_mtime'); //will be migrate to updated_at
            $table->timestamps();
        });

        /*** END USER FIELDS  ***/
        Schema::table('end_user_fields', function (Blueprint $table) {
            $table->renameColumn('indexid', 'id');
            $table->renameColumn('field', 'cave_field');
            //$table->renameColumn('type', 'type_to_be_deleted');
            $table->timestamps();
        });

        /*** EULA  ***/
        Schema::table('eula', function (Blueprint $table) {
            $table->dropColumn('last_update');
            $table->timestamps();
        });

        /*** FILE RESSOURCES  ***/
        Schema::rename('files_ressources', 'file_resources');
        Schema::table('file_resources', function (Blueprint $table) {
            $table->renameColumn('indexid', 'id');
            $table->dropColumn('creation_date');
            $table->timestamps();
        });

        /*** GROUPS  ***/
        Schema::rename('groups', 'varcave4_groups');
        Schema::table('varcave4_groups', function (Blueprint $table) {
            $table->renameColumn('indexid', 'id');
            $table->renameColumn('groupName', 'group_name');
            $table->timestamps();
        });

        /*** LAYERS PLUGINS  ***/
        Schema::rename('layers_plugins', 'map_layers');
        Schema::table('map_layers', function (Blueprint $table) {
            $table->renameColumn('indexid', 'id');
            $table->renameColumn('guid', 'uuid');
            $table->renameColumn('map_name', 'code');
            $table->renameColumn('map_display_name', 'display_name');
            $table->renameColumn('path', 'plugin_path');
            $table->renameColumn('is_active', 'is_enabled');
            $table->timestamps();
        });


        /*** LISTS  ***/
        Schema::table('lists', function (Blueprint $table) {
            $table->renameColumn('indexid', 'id');
            $table->renameColumn('list_item', 'value');
            $table->renameColumn('list_name', 'list_key');
            $table->timestamps();
        });

        /*** LISTS  COORDINATES SYSTEMS***/
        Schema::rename('list_coordinates_systems', 'coordinate_system_handlers');
        Schema::table('coordinate_system_handlers', function (Blueprint $table) {
            $table->renameColumn('indexid', 'id');
            $table->renameColumn('indexid_lists', 'list_id');
            $table->renameColumn('php_lib_filename', 'php_handler');
            $table->renameColumn('js_lib_filename', 'js_handler');
            $table->timestamps();
        });

        /*** NEWS ***/
        Schema::rename('news', 'home_announcements');
        Schema::table('home_announcements', function (Blueprint $table) {
            $table->renameColumn('indexid', 'id');
            $table->renameColumn('deleted', 'is_hidden');
            $table->renameColumn('creation_date', 'legacy_created_at');
            $table->renameColumn('edit_date', 'legacy_updated_at');
            $table->timestamps();
        });

        /*** STATS ***/
        Schema::table('stats', function (Blueprint $table) {
            $table->renameColumn('indexid', 'id');
        });

        /*** STATS ***/
        Schema::rename('users_favorites', 'user_favorites');
        Schema::table('user_favorites', function (Blueprint $table) {
            $table->renameColumn('indexid', 'id');
            $table->renameColumn('addDate', 'legacy_created_at');
            $table->renameColumn('userid', 'user_id');
            $table->timestamps();
        });





    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('caves'); //simpler to delete... And restore
        Schema::dropIfExists('cave_files');
        Schema::dropIfExists('home_announcements');
	}
};
