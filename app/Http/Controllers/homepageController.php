<?php

namespace App\Http\Controllers;

use App\Models\Cave;
use App\Models\Setting;
use App\Models\CaveFile;
use Illuminate\Http\Request;
use App\Models\caveChangelog;
use App\Models\HomeAnnouncement;

class homepageController extends Controller
{
    function displayHomepage()
    {
        $homeAnnouncements = HomeAnnouncement::latestAnnouncements(Setting::get('max_news_homepage'));
        $caveChangelogs = caveChangelog::lastestCaveChangeLog(Setting::get('welcomePageShowLastUpdate'));
        $caveFiles = CaveFile::get('dc10957e-0e00-4b20-acb5-312a391c4c46', 'photos'); //id maram 1579
        return view('varcave.home', [
            'homeAnnouncements' => $homeAnnouncements,
            'caveChangelogs' => $caveChangelogs,
            'caveFiles' => $caveFiles, 
            'randomCave' => Cave::getFromUuid('dc10957e-0e00-4b20-acb5-312a391c4c46'),
        ]);
    }
}
