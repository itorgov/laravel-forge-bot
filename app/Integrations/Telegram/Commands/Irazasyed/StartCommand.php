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
     * @return void
     */
    public function handle()
    {
        $this->replyWithMessage([
            'text' => "Hey, {$this->user()->name}! You are welcome! It's an unofficial bot for Laravel Forge. ".
                'It\'s completely free and you can find source code on '.
                '[GitHub](https://github.com/itorgov/laravel-forge-bot). '.
                'If you like it and find it useful, please give a star to the repository.',
            'parse_mode' => 'Markdown',
        ]);

        $this->triggerCommand('addtoken');
    }
}
