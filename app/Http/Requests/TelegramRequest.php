<?php

namespace App\Http\Requests;

use Illuminate\Support\Collection;

class TelegramRequest
{
    /**
     * @var Collection $commands
     */
    public $commands;

    /**
     * TelegramRequest constructor.
     *
     * @return void
     */
    public function __construct()
    {
        logger('Telegram update object', request()->all());

        $this->commands = collect();
        $this->extractCommands();
    }

    /**
     * Extract commands from a telegram update object.
     *
     * @return void
     */
    protected function extractCommands(): void
    {
        $text = request('message.text', '');

        foreach (request('message.entities', []) as $entity) {
            if (data_get($entity, 'type') === 'bot_command') {
                $command = substr(
                    $text,
                    data_get($entity, 'offset', 0),
                    data_get($entity, 'length', 0)
                );

                if (!empty($command)) {
                    $this->commands->push($command);
                }
            }
        }
    }
}
