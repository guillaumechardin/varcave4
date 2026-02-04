<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;


class Page extends Model
{
    protected $fillable = ['key', 'description'];
    protected $pageModel = null;
    protected $modelFields = [];
    protected $listNames = [];


    /**
     * Retrieve a Page configuration with all fields to be displayed for a given page context.
     *
     * @param  string  $page  Logical page identifier (e.g. 'display', 'pdf', 'edit', 'search')
     * @param  string  $sectionKey  section identifier ('main')
     * @param  array   forceAddField required value format : ['field_key_name' => string $keyName,
     *                                                        'sort_order' => int $sortOrder]
     * @return Page
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function setPageModelFor(string $page, string $sectionKey, bool $forceUuid = false,  array|bool $forceAddField = false): void
    {
        $pageModel = Page::where('key', $page)
            ->with(['pageFields' => fn($q) => 
                $q->where('section_key', $sectionKey)
                ->where('is_visible', 1)
                ->orderBy('sort_order','asc')
                ->with('field')
            ])
            ->firstOrFail();

        if ($forceAddField !== false) {
                $pageModel->pageFields->push(
                    self::makeRuntimePageField(
                        $page,
                        $sectionKey,
                        new Field()->where('key', (string) $forceAddField['field_key_name'])->firstOrFail(),
                        (int) $forceAddField['sort_order']
                    )
                );
        }
    
        $this->pageModel = $pageModel;
        $this->setModelFields($forceUuid);
    }       

    /**
     * pageField relation
     */
    public function pageFields()
    {
        return $this->hasMany(PageField::class, 'page_key', 'key')
                    ->with('field');        // automatic load Field
                    //->orderBy('sort_order','desc');          
    }

    protected static function makeRuntimePageField(
        string $pageKey,
        string $sectionKey,
        Field $field,
        int $sortOrder
        ): PageField {
            $pageField = new PageField([
                'page_key'    => $pageKey,
                'section_key' => $sectionKey,
                'is_visible'  => 1,
                'sort_order'  => $sortOrder,
            ]);

            // Relation Field injectée manuellement (pas de field_id persisté)
            $pageField->setRelation('field', $field);

            return $pageField;
    }

    /*
     * return a list of formated cave data from their respective display page
     * 
     */
    protected function setModelFields(bool $forceUuid = false): void
    {
        Log::debug(__METHOD__ . ' called.');
        
        $currentmodelFields = [];
        foreach($this->pageModel->pageFields as $pf => $value){
            $currentmodelFields[ $value['field']['key'] ] = [
                'i18n_label' => __('varcave.table_cave.' . $value['field']['key'] ),
                'data_type'  => $value['field']['data_type'],
                'unit'       => $value['field']['unit'],
                'storage_type' => $value['field']['storage_type'],
            ];

            
            if($value['field']['storage_type'] === 'list'){
                $this->listNames[] = $value['field']['key'];
                $listValues = [];

                $listValues = ListValue::whereIn('list_name', [$value['field']['storage_target']])->get();
                
                $list = array();
                foreach( $listValues->toArray() as $listItem){
                    
                    $list[ $listItem['value'] ] = $listItem['i18n_key']  ;
                }
               $currentmodelFields[ $value['field']['key'] ]['list_values'] = $list;
            }
            
        }
        
        //add Uuid 
        if($forceUuid) {
            $currentmodelFields['uuid'] = [
                'i18n_label' => 'uuid',
                'data_type' => 'uuid',
                'unit' => null,
                'storage_type' => null,
            ];
        }

        $this->modelFields = $currentmodelFields;
    }

    public function getPageModel(){
        return $this->pageModel;
    }

    public function getModelFields(){
        return $this->modelFields;
    }

    public function getListNames(){
        return $this->ListNames;
    }
    
}

