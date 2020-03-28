<?php

namespace App\Integrations\Telegram\Entities;

class ReplyKeyboard extends Keyboard
{
    /**
     * @var array
     */
    private array $params = [];

    /**
     * Adds a button to the last row of the keyboard.
     *
     * @param KeyboardButton $button
     *
     * @return $this
     */
    public function button(KeyboardButton $button): self
    {
        return parent::addButtonToRow($button);
    }

    /**
     * Requests clients to resize the keyboard vertically for optimal fit.
     *
     * @param bool $resize
     *
     * @return $this
     */
    public function resizeKeyboard(bool $resize): self
    {
        $this->params['resize_keyboard'] = $resize;

        return $this;
    }

    /**
     * Requests clients to hide the keyboard as soon as it's been used.
     *
     * @param bool $isOneTime
     *
     * @return $this
     */
    public function oneTimeKeyboard(bool $isOneTime): self
    {
        $this->params['one_time_keyboard'] = $isOneTime;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return array_merge($this->params, [
            'keyboard' => $this->rows,
        ]);
    }
}
