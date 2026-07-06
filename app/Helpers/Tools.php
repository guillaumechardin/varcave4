<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class Tools
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

   /**
     * Returns the Bootstrap Icons class name associated with a file extension.
     *
     * @param string $ext File extension (e.g. "pdf", "jpg", "docx")
     * @return string Bootstrap Icons CSS class name corresponding to the given extension
     */
    public static function getBiIcon(string $ext): string
    {
        Log::debug(__METHOD__ . ' called.');

        switch ($ext) {
            case 'jpeg':
            case 'jpg':
            case 'png':
                return 'bi bi-file-image';

            case 'txt':
                return 'bi bi-file-text';

            case 'pdf':
                return 'bi bi-file-pdf';

            case 'xls':
            case 'xlsx':
                return 'bi bi-file-earmark-excel';

            case 'doc':
            case 'docx':
                return 'bi bi-file-earmark-word';

            case 'ppt':
            case 'pptx':
                return 'bi bi-file-earmark-ppt';

            case 'zip':
                return 'bi bi-file-earmark-zip';

            case 'odt':
                return 'bi bi-file-earmark-text';

            case 'gpx':
            case 'trk':
            case 'wpt':
                return 'bi bi-geo-alt';

            default:
                return 'bi bi-file-binary';
        }

    }
}
