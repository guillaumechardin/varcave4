<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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


        //inject default user admin
        DB::unprepared('INSERT INTO users (username, firstname, lastname, email, password, created_at, updated_at)
            VALUES (
                \'admin\',
                \'Admin\',
                \'Admin\',
                \'email@host.com\',
                \'$2y$12$ToCxlRNWAQJKR44hssHS5eC5trIHaZgwVk0qnkSzPC3Z1Ahkn.0Aa\', -- speleo2025
                NOW(),
                NOW()
            )');

        //change/shorten default site name
        DB::unprepared('UPDATE settings SET value = \'Fichier des cavités du Var\' WHERE name=\'websiteFullName\'') ;

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
