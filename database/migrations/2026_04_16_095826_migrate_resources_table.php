<?php

use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //rename legacy table
        Schema::dropIfExists('file_resources');
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
            $table->text('original_file_name');
            $table->text('description');
            $table->text('access_rights');
            $table->boolean('is_hidden');
            $table->integer('sort_order')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('file_resource_group_id')->references('id')->on('file_resource_groups')->restrictOnDelete();
        });       


        //add resource-admin role
        Role::create([
            'name' => 'resource-admin',
            'description' => null,
            'created_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_resources');
        Schema::dropIfExists('file_resource_groups');
        DB::table('roles')->where('name', 'resource-admin')->delete();
    }
};
