<?php


use Illuminate\Support\Carbon;
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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('role_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'role_id']); //prevents duplicate role assignments for a user
        });

        $now = Carbon::now();
        DB::table('roles')->insert([
            ['name' => 'user', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'admin', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'cave-editor', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'announcement-editor', 'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('role_user')->insert([
            ['user_id' => '1', 'role_id' => '2', 'created_at' => $now, 'updated_at' => $now], //admin role admin
            ['user_id' => '1', 'role_id' => '1', 'created_at' => $now, 'updated_at' => $now], //admin role user
            ['user_id' => '2', 'role_id' => '1', 'created_at' => $now, 'updated_at' => $now], //user role user
            
        ]);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('roles');
        Schema::dropIfExists('role_user');
        Schema::enableForeignKeyConstraints();
    }
};
