<?php

namespace Timedoor\TmdMembership\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class OtpExpiredException extends Handler
{
    public $message = 'Token expired';

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
                'message' => 'Token has been expired you can request new otp token.'
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
