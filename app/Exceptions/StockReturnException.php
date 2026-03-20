<?php

namespace App\Exceptions;

use Exception;
use Throwable;


class StockReturnException extends Exception
{
    //
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof StockReturnException) {
            return response()->json([
                'status' => false,
                'message' => $exception->getMessage()
            ], 422);
        }

        return parent::render($request, $exception);
    }
}
