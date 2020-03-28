<?php

namespace App\Integrations\Telegram\Entities;

use Illuminate\Contracts\Support\Arrayable;

class KeyboardButton implements Arrayable
{
    /**
     * @var array
     */
    protected array $params = [];

    /**
     * KeyboardButton constructor.
     *
     * @param string $text
     *
     * @return void
     */
    protected function __construct(string $text)
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
        return new static($text);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return $this->params;
    }
}
