<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('coordinate_system_handlers');

        Schema::create('coordinate_system_handlers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('list_value_id')->constrained('list_values')->restrictOnDelete();
            $table->unsignedInteger('epsg_code')->unique();
            $table->string('epsg_name', 100);
            $table->text('js_handler')->nullable();
            $table->text('php_handler')->nullable();
            $table->text('proj4_string')->nullable();
            $table->boolean('enabled');
            $table->timestamps();
            

            
        });

        
        DB::table('list_values')->insert([
            [
                'list_name'  => 'crs.options',
                'value'      => 0,
                'i18n_key'   => 'varcave.coordinateSystems.wgs84',
                'sort_order' => 0,
                'is_active'  => true,
                'created_at'=> Carbon::now(),
                'updated_at'=> NULL,
            ],
            [
                'list_name'  => 'crs.options',
                'value'      => 1,
                'i18n_key'   => 'varcave.coordinateSystems.lambert3',
                'sort_order' => 0,
                'is_active'  => true,
                'created_at'=> Carbon::now(),
                'updated_at'=> NULL,
            ],
            [
                'list_name'  => 'crs.options',
                'value'      => 2,
                'i18n_key'   => 'varcave.coordinateSystems.lambert93',
                'sort_order' => 0,
                'is_active'  => true,
                'created_at'=> Carbon::now(),
                'updated_at'=> NULL,
            ],
            [
                'list_name'  => 'crs.options',
                'value'      => 3,
                'i18n_key'   => 'varcave.coordinateSystems.utm',
                'sort_order' => 0,
                'is_active'  => true,
                'created_at'=> Carbon::now(),
                'updated_at'=> NULL,
            ],
        ]);

        $crsIds = DB::table('list_values')
        ->where('list_name', 'crs.options')
        ->pluck('id', 'i18n_key')
        ->toArray();

        DB::table('coordinate_system_handlers')->insert([
            [
                'epsg_code' => 4326,
                'epsg_name' => 'WGS84',
                'js_handler'   => NULL,
                'proj4_string' => '+proj=longlat +datum=WGS84 +no_defs +type=crs',
                'list_value_id' => $crsIds['varcave.list.crs.wgs84'],
                'enabled' => 1,
            ],
            [
                'epsg_code'    => 27563,
                'epsg_name'    => 'Lambert Zone III étendu',
                'js_handler'   => NULL,
                'proj4_string'  => '+proj=lcc +lat_1=44.1 +lat_0=44.1 +lon_0=0 +k_0=0.999877499 +x_0=600000 +y_0=200000 +ellps=clrk80ign +pm=paris +towgs84=-168,-60,320,0,0,0,0 +units=m +no_defs +type=crs',
                'list_value_id'=> $crsIds['varcave.list.crs.lambert3'],
                'enabled' => 1,
            ],
            [
                'epsg_code' => 2154,
                'epsg_name' => 'Lambert93',
                'js_handler'   => NULL,
                'proj4_string' => '+proj=lcc +lat_0=46.5 +lon_0=3 +lat_1=49 +lat_2=44 +x_0=700000 +y_0=6600000 +ellps=GRS80 +towgs84=0,0,0,0,0,0,0 +units=m +no_defs +type=crs',
                'list_value_id' => $crsIds['varcave.list.crs.lambert93'],
                'enabled' => 1,
            ],
            [
                'epsg_code'    => 326,
                'epsg_name'    => 'UTM/WGS84',
                'js_handler'   => '/lib/varcave/proj4-crs-handler/326xx.js',
                'proj4_string'  => null,
                'list_value_id'=> $crsIds['varcave.list.crs.utm'],
                'enabled' => 1,
            ],



        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coordinate_system_handlers', function (Blueprint $table) {
            $table->dropUnique(['epsg_code']);
            $table->dropColumn(['epsg_code', 'epsg_name', 'proj_string','list_value_id']);
        });
    }
};
