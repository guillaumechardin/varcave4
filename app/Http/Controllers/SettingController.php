<?php

namespace App\Http\Controllers;

use App\Helpers\VarcaveApiResponse;
use App\Models\Setting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SettingController extends Controller
{
    public function show(Request $request): View
    {
        Log::debug(__METHOD__ . ' called.');
        $rawSettings = DB::table('settings')
                        ->orderBy('category', 'asc')
                        ->get();
        
        $stdSettings = array();
        foreach($rawSettings as $s){
            $stdSettings[$s->category][] = $s;
        }

        return view('varcave.admin.settings',
            [
                'pageTitle' => Str::ucfirst(__('varcave.settings.title')),
                'stdSettings' => $stdSettings,
            ]
        );
    }

    public function update(Request $request, Setting $setting)
    {
        Log::debug(__METHOD__ . ' called.');

        $type = $setting->type;
        if($setting->type =='list'){
            $type = 'string';
        }

        $validated = $request->validate([
            'value' => ['required', $type],
        ]);

        Log::info('Update setting '. $setting->name . ' with value: '. Str::limit($validated['value'], 15));
        $setting->setValueAttribute($validated['value']);
        $setting->save();

        return VarcaveApiResponse::ajaxResponse(
                'success',
                Str::ucfirst(__('varcave.general.opSuccess')),
                Str::ucfirst(__('varcave.settings.settings_saved')),
                $validated['value'],
        );
    }

    public function supportinfo()
    {
        return view('varcave.admin.supportinfo');
    }
}
