<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Blade;
use App\Actions\Fortify\UpdateUserPassword;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    public function setTheme()
    {


    }

    public function show(Request $request) {
        $asRoleAdmin = $request->user()->hasRole('admin');
        $isSearcher = $request->user()->hasRole('searcher');
        $asTwoRoles = $request->user()->hasRole(['admin', 'user']);
        $asMissingRoles = $request->user()->hasRole(['admin','user', 'dodger']);
        return view('varcave.profile', [
                'user' => $request->user(),
                'fillable' => $request->user()->getFillable(),
                'asRoleAdmin' => $asRoleAdmin,
                'asTwoRoles' => $asTwoRoles,
                'asMissingRoles' => $asMissingRoles,
                'isSearcher' => $isSearcher,
                'roles' => $request->user()->getRoles(),
            ]);
    }

    public function showUpdatePassword(){
        return view('varcave.update-password');
    }

    public function updatePassword(Request $request){
        Log::debug('User obj:', [$request->user()]);
        Log::debug('inputs:', [$request->input()]);
        try{
            $updateUserPassword = new UpdateUserPassword();
            $updateUserPassword->update($request->user(), $request->input());
            return redirect()
                ->route('varcave.profile')
                ->with('status', 'password-updated');
        }
        catch(Exception $e)
        {
            Log::error('Echec de la modif', [$e->getMessage()]);
            throw ValidationException::withMessages([
                'password' => $e->getMessage(),
            ]);
        }
    }

}
