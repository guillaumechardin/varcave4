<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class VarcaveApiResponse
{
    

    public static function ajaxResponse(
                                        string $status,
                                        string $message,
                                        string $title,
                                        mixed $data,
                                        int $code = 200,
                                        string $dataDescr = '',
                                        string $redirecturl = ''){

        Log::debug('Build browser response');

        return response()->json(
            [
                'status' => $status,
                'message' => $message,
                'title' => $title,
                'dataDescr' => $dataDescr,
                'data' => $data,
                'redirecturl' => $redirecturl,
            ],
            $code
        );
    }
}

?>