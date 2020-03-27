<?php

namespace App\Integrations\Telegram\Dialogs;

use App\Dialog as DialogModel;

abstract class Dialog
{
    protected DialogModel $dialog;

    /**
     * @inheritDoc
     */
    public function isFinished(): bool
    {
        return $this->dialog->finished();
    }
}
