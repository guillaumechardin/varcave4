<?php

namespace App\Http\Controllers;

use App\Models\Cave;
use App\Models\CaveChangelog;
use App\Models\CaveFile;
use App\Models\FeaturedCave;
use App\Models\HomeAnnouncement;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HomepageController extends Controller
{
    public function show()
    {
        Log::debug(__METHOD__ . ' called.');
        $homeAnnouncements = HomeAnnouncement::latestAnnouncements(Setting::get('max_news_homepage'));
        $caveChangelogs = CaveChangelog::lastestCaveChangeLog(Setting::get('welcomePageShowLastUpdate'));
        
        //check if already present FeaturedCave
        $lastFeaturedCave = FeaturedCave::where('is_active', 1)->first();
        if($lastFeaturedCave == null)
        {
            $cave = Cave::getRandomWithFile(); //fetch random cave with photo file
            if ($cave == null){
                Log::warning(' no cave with file found');
                $lastFeaturedCave = null;
                $expiresAt = null;
            }
            else{
                $lastFeaturedCave = FeaturedCave::setAsFeatured($cave); 
            }
        }
        
            
        //check if featured cave is expired
        $expiresAt = $lastFeaturedCave->created_at->addSeconds((int)Setting::get('featured_caves_delay'));
        Log::debug('Featured cave refresh in ' . now()->diffInSeconds($expiresAt) . ' s');
        if (now()->greaterThan($expiresAt)){
                Log::info(' >refresh featured cave needed');
                $cave = Cave::getRandomWithFile();
                $lastFeaturedCave = FeaturedCave::setAsFeatured($cave);
        }
        
        //get FeaturedCave details
        $featuredCave = Cave::with('caveFiles')->find($lastFeaturedCave->cave_id);
         
        return view('varcave.homepage', [
            'pageTitle' => __('varcave.homepage.title'),
            'homeAnnouncements' => $homeAnnouncements,
            'caveChangelogs' => $caveChangelogs,
            'featuredCave' => $featuredCave, 
        ]);
    }
}
