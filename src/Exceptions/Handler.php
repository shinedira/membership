<?php

namespace Timedoor\TmdMembership\Exceptions;

use Exception;
use Illuminate\Support\Arr;

class Handler extends Exception
{
    /**
     * Convert the given exception to an array.
     *
     * @param  \Throwable  $e
     * @return array
     */
    protected function convertExceptionToArray()
    {
        return [
            'message'   => $this->message,
            'exception' => get_class($this),
            'file'      => $this->getFile(),
            'line'      => $this->getLine(),
            'trace'     => collect($this->getTrace())->map(function ($trace) {
                return Arr::except($trace, ['args']);
            })->all(),
        ];
    }
}
