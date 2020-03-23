<?php

namespace App\Contracts;

use App\Dialog;

interface DialogContract
{
    /**
     * Starts a dialog for the first time.
     *
     * @return static
     */
    public static function start(): self;

    /**
     * Goes to a next step of the dialog.
     *
     * @param Dialog $dialog
     * @param string $message
     *
     * @return static
     */
    public static function next(Dialog $dialog, string $message): self;
}
