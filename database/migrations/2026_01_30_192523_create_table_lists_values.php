<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('list_values', function (Blueprint $table) {
            $table->id();

            $table->string('list_name', 100);
            $table->integer('value');

            $table->string('i18n_key')->nullable();

            $table->integer('sort_order')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique(['list_name', 'value'], 'lists_list_name_value_unique');
            $table->index('list_name', 'lists_list_name_index');
        });

        DB::table('list_values')->insert([
        [
            'list_name'  => 'cave.pollution',
            'value'      => 0,
            'i18n_key'   => 'varcave.table_cave.pollution_list.none',
            'sort_order' => 0,
            'is_active'  => true,
            'created_at'=> Carbon::now(),
            'updated_at'=> Carbon::now(),
        ],
        [
            'list_name'  => 'cave.pollution',
            'value'      => 1,
            'i18n_key'   => 'varcave.table_cave.pollution_list.low',
            'sort_order' => 1,
            'is_active'  => true,
            'created_at'=> Carbon::now(),
            'updated_at'=> Carbon::now(),
        ],
        [
            'list_name'  => 'cave.pollution',
            'value'      => 2,
            'i18n_key'   => 'varcave.table_cave.pollution_list.medium',
            'sort_order' => 2,
            'is_active'  => true,
            'created_at'=> Carbon::now(),
            'updated_at'=> Carbon::now(),
        ],
        [
            'list_name'  => 'cave.pollution',
            'value'      => 3,
            'i18n_key'   => 'varcave.table_cave.pollution_list.high',
            'sort_order' => 3,
            'is_active'  => true,
            'created_at'=> Carbon::now(),
            'updated_at'=> Carbon::now(),
        ],
    ]);
    
    //drop legacy table
    DB::statement('ALTER TABLE `coordinate_system_handlers` DROP INDEX `fk_lists_coordsys`');
    Schema::dropIfExists('lists');

    }

    public function down(): void
    {
        Schema::dropIfExists('list_values');
    }
};
