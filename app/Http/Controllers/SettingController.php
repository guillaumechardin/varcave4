<?php

namespace App\Http\Controllers;

use App\Helpers\VarcaveApiResponse;
use App\Models\ListValue;
use App\Models\Setting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
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

        //each setting with list type must have a name related to list_values 
        // ie: setting name: my_setting is related to list_values: settings.my_setting
        $listSettings = Setting::where('type', 'list')->get(['name', 'type', 'value']);
        //build a list of available settings
        $lists = [];
        foreach($listSettings as $list){
            $listValueName = 'setting.' . $list['name'];
            //then fetch all available list option for each list
            $listValues = ListValue::where('list_name', $listValueName)->get()->toArray();
            $listValuesA[] = $listValues;
            foreach($listValues as &$lv){
                if(isset($lv['i18n_key']) && Lang::has($lv['i18n_key']) ){
                    $lv['i18n_key'] = Str::upper(__($lv['i18n_key']));
                }
            }
            $lists[$listValueName] = $listValues; 
        }     


        return view('varcave.admin.settings',
            [
                'pageTitle' => Str::ucfirst(__('varcave.settings.title')),
                'stdSettings' => $stdSettings,
                'listsDetails' => $lists,
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
