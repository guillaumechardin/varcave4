<?php
namespace App\ViewModels;

use LogicException;
use App\Models\Cave;
use App\Models\Page;
use App\Models\Field;
use App\Models\ListValue;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class CaveViewModel
{
    public const AS_ARRAY = 0;
    public const AS_STD_OBJ = 1;
    public const AS_DATATABLES = 2;

    

    protected array $fields;
    protected array $listValues = [];
    protected Page $page;

    

    public function __construct(Page $page)
    {
        $this->page = $page;
    }

    public function render(){
        return false;
    }
}