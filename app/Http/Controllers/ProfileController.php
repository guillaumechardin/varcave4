<?php

namespace App\Http\Controllers;

use App\Actions\Fortify\UpdateUserPassword;
use App\Helpers\VarcaveApiResponse;
use App\Models\Cave;
use App\Models\UserBookmark;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    use AuthorizesRequests;

    public function show(Request $request) {
        $asRoleAdmin = $request->user()->hasRole('admin');
        $isSearcher = $request->user()->hasRole('searcher');
        $asTwoRoles = $request->user()->hasRole(['admin', 'user']);
        $asMissingRoles = $request->user()->hasRole(['admin','user', 'dodger']);

        $userBookmarks = $request->user()->bookmarks;
        $bookmarks = array();
        //dd($request->user()->bookmarks);
        foreach($userBookmarks as $bookmark)
        {
            $cave = Cave::getByUuid($bookmark->cave_uuid);
            $bookmarks[] = [
                'id' => $bookmark->id,
                'caveName' => $cave->name,
                'caveUuid' => $cave->uuid,
                'created_at' => $bookmark->created_at,
            ];
        }

        return view('varcave.profile', [
                'user' => $request->user(),
                'bookmarks' => $bookmarks,
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

    public function storeBookmark(Request $request)
    {
        Log::debug(__METHOD__ . ' called.');

        $validated = $request->validate([
            'uuid' => ['required', 'uuid'],
        ]);

        $uuid = $validated['uuid'];
        $user =  $request->user();


        $bookmark = $user->bookmarks()
            ->where('cave_uuid', $uuid)
            ->first();

        if ($bookmark)
        {
            $bookmark->delete();
            $msg = __('varcave.caveshow.caveDelFav');
            $style = 'bi bi-star';

        } 
        else
        {
            $user->bookmarks()->create(['cave_uuid' => $uuid]);
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

    /**
     * Save theme to user 
     */
    public function storeTheme(Request $request)
    {
        Log::debug(__METHOD__ . ' called.');

        $validated = $request->validate([
            'theme' => ['required', 'string', 'in:dark,light,system'],
        ]);
        if($request->user() === null)
        {
            session([ 'theme' => $validated['theme'] ]);
            Log::debug('guest user set theme to session: '.$validated['theme']);
            return true;
        }

        if($validated['theme'] == "system")
        {
            $request->user()->theme = null;
        }
        else {
            $request->user()->theme = $validated['theme'];
        }
        Log::debug('user set theme to: ' . $request->user()->theme);
        
        //prevent tests in header.blade to use a bad session var.
        session([ 'theme' => null ]);
        $request->user()->save();
    }

    public function deleteBookmark(UserBookmark $bookmark, Request $request)
    {
        Log::info('User delete bookmark', ['id' => $bookmark->id]);
        $user =  $request->user();
        $bookmarkID = $bookmark->id;

        //check id ownership
        $this->authorize('deleteBookmark', $bookmark); //from UserBookmarkPolicy

        $bookmark->delete();

        return VarcaveApiResponse::ajaxResponse(
                'success',
                Str::ucfirst(__('varcave.general.opSuccess')),
                Str::ucfirst(__('varcave.profile.bookmark-deleted')),
                ['deletedBookmarkId' => $bookmarkID],
        );
    }

    public function showEULA(Request $request)
    {
       Log::debug(__METHOD__ . ' called.');
       $eula_localized_content = DB::table('eulas')
        ->where('lang', App::getLocale())
        ->first();

        return view('varcave.profile.eula', [
                'pageTitle' => Str::ucfirst(__('varcave.profile.show_eula_title')),
                'user' => $request->user(),
                'eula' => $eula_localized_content,
            ]);

    }

    public function updateEULA(Request $request)
    {
        Log::info('User update EULA', ['username' => $request->user()->username]);
          
        $validated = $request->validate([
            'eula_accepted' =>['required', 'boolean'],
        ]);

        //user update itself
        $user = $request->user();
        $user->eula_accepted = $validated['eula_accepted'];
        $user->eula_accepted_at = now();
        $user->save();

        Log::info('  EULA read status set to' . $validated['eula_accepted']);

        return redirect(
            route('varcave.profile') . '#tab-security')
            ->with('success', __('varcave.general.opSuccess'));
    }

}
