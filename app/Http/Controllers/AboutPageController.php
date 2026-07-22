<?php

namespace App\Http\Controllers;

use App\Models\AboutPage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AboutPageController extends Controller
{
    public function show(Request $request): View
    {
        Log::debug(__METHOD__ . 'called');

        $pageContent = AboutPage::find(1, 'html_content');

        $htmlContent = '';
        if(!$pageContent){
            $htmlContent = '';
        }
        else
        {
            $htmlContent = $pageContent->html_content;
        }
        
        return view('varcave.aboutPage', [
            'aboutContent'  => $htmlContent,
        ]);

    }
}
