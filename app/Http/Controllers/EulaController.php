<?php

namespace App\Http\Controllers;

use App\Models\Eula;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class EulaController extends Controller
{
    public function show(Request $request): View
    {
        Log::debug(__METHOD__ . 'called');

        $eulas = Eula::all();

        return view('varcave.admin.eula',
            [
                'eulas' => $eulas,
            ]
        );
    }

    public function update(Eula $eula, Request $request): View
    {
        Log::debug(__METHOD__ . 'called');

        Gate::authorize('admin-access');

        $validated = $request->validate([
            'eula-ids' => ['required', 'exists:eula,id'],
            'eula-content' => ['required', 'string'],
        ]);

       
       try{
            $eula = $this-
       }
        


    }
}
