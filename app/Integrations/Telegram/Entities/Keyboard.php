<?php

namespace App\Integrations\Telegram\Entities;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;

abstract class Keyboard implements Arrayable
{
    /**
     * @var array $rows
     */
    protected array $rows = [];

    /**
     * Keyboard constructor.
     *
     * @return void
     */
    protected function __construct()
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
        return new static();
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
     * @param KeyboardButton $button
     *
     * @return $this
     */
    protected function addButtonToRow(KeyboardButton $button): self
    {
        $lastRow = array_pop($this->rows);

        $lastRow[] = $button->toArray();
        $this->rows[] = $lastRow;

        return $this;
    }
}
