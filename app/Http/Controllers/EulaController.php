<?php

namespace App\Http\Controllers;

use App\Models\Eula;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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

    public function update(Eula $eula, Request $request): RedirectResponse
    {
        Log::debug(__METHOD__ . 'called');

        Gate::authorize('admin-access');

        $validated = $request->validate([
            'eula-content' => ['required', 'string'],
        ]);

            
       try{
            $eula->content = $validated['eula-content'];
            $eula->save();

            $msg = Str::ucfirst(__('varcave.general.opSuccess')) . ': ' . __('varcave.eula.eula_saved');

            return redirect()->back()
                ->with('success', $msg);

       }catch(Exception $e){
            $msg = __('varcave.general.opFailed') . ': ' . $e->getMessage();
            return redirect()->back()
                ->with('error', $msg)
                ->withInput();
       }
        


    }
}
