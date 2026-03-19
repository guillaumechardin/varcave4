<?php

namespace App\Http\Controllers;

use App\Helpers\VarcaveApiResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController extends Controller
{
    //
    public function index(Request $request)
    {
        Log::debug(__METHOD__ . ' called.');
        $user_cols = ['username', 'firstname', 'lastname', 'id'];
        $users = User::select($user_cols)
            ->orderBy('username')
            ->get();

        $users = $users->toArray();
        $datatablesLang = json_encode(__('varcave.searchPage.datatables'), JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE) ;

        return view('varcave.admin.users',
            [
                'users' => $users,
                'user_cols' => $user_cols,
                'datatablesLang' => $datatablesLang,
            ]
        );
    }

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

}
