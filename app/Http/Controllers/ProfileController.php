<?php

namespace App\Http\Controllers;

use App\Actions\Fortify\UpdateUserPassword;
use App\Helpers\VarcaveApiResponse;
use App\Models\Cave;
use App\Models\ListValue;
use App\Models\Setting;
use App\Models\UserBookmark;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
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

        $settingNames = Setting::getValue('user_overridable_settings');
            $rawSettings = DB::table('settings')
                        ->whereIn('name', $settingNames)
                        ->orderBy('category', 'asc')
                        ->get();
        
        $availableSettings = array();
        foreach($rawSettings as $s){
            $availableSettings[$s->category][] = $s;
        }


        $listSettings = Setting::where('type', 'list')->get(['name', 'type', 'value']);
        //build a list of available settings
        $lists = [];
        foreach($listSettings as $list){
            $listValueName = 'setting.' . $list['name'];
            //then fetch all available list option for each list
            $listValues = ListValue::where('list_name', $listValueName)->get()->toArray();
            $listValuesA[] = $listValues;
            foreach($listValues as &$lv){
                if($lv['i18n_key'] != null && (isset($lv['i18n_key']) && Lang::has($lv['i18n_key'])) ){
                    $lv['i18n_key'] = Str::upper(__($lv['i18n_key']));
                }else{
                    $lv['i18n_key'] = $lv['value'];
                }
            }
            $lists[$listValueName] = $listValues; 
        }  

        return view('varcave.profile', [
            'pageTitle' => __('varcave.profile.page_title'),
            'user' => $request->user(),
            'bookmarks' => $bookmarks,
            'roles' => $request->user()->getRoles(),
            'userSettings' => $request->user()->site_settings ?? [] ,
            'availableSettings' => $availableSettings,
            'listsDetails' => $lists,
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

    public function updatePreference(Request $request)
    {
        Log::info('User update prefs', ['username' => $request->user()->username]);

        $fields = json_decode(Setting::get('user_overridable_settings'));
        $settings = Setting::whereIn('name', $fields)->get();

        $setting = $settings->firstWhere('name', $request->prefName);

        $validatedReset = $request->validate([
            'reset' => [
                'sometimes',
                'accepted',
            ],
        ]);

        //fetch user prefs
        $user = $request->user();
        $userPrefs = $user->preferences;


        if(isset($validatedReset['reset']) && (bool)$validatedReset['reset'] == true){
            $user->preferences = null;
            $user->save();

            return VarcaveApiResponse::ajaxResponse(
                'success',
                Str::ucfirst(__('varcave.general.opSuccess')),
                Str::ucfirst(__('varcave.profile.pref_saved')) . '. ' . __('varcave.general.redirecting') ,
                'null',
                200,
                '',
                route('varcave.profile'),
            );
        }
        

        //bad user request, unauthorized pref name
        if(!$setting){
            abort(422, 'Invalid preference');
        }

        $rules = [
            'prefName' => [
                'required',
                Rule::in($fields),
            ],
            'prefValue' => [
                'required',
            ],
        ];


        //custom validation rule (to move to form request ?)
        //special case for list, that is not strictly a laravel list validator, but a ref to List table
        if ($setting->type === 'list'){
            $validationArray = ListValue::where('list_name', 'setting.' . $setting->name)
                ->pluck('value')
                ->toArray();

            $rules['prefValue'][] = Rule::in($validationArray);
        }else{
            $rules['prefValue'][] = $setting->type;
        }
        $validated = $request->validate($rules);

        //merge/replace with new value
        
        $userPrefs[$validated['prefName']] = $validated['prefValue'];
        $user->preferences = $userPrefs;
        $user->save();

        return VarcaveApiResponse::ajaxResponse(
                'success',
                Str::ucfirst(__('varcave.general.opSuccess')),
                Str::ucfirst(__('varcave.profile.pref_saved')),
                'null',
        );
    }

}
