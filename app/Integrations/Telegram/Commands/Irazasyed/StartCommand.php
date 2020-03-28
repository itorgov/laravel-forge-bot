<?php

namespace App\Integrations\Telegram\Commands\Irazasyed;

class StartCommand extends Command
{
    /**
     * @var string Command name.
     */
    protected $name = 'start';

    /**
     * @var string Command description.
     */
    protected $description = 'Start command to get you started.';

    /**
     * Handle the command.
     *
     * @param $arguments
     */
    public function handle($arguments)
    {
        $this->replyWithMessage([
            'text' => "Hello, {$this->user()->name}! Welcome to our bot!",
        ]);

        $this->triggerCommand('addtoken');
    }
}
