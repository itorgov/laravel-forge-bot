<?php

namespace App\Integrations\Telegram\Entities;

class InlineKeyboard extends Keyboard
{
    /**
     * Adds a button to the last row of the keyboard.
     *
     * @param InlineKeyboardButton $button
     *
     * @return $this
     */
    public function button(InlineKeyboardButton $button): self
    {
        return parent::addButtonToRow($button);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return [
            'inline_keyboard' => $this->rows,
        ];
    }
}
