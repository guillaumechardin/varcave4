<?php
namespace App\Services;

use App\Services\SettingsService;
use Illuminate\Support\Facades\Auth;

class UserPreferenceService
{
    public static function get(string $key)
    {
        $user = Auth::user();
        $userPrefs = $user->preferences;
        
        if ($user && isset($userPrefs[$key])) {
            return $userPrefs[$key];
        }
        
        $globalSetting = new SettingsService();
        return $globalSetting->get($key);
    }
}