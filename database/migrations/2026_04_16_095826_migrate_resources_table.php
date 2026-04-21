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
        //rename legacy table
        Schema::rename('file_resources', 'file_resources_legacy');

        Schema::dropIfExists('file_resources');
        Schema::dropIfExists('file_resource_groups');

        Schema::create('file_resource_groups', function (Blueprint $table) {
            $table->id();            
            $table->string('name')->unique();
            $table->integer('sort_order')->nullable();
            $table->timestamps();
        });

        Schema::create('file_resources', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('file_resource_group_id');
            $table->text('name');
            $table->text('file_path');
            $table->text('description');
            $table->text('access_rights');
            $table->boolean('is_hidden');
            $table->integer('sort_order')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('file_resource_group_id')->references('id')->on('file_resource_groups')->restrictOnDelete();
        });

        //migrate data add resource_groups 
        
        DB::table('file_resources_legacy')->select(['display_group'])
            ->distinct()
            ->orderBy('display_group')
            ->chunk(100, function ($rows) {
            foreach ($rows as $row) {
                DB::table('file_resource_groups')->insert([
                    'name' => $row->display_group,
                    'sort_order'      => 0,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }
        });

        //populate main table
        DB::table('file_resources_legacy')->chunkById(100, function ($rows) {
            foreach ($rows as $row) {
                $values = $row->access_rights
                    ? explode(',', $row->access_rights)
                    : [];

                $values = str_replace('anonymous', 'public', $values);
                $accessRightsJson = json_encode($values);

                //dd(var_dump($row));
                DB::table('file_resources')->insert([
                    'file_resource_group_id'  => 1,
                    'name'      => $row->display_name,
                    'file_path'      => $row->filepath,
                    'description'    => $row->description,
                    'access_rights'  => $accessRightsJson,
                    'user_id'        => $row->creator,
                    'is_hidden'      => 0,
                    'sort_order'      => 0,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }
        });

        //add resource-admin role
        DB::table('roles')->insert([
            'name' => 'resource-admin',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_resources');
        Schema::dropIfExists('file_resource_groups');
        Schema::rename('file_resources_legacy', 'file_resources');
        DB::table('roles')->where('name', 'resource-admin')->delete();
    }
};
