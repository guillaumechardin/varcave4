<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class VarcaveApiResponse
{
    
    /**
     * Generate a standardized AJAX JSON response.
     *
     * This method creates a structured response suitable for returning
     * from an AJAX request. It includes status, title, message, optional
     * data payload, HTTP status code, description of the data, and
     * optionally a redirect URL.
     *
     * @param string $status       Status of the response (e.g., 'success', 'error')
     * @param string $title        Short title of the response (used for display in UI)
     * @param string $message      Detailed message of the response
     * @param mixed  $data         Optional payload data (array, object, string, etc.)
     * @param int    $code         HTTP status code to return (default 200)
     * @param string $dataDescr    Optional description of the $data contents
     * @param string $redirecturl  Optional URL to redirect the client after success/failure
     *
     * @return \Illuminate\Http\JsonResponse
     *         Returns a JSON response ready to be sent to the client
     *
     * @example
     * return ajaxResponse('success', 'Saved', 'Your changes have been saved.', ['id' => 12]);
     */
    public static function ajaxResponse(
                                        string $status,
                                        string $title,
                                        string $message,
                                        mixed $data,
                                        int $code = 200,
                                        string $dataDescr = '',
                                        string $redirectUrl = ''){

        Log::debug('Build browser response');

        return response()->json(
            [
                'status' => $status,
                'title' => $title,
                'message' => $message,
                'data' => $data,
                'dataDescr' => $dataDescr,
                'redirectUrl' => $redirectUrl,
            ],
            $code
        );
    }
}

?>