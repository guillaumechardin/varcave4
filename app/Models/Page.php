<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = ['key', 'description'];

    /**
     * Retrieve a Page configuration with all fields to be displayed for a given page context.
     *
     * @param  string  $page  Logical page identifier (e.g. 'display', 'pdf', 'edit', 'search')
     * @param  string  $sectionKey  section identifier ('main')
     * @return Page
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public static function pageFieldsFor(string $page, string $sectionKey): Page
    {
        return Page::where('key', $page)
            ->with(['pageFields' => fn($q) => 
                $q->where('section_key', $sectionKey)
                ->where('is_visible', 1)
                ->orderBy('sort_order')
                ->with('field')
            ])
            ->firstOrFail();
    }

    /**
     * pageField relation
     */
    public function pageFields()
    {
        return $this->hasMany(PageField::class, 'page_key', 'key')
                    ->with('field')        // automatic load Field
                    ->orderBy('sort_order');
                    
    }

}
