<?php

namespace App\Integrations\Telegram\Entities;

use App\Integrations\Telegram\Exceptions\CallbackDataTooLongException;

class InlineKeyboardButton extends KeyboardButton
{
    /**
     * Adds data to be sent in a callback query to the bot when button is pressed, 1-64 bytes.
     *
     * @param string $data
     *
     * @return $this
     */
    public function callbackData(string $data): self
    {
        if (strlen($data) > 64) {
            throw new CallbackDataTooLongException;
        }

        $this->params['callback_data'] = $data;

        return $this;
    }
}
