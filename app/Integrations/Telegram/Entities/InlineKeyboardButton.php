<?php

namespace App\Integrations\Telegram\Entities;

use App\Integrations\Telegram\Exceptions\CallbackDataTooLongException;
use Illuminate\Contracts\Support\Arrayable;

class InlineKeyboardButton implements Arrayable
{
    /**
     * @var array $params
     */
    private array $params = [];

    /**
     * InlineKeyboardButton constructor.
     *
     * @param string $text
     *
     * @return void
     */
    private function __construct(string $text)
    {
        $this->params['text'] = $text;
    }

    /**
     * Makes a new instance of this class.
     *
     * @param string $text
     *
     * @return static
     */
    public static function make(string $text): self
    {
        return new self($text);
    }

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

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return $this->params;
    }
}
