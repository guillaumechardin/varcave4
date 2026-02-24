<?php

namespace App\Http\Controllers;

use App\Models\Cave;
use App\Models\Setting;
use App\Models\CaveFile;
use Illuminate\Http\Request;
use App\Models\CaveChangelog;
use App\Models\HomeAnnouncement;

class HomepageController extends Controller
{
    function displayHomepage()
    {
        $homeAnnouncements = HomeAnnouncement::latestAnnouncements(Setting::get('max_news_homepage'));
        $caveChangelogs = CaveChangelog::lastestCaveChangeLog(Setting::get('welcomePageShowLastUpdate'));
        $cave = Cave::getByUuid('dc10957e-0e00-4b20-acb5-312a391c4c46');//id maram 1579
        $caveFiles = CaveFile::get($cave, 'photos'); 
        return view('varcave.homepage', [
            'pageTitle' => __('varcave.homepage.title'),
            'homeAnnouncements' => $homeAnnouncements,
            'caveChangelogs' => $caveChangelogs,
            'caveFiles' => $caveFiles, 
            'randomCave' => $cave,
        ]);
    }
}
