<?php

namespace App\Integrations\Telegram\Entities;

use Illuminate\Contracts\Support\Arrayable;

class BotCommand implements Arrayable
{
    /**
     * @var string
     */
    private string $name;

    /**
     * @var string
     */
    private string $description;

    public function __construct(string $name, string $description)
    {
        $this->name = $name;
        $this->description = $description;
    }

    /**
     * Returns name of the command.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns description of the command.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return [
            'command' => $this->name,
            'description' => $this->description,
        ];
    }
}
