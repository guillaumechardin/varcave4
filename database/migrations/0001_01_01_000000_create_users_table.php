<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('firstname');
            $table->string('lastname');
            $table->string('email');
            /**  Varcave legacy fields **/
            $table->tinyText('theme')->nullable(); //tinyText : 255 ; TEXT:65535 ; MEDIUMTEXT:16 777 215
            $table->tinyText('map_layer')->nullable();
            $table->unsignedSmallInteger('datatables_max_items')->nullable();  	
            $table->tinyText('pref_coord_system')->nullable();
            $table->tinyText('caving_group')->nullable();
            $table->tinyText('language')->nullable();
            $table->boolean('eula_accepted')->default(false);
            $table->timestamp('eula_accepted_at')->nullable();
            /******END */

            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });


        //inject simple users
        $users = [
            [
                'username' => 'Admin',
                'firstname' => 'Admin',
                'lastname' => 'ADMIN',
                'email' => 'admin@myhost.local',
                'password' => Hash::make('speleo2025'),
                'created_at' => now(),
                'updated_at' => NULL,
            ],

            [
                'username' => 'User',
                'firstname' => 'User',
                'lastname' => 'user',
                'email' => 'user@myhost.local',
                'password' => Hash::make('testeur25'),
                'created_at' => now(),
                'updated_at' => NULL,
            ],
        ];
         foreach ($users as $user) {            
            DB::table('users')->insert($user);
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
