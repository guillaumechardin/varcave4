<?php
namespace App\Services;

use App\Services\SettingsService;
use Illuminate\Support\Facades\Auth;

class UserPreferenceService
{
    public static function get(string $key, string $defaultSetting)
    {
        $user = Auth::user();
        if ($user && isset($user->{$key})) {
            return $user->{$key};
        }
        $globalSetting = new SettingsService();
        return $globalSetting->get($defaultSetting);
    }
}