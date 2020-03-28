<?php

namespace App\Exceptions\Dialogs;

use RuntimeException;
use Throwable;

class DialogClassNotFoundException extends RuntimeException
{
    public function __construct($className = '', $code = 0, Throwable $previous = null)
    {
        $message = sprintf('Dialog class "%s" not found! Please make sure the class exists.', $className);

        parent::__construct($message, $code, $previous);
    }
}
