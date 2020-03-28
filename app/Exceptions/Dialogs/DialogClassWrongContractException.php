<?php

namespace App\Exceptions\Dialogs;

use App\Contracts\DialogContract;
use RuntimeException;
use Throwable;

class DialogClassWrongContractException extends RuntimeException
{
    public function __construct($className = '', $code = 0, Throwable $previous = null)
    {
        $message = vsprintf('Dialog class "%s" should implements "%s"', [
            $className,
            DialogContract::class,
        ]);

        parent::__construct($message, $code, $previous);
    }
}
