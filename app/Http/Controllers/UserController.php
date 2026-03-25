<?php

namespace App\Http\Controllers;

use App\Helpers\VarcaveApiResponse;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\File;

class UserController extends Controller
{
    //
    public function index(Request $request)
    {
        Log::debug(__METHOD__ . ' called.');
        $user_cols = ['username', 'firstname', 'lastname', 'id', 'expires_at'];
        $users = User::select($user_cols)
            ->orderBy('id')//->limit(10) //for tests purpose
            ->get();

        $users = $users->toArray();
        $datatablesLang = json_encode(__('varcave.searchPage.datatables'), JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE) ;

        $roles = Role::all()->toArray();

        $expirationDate = Carbon::now()->addYear()->setMonth(1)->setDay(31)->startOfDay()->format('d/m/Y');

        return view('varcave.admin.users',
            [
                'users' => $users,
                'user_cols' => $user_cols,
                'datatablesLang' => $datatablesLang,
                'roles' => $roles,
                'expirationDate' => $expirationDate,
            ]
        );
    }

    /**
     * return a view containing user form to be inserted into 
     * modal form to edit user details
     */
    public function getUserModalForm(User $user, Request $request)
    {
        Log::debug(__METHOD__ . ' called.');
        Log::info('Fecth user details:' . $user->id);

        if ($request->user()->cannot('fetchUser', $user) ){
            abort(403);
        }

        return view('varcave.admin.user-modal-form',[
            'user' => $user,
            'editableItems' => [
                'username' => 'string', 
                'firstname' => 'string', 
                'lastname' => 'string', 
                'email' => 'email', 
                'caving_group' => 'string', 
                'eula_accepted' => 'bool', 
                'password' => 'string', 
            ],
        ]);
    }

    /**
     * return a html view containing user roles to be inserted into 
     * modal form to edit
     */
    public function getRoleModalForm(User $user, Request $request)
    {
        Log::debug(__METHOD__ . ' called.');
        Log::info('Fecth user roles:' . $user->id);

        if ($request->user()->cannot('fetchUser', $user) ){
            abort(403);
        }

        $allRoles = Role::all();
        $userRoles = $user->roles(); 

        $availableRoles = $allRoles->whereNotIn(
            'id',
            $userRoles->pluck('roles.id')
        )->values();

        return view('varcave.admin.user-roles-form',[
            'user' => $user,
            'userRoles' => $userRoles->get()->toArray(),
            'availableRoles' => $availableRoles->toArray(),
        ]);
    }


    

    /**
     * Delete a User ressources
     */
    public function destroy(User $user, Request $request)
    {
        Log::debug(__METHOD__ . ' called.');
        Log::info('Start user delete:' . $user->id . " ($user->username)");
        
        $username = $user->username;
        $userid = $user->id;
        $user->deleteOrFail();
        Log::info('User deletion complete');

        return VarcaveApiResponse::ajaxResponse(
            'success',
            Str::ucfirst(__('varcave.users.deleted_title')),
            Str::ucfirst(__('varcave.users.deleted_msg', ['username' => $username])),
            $userid,
        );
    }

    /**
     * Delete a User ressources
     */
    public function save(User $user, Request $request)
    {
        Log::debug(__METHOD__ . ' called.');
        Log::info('Update user private data :' . $user->id . " ($user->username)");

        $input = $request->all();
        if (isset($input['no-expiry']) && $input['no-expiry'] === 'on') {
            $input['no-expiry'] = true;
        }
        else{
            $input['no-expiry'] = false;
        }

        if (isset($input['expires_at']) && $input['expires_at'] === '') {
            $input['expires_at'] = null; //force null value if empty string
        }

        if (isset($input['eula_accepted']) && $input['eula_accepted'] === 'on') {
            $input['eula_accepted'] = true;
        }
        else{
           $input['eula_accepted'] = false;
        }

        //$inputs = $request->all();
        $validated = Validator::make($input,[
            'username' => 'sometimes|string|max:255',
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'expires_at' => ['nullable', 'date_format:d/m/Y'], // only accept jj/mm/aaaa
            'password' => 'nullable|string|min:8',
            'caving_group' => 'nullable|string|max:255',
            'no-expiry' => 'sometimes|boolean',
            'eula_accepted' => 'sometimes|boolean',
            
        ])->validate();

        if($validated['no-expiry'] ) {
            $validated['expires_at'] = null;
        }elseif( !empty($validated['expires_at']) ){
            $validated['expires_at'] = Carbon::createFromFormat('d/m/Y', $validated['expires_at'])->format('Y-m-d 00:00:00');
        }

        //special case username
        if (!empty($validated['username'])) {
             $user->username = $validated['username'];
        }
        $user->firstname = $validated['firstname'];
        $user->lastname = $validated['lastname'];
        $user->email = $validated['email'];
        $user->expires_at = $validated['expires_at'];
        $user->caving_group = $validated['caving_group'];

        if($validated['eula_accepted'] && !$user->eula_accepted){ //update only if user have never accepted
            $user->eula_accepted = $validated['eula_accepted'];
            $user->eula_accepted_at = now();
        }
        else if(!$validated['eula_accepted']){
            $user->eula_accepted = 0;
            $user->eula_accepted_at = null;
        }
         


        //special password case
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();
        $user->refresh();
        Log::info('User save complete');

        return VarcaveApiResponse::ajaxResponse(
            'success',
            Str::ucfirst(__('varcave.users.save_title')),
            Str::ucfirst(__('varcave.users.save_msg', ['username' => $user->username])),
            $user,
        );
        
    }

    public function roleSave(User $user, Request $request)
    {   
        Log::debug(__METHOD__ . ' called.');
        Log::info('Update user role :' . $user->id . " ($user->username)");

        $validated = $request->validate([
            'userid' => 'required|integer|exists:users,id',
            'roles' => 'sometimes|nullable|array',
            'roles.*' => 'integer|exists:roles,id'
        ]);

        Log::debug('Roles:', [$validated]);
        $_user = User::findOrFail($validated['userid']);
        $_user->roles()->sync($validated['roles'] ?? [], true);

        return VarcaveApiResponse::ajaxResponse(
            'success',
            Str::ucfirst(__('varcave.general.opSuccess')),
            Str::ucfirst(__('varcave.users.role_saved', ['username' => $user->username])),
           $_user->roles()->get()->toArray(),
        );
    }

    public function import(User $user, Request $request)
    {
        Log::debug(__METHOD__ . ' called.');
        Log::info('Bulk update from csv file (' . $user->username .')');

        $validated = $request->validateWithBag('import', [
            'csv-file' => ['required','file','mimetypes:text/plain,text/csv'],
            'import-expires-at' => 'date_format:d/m/Y',
        ]);

        //change date format to be compat with mysql
        $validated['import-expires-at'] = Carbon::createFromFormat('d/m/Y', $validated['import-expires-at'])->format('Y-m-d 00:00:00');

        
        $file = $request->file('csv-file');
        $contents = file_get_contents($file->getRealPath());

        //check utf8 encoding
        $encoding = mb_detect_encoding($contents, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);

        Log::debug('CSV encoding is:'.$encoding);
        if ($encoding !== 'UTF-8') {
            return redirect()->back()
                ->withErrors(['csv-file' => 'Le fichier doit être encodé en UTF-8'], 'import');
        }

        $handle = fopen($file->getRealPath(), 'r');

        /** 
         * start user sync. 
         *   Add users that does not exists
         *   change expiration date for existing one
         */
        $count = 0;
        $failed = 0;
        $addedUsers = 0;
        $updatedUsers = 0;
        while (($line = fgetcsv($handle, 600, ';')) !== false) {
            if( count($line) != 6 ){
                $count++;
                $failed++;
                Log::error('Not enought fields in line: ' . $count  . ' line size:' . count($line) .' skipping');
                continue;
            }
            $headers = [
                'username',
                'password',
                'firstname',
                'lastname',
                'email',
                'organisation',
            ];
            $line = array_combine($headers, $line);

            $user = User::where('username', $line['username'])->first();
            if($user){
                Log::debug(' Update user ' . $line['username']);
                $user->firstname = $line['firstname'];
                $user->lastname = $line['lastname'];
                $user->email = $line['email'];
                $user->caving_group = $line['organisation'];
                $user->expires_at = $validated['import-expires-at'];
                $user->addRole('user');
                $user->save();
                
                $updatedUsers++;
            }
            else{
                Log::info(' Add user :' . $line['username']);
                $user = new User();
                $user->username     = $line['username'];
                $user->password     = Hash::make($line['password']);
                $user->firstname    = $line['firstname'];
                $user->lastname     = $line['lastname'];
                $user->email        = $line['email'];
                $user->caving_group = $line['organisation'];
                $user->expires_at = $validated['import-expires-at'];
                $user->addRole('user');
                $user->save();
                
                $addedUsers++;
            }
            $count++;
        }   

        Log::debug("processed total: $count ; added:$addedUsers ; updated:$updatedUsers");

        return redirect(route('varcave.users.index'))->with('upload-csv-success', __('varcave.users.import_results', [
            'total' => $count,
            'added' => $addedUsers,
            'updated' => $updatedUsers,
            'failed' => $failed,
        ]));
    }
}
