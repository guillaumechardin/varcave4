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
        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('is_user_overridable')
            ->after('category')
            ->default(0);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->json('preferences')
            ->after('theme')
            ->nullable();
        });

        $availFields = 
        [
            'pdf_coords_system',
            'include_GPX_details',
            'datatables_max_items',
            'pdf_map_zoom',
            'ol_zoom_map_lvl',
        ];

        DB::table('settings')
        ->insert([
            'name' => 'user_overridable_settings',
            'value' => json_encode($availFields),
            'type' => 'json',
            'legacy_mtime' => 0,
            'category' => 'general',
            'is_advanced_option' => 1,
            'created_at' => now(),
            'updated_at' => null,
        ]);

        DB::table('settings')
        ->where('name', 'datatables_items_selector')
        ->update([
            'value'=> 5,
            'type' => 'list',
            'is_advanced_option' => 0,
        ]);

        $listName = 'setting.datatables_items_selector';
        $values = ["5","10","20","35","50","100"];
        $i=0;
        foreach ($values as $val){
            DB::table('list_values')->insert(
                [
                    'list_name' => $listName,
                    'value' => $val,
                    'i18n_key' => null,
                    'sort_order' => $i,
                    'is_active' => 1,
                    'created_at' => now(),
                    'updated_at' => null,
                ]
            );
            $i++;
        }

        //remove legacy setting
        DB::table('settings')
        ->where('name', 'datatables_max_items')
        ->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('is_user_overridable');
        });

        DB::table('settings')
        ->where('name', 'user_overridable_settings')
        ->delete();

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('preferences');
        });

        DB::table('list_values')
        ->where('list_name', 'setting.datatables_items_selector')
        ->delete();
    }
};
