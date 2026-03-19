<?php 
namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class JsLangFileServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Generate a JS file with Laravel translations.
     *
     * @param string $locale Locale to use (e.g., 'fr')
     * @param string $jsFilePath Full path to the output JS file
     * @param array $files Optional array of translation file names to include
     */
    function boot(): void
    {
        $settings['locale'] = App::currentLocale();
        $settings['fallbackLocale'] = config('app.fallback_locale');
        $settings['lang_path'] = lang_path();
        
        $settings['locale_lang_file'] = $settings['lang_path'] . '/' . $settings['locale'] . '/varcave.php';
        $settings['locale_jsFilePath'] = public_path('varcave/langcache/lang.' . $settings['locale'] . '.js');
        $settings['fallback_lang_file'] = $settings['lang_path'] . '/' . $settings['fallbackLocale'] . '/rigmgr.php' ;

        //Log::debug('Language file settings:' . print_r($settings, true));

        //$langPath = $settings['lang_path'] ?? null;
        $rigmgrLangFiles = [];

        if (!empty( $settings['lang_path']) && is_dir( $settings['lang_path'])) {
            // Scan all first-level subdirectories
            foreach (glob( $settings['lang_path'] . '/*', GLOB_ONLYDIR) as $subDir) {
                $filePath = $subDir . '/rigmgr.php';

                // Check if rigmgr.php exists inside this subdirectory
                if (file_exists($filePath)) {
                    $langCode = basename( dirname($filePath) );
                    $jsFilePath = public_path('varcave/langcache/lang.' . $langCode . '.js');
                    $rigmgrLangFiles[$langCode] = [
                        'phpFilePath' => $filePath,
                        'mtime_phpFile' => filemtime($filePath),
                        'jsFilePath' => $jsFilePath,
                        'mtime_jsFile' => file_exists($jsFilePath) ? filemtime($jsFilePath) : 0,
                    ];
                }
            }
        }

        foreach($rigmgrLangFiles as $key => $file)
        {
            Log::debug('check changes on [' . $key .'] language');
            if($file['mtime_jsFile'] < $file['mtime_phpFile'])
            {
                Log::debug('JsFile is outdated!');
                $languageArray = include $file['phpFilePath'];
                $fallbackArray = file_exists($settings['fallback_lang_file']) ? include $settings['fallback_lang_file'] : [];

                // Merge fallback with locale (locale has precedence)
                $merged = array_replace_recursive($fallbackArray, $languageArray);

                // Convert to JS object string
                $jsContent = "i18n = " . json_encode($merged, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . ";";
                //die("<html><body><script>".print_r($jsContent,true)."</script></body></html>");

                File::put($file['jsFilePath'], $jsContent);
                $locale_jsFileHTML='<script src="' . $file['jsFilePath'] . '"></script>';
                continue;
            }
            else{
                Log::debug('JsFile is up to date');
                continue;
            }
        }
    }
}