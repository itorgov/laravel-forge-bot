<?php

namespace App\Integrations\Telegram\Entities;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;

class InlineKeyboard implements Arrayable
{
    /**
     * @var array $rows
     */
    private array $rows = [];

    /**
     * InlineKeyboard constructor.
     *
     * @return void
     */
    private function __construct()
    {
        $this->row();
    }

    /**
     * Makes a new instance of this class.
     *
     * @return static
     */
    public static function make(): self
    {
        return new self();
    }

    /**
     * Adds a new row of buttons to the keyboard.
     *
     * @return $this
     */
    public function row(): self
    {
        $lastRow = Arr::last($this->rows);

        // Prevent to add empty rows.
        if ($lastRow === null || !empty($lastRow)) {
            $this->rows[] = [];
        }

        return $this;
    }

    /**
     * Adds a button to the last row of the keyboard.
     *
     * @param InlineKeyboardButton $button
     *
     * @return $this
     */
    public function button(InlineKeyboardButton $button): self
    {
        $lastRow = array_pop($this->rows);

        $lastRow[] = $button->toArray();
        $this->rows[] = $lastRow;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return [
            'inline_keyboard' => $this->rows,
        ];
    }
}
