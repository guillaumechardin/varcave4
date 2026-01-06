<?php
namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    protected array $settings = [];

    public function __construct()
    {
        $useCache = false; // toggle cache ON/OFF pour tester


        if ($useCache) {
            // Cached Settings
            $this->settings = Cache::remember('settings.all', 3600, function() {
                //Log::debug('[SettingsService] Fetch from DB (cache miss)');
                return Setting::all()->pluck('value', 'name')->toArray();
            });
        } else {
            // Direct DB call (no cache)
            //Log::debug('[SettingsService] Direct fetch from DB');
            $this->settings = Setting::all()->pluck('value', 'name')->toArray();
        }

    }


    public function get(string $name, $default = null)
    {
        return $this->settings[$name] ?? $default;
    }

    public function set(string $name, $value): void
    {
        Setting::updateOrCreate(['name' => $name], ['value' => $value]);
        // update cache
        $this->settings[$name] = $value;
        Cache::put('settings.all', $this->settings, 3600);
    }
}
