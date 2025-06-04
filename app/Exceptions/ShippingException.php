<?php

namespace App\Exceptions;

use Exception;

class ShippingException extends Exception
{
    protected $errors = [];

    public function __construct($message = "", $errors = [], $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $this->getMessage(),
                'errors' => $this->getErrors()
            ], 422);
        }

        return back()->withErrors($this->getErrors())
            ->with('error', $this->getMessage());
    }
} 