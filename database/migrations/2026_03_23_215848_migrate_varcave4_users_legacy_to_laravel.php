<?php

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->datetime('expires_at')
                ->nullable()
                ->after('eula_accepted_at');
        });

        echo 'delete existing empty usernames';
        DB::table('varcave4_users')
            ->where('username', '')
            ->delete();

        $legacyUsers = DB::table('varcave4_users')
        ->orderBy('indexid', 'asc')
        /*
        ->where('username', 
            'NOT REGEXP',
            '^[A-Z][0-9]{2}-[0-9]{3}-[0-9]{3}$') // seulement les non licenciés/fédérés
        */
        ->get();
        $totalLegacyUsers = count( $legacyUsers->toArray() );

        $i=1;
        echo "\n";
        echo 'nombre de users :' . count($legacyUsers->toArray())."\n";

        $userRole = DB::table('users')->where('name', 'user')->firstOrFail();

        foreach ($legacyUsers as $userLegacy) {
            //simple synchronisation between old and new database
            $newDbUserExists = DB::table('users')
                ->where('username', $userLegacy->username)
                ->exists();

            if($newDbUserExists)
            {
                 echo 'User ' . $userLegacy->username  . "[$newDbUserExists] already exists.\n";
                continue;
            }

            // user does not exist in new table, insert
            echo 'Processing user ' . $i . '/' . $totalLegacyUsers . ': ' . trim($userLegacy->username) . "\n";
            
            $insertId = DB::table('users')->insertGetId([
                'username' => trim($userLegacy->username),
                'firstname' => $userLegacy->firstname,
                'lastname' => $userLegacy->lastname,
                'email' => $userLegacy->emailaddr,
                'password' => $userLegacy->password, //Hash::make($plainPassword);
                'eula_accepted' => 0,
                'eula_accepted_at' => null,
                'expires_at' => $userLegacy->expire ? Carbon::parse($userLegacy->expire) : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            
            //add user as a simple user
            DB::table('role_user')->insert([
                'user_id' => $insertId,
                'role_id' => $userRole->id, //should be `user`
            ]);

            $i++;
        }

        echo 'debut drop';
        //migrate legacy home_annoucement info
        Schema::table('home_announcements', function (Blueprint $table) {
            $table->dropForeign('fk_creator');
            $table->dropForeign('fk_lastEditor'); 
        });

        Schema::table('password_reset', function (Blueprint $table) {
            $table->dropForeign('fk_users_indexid');

        });
        
        
        //migrate home announcement creator fields
        $ids = [];

        $jplLegacy = DB::table('varcave4_users')->where('username', 'jpl')->value('indexid');
        $jplNew    = DB::table('users')->where('username', 'jpl')->value('id');
        $ids[] = [$jplLegacy, $jplNew];

        $guiLegacy = DB::table('varcave4_users')->where('username', 'gui')->value('indexid');
        $guiNew    = DB::table('users')->where('username', 'gui')->value('id');
        $ids[] = [$guiLegacy, $guiNew];

        $rfLegacy = DB::table('varcave4_users')->where('username', 'rfreminet')->value('indexid');
        $rfNew    = DB::table('users')->where('username', 'rfreminet')->value('id');
        $ids[] = [$rfLegacy, $rfNew];

        //replace old creator data
        foreach ($ids as [$legacyId, $newId]) {
            echo 'update legacy creator id:' . $legacyId . ' to new id:' . $newId."\n"; 
            DB::table('home_announcements')
                ->where('creator', $legacyId) // ancien ID dans home_announcements
                ->update(['creator' => $newId]); // remplacer par le nouvel ID
        }
        
        //replace old editor data
        foreach ($ids as [$legacyId, $newId]) {
            echo 'update legacy editor id:' . $legacyId . ' to new id:' . $newId ."\n"; 
            DB::table('home_announcements')
                ->where('last_editor', $legacyId) // ancien ID dans home_announcements
                ->update(['last_editor' => $newId]); // remplacer par le nouvel ID
        }

        //change id types
        Schema::table('home_announcements', function (Blueprint $table) {
            $table->unsignedBigInteger('creator')->change();
            $table->unsignedBigInteger('last_editor')->nullable()->change();
        });

        Schema::table('home_announcements', function (Blueprint $table) {
            $table->foreign('creator')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('last_editor')->references('id')->on('users')->onDelete('cascade');
        });

        //drop legacy table
        Schema::dropIfExists('password_reset'); //legacy token system
        Schema::dropIfExists('varcave4_users');
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //Schema::dropIfExists('users');
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['expires_at']);
        });
        
        DB::statement('DELETE FROM `users` WHERE ID > 5');
        
    }
    
};
