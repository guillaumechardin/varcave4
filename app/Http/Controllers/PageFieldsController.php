<?php

namespace App\Http\Controllers;

use App\Helpers\VarcaveApiResponse;
use App\Models\Page;
use App\Models\PageField;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PageFieldsController extends Controller
{
    public function show(Request $request): View
    {
        Log::debug(__METHOD__ . ' called.');

        $pagesName = Page::all()
        ->pluck('key')
        ->toArray();

        $pageFields = array();
        foreach($pagesName as $page){
            $pageFields[$page] = PageField::with('field')
            ->where([
                ['page_key', $page],
                ['section_key', 'main'],
            ])
            ->orderBy('sort_order', 'asc')
            ->get();
        }
        
        
        
        $pagesF  = array();
        foreach($pageFields as $pageName => $page )
        {
            $pfTop = $pfBottom = array();
            foreach($page as $pf)
            {
                if($pf->is_visible == 0)
                {
                    $pfBottom[] = [
                        'id' => $pf->id, 
                        'is_visible' => $pf->is_visible,
                        'sort_order' => $pf->sort_order,
                        'i18n_name' => __('varcave.table_cave.'.$pf->field->key),
                        'field_name' => $pf->field->key,
                        'created_at' => $pf->created_at,
                        'updated_at' => $pf->updated_at,
                    ];
                    continue;
                }else{
                    $pfTop[] = [
                    'id' => $pf->id, 
                    'is_visible' => $pf->is_visible,
                    'sort_order' => $pf->sort_order,
                    'i18n_name' => __('varcave.table_cave.'.$pf->field->key),
                    'field_name' => $pf->field->key,
                    'created_at' => $pf->created_at,
                    'updated_at' => $pf->updated_at,
                    ];
                }
            }
            
            $pagesF[$pageName] = array_merge($pfTop, $pfBottom);
        }

        return view('varcave.admin.pageFields', [
            'pagesName' => $pagesName,
            'pageFields'  => $pagesF ,
        ]);


    }

    public function update(PageField $pagefield, Request $request): JsonResponse
    {
        Log::debug(__METHOD__ . ' called.');
        
        try{
            $pagefield->is_visible = !$pagefield->is_visible ;
            $pagefield->save();

            $html['fieldId'] = $pagefield->id;
            $html['is_visible'] = $pagefield->is_visible;
            $html['page_key'] = $pagefield->page_key;

            $success = 'success';
            $title = Str::ucfirst(__('varcave.general.opSuccess'));
            $msg = Str::ucfirst(__('varcave.page_fields.visiblity_updated'));
            $data = $html;
            $code = 200;
        }
        catch(Exception $e)
        {
            $success = 'fail';
            $title = Str::ucfirst(__('varcave.general.opFailed'));
            $msg = '(' . $e->getMessage() . ')';
            $data = 'null';
            $code = 500;
        }
        
        return VarcaveApiResponse::ajaxResponse(
                $success,
                $title,
                $msg,
                $data,
                $code,
        );
    }

    public function reorder(Request $request): JsonResponse
    {
        Log::debug(__METHOD__ . ' called.');
        $validated = $request->validate([
            'fields.*.id' => ['required', 'integer', 'exists:page_fields,id'],
            'fields.*.sort_order' => ['required', 'integer', 'min:1'],
        ]);

        try{
            foreach($validated['fields'] as $field){
                $pf = PageField::findOrFail($field['id']);
                $pf->sort_order = $field['sort_order'];
                $pf->save();                
            }
            $success = 'success';
            $title = Str::ucfirst(__('varcave.general.opSuccess'));
            $msg = Str::ucfirst(__('varcave.page_fields.sort_order_updated'));
            $data = '';
            $code = 200;
        }
        catch(Exception $e)
        {
            $success = 'fail';
            $title = Str::ucfirst(__('varcave.general.opFailed'));
            $msg = '(' . $e->getMessage() . ')';
            $data = 'null';
            $code = 500;
        }
        
        return VarcaveApiResponse::ajaxResponse(
                $success,
                $title,
                $msg,
                $data,
                $code,
        );
    }
}
