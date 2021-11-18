<?php

namespace Timedoor\TmdMembership\Exceptions;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class OtpNotFound extends Handler
{
    public $message = 'Otp Not Found';

    public function __construct()
    {
        parent::__construct($this->message);
    }
    
    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        $errorsArray = [
            'error' => [
                'code'    => Response::HTTP_BAD_REQUEST,
                'title'   => $this->message,
                'message' => 'Cannot find otp from these credential'
            ]
        ];

        if (config('app.debug')) {
            $errorsArray = array_merge($errorsArray, $this->convertExceptionToArray());
        }

        return new JsonResponse(
            $errorsArray,
            Response::HTTP_BAD_REQUEST, [],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );
    }
}
