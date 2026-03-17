<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    //
    public function index(Request $request)
    {
        Log::debug(__METHOD__ . ' called.');
        $users = User::all()
            ->select(['username', 'firstname', 'lastname']);

        return view('varcave.admin.users',
            [
                'users' => $users,
            ]
        );
    }
}
