<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\ResetsUserPasswords;

class ResetUserPassword implements ResetsUserPasswords
{
    use PasswordValidationRules;

    /**
     * Validate and reset the user's forgotten password.
     *
     * @param  array<string, string>  $input
     */
    public function reset(User $user, array $input)
    {
        Log::debug('start reset password for user');
        Validator::make($input, [
            'password' => $this->passwordRules(),
        ])->validate();

        //check username is ok and corresponding email address username is `unique` not email
        Log::debug('start reset password for user');
        $exists = User::where('username', $input['username'])
            ->where('email', $input['email'])
            ->exists();
        if($exists == false){
            Log::debug('User does not exists:',[$exists]);
            throw ValidationException::withMessages([
                'username' => __('varcave.auth.user_email_fail', ['email' => $input['email'], 'username' => $input['username'] ]),
            ]);
        }
        Log::debug('validation end');

        $user->forceFill([
            'password' => Hash::make($input['password']),
        ])->save();
    }
}
