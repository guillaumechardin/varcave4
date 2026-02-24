<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Helpers\VarcaveApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;
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

    public function storeFavorite(Request $request)
    {
        Log::debug(__METHOD__ . ' called.');

        $validated = $request->validate([
            'uuid' => ['required', 'uuid'],
        ]);

        $uuid = $validated['uuid'];
        $user =  $request->user();


        $favorite = $user->favorites()
            ->where('cave_uuid', $uuid)
            ->first();

        if ($favorite)
        {
            $favorite->delete();
            $msg = __('varcave.caveshow.caveDelFav');
            $style = 'bi bi-star';

        } 
        else
        {
            $user->favorites()->create(['cave_uuid' => $uuid]);
            $msg = __('varcave.caveshow.caveAddToFav');
            $style = 'bi bi-star-fill';
        }
        
        return VarcaveApiResponse::ajaxResponse(
                'success',
                Str::ucfirst(__('varcave.general.opSuccess')),
                Str::ucfirst($msg),
                $style,
        );

    }

    public function storeTheme(Request $request)
    {
        Log::debug(__METHOD__ . ' called.');

        $validated = $request->validate([
            'theme' => ['required', 'string', 'in:dark,light,system'],
        ]);

        $request->user()->theme = $validated['theme'];
        $request->user()->save();
    }

}
