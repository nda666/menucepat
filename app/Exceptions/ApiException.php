<?php

namespace App\Exceptions;

use Exception;

class ApiException extends Exception
{
    public function __construct($message = "", $code = 500, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        return response()->json([
            'success' => false,
            'message' => $this->message,
            'data' => null
        ], $this->code);
    }
}
