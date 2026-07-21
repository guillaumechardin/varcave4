<?php

namespace App\Actions\Varcave;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class MigrateLegacyUserPassword
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function __invoke($request, $next)
    {
        Log::debug('Migrating user password');
        $user = User::where(
            config('fortify.username'),
            $request->input(config('fortify.username'))
        )->first();

        if ($user && $this->isSha256($user->password)) {
            Log::info('Try password migration');
            if (hash('sha256', $request->password) == $user->password) {
                $user->password = Hash::make($request->password);
                $user->save();
                Log::info('User password migrated successfully');
            }else{
                Log::error('User authentication failed, abord password migration');
                return redirect(route('login'))->with('migrationError', 'Echec de la migration de l\'utilisateur, mot de passe incorrect<br>Veuillez vous reconnecter');
            }
        }else{
            Log::info('Password is not SHA256, abord migration');
        }

        return $next($request);
    }


    private function isSha256(string $hash): bool
    {
        return preg_match('/^[a-f0-9]{64}$/i', $hash);
    }
}
