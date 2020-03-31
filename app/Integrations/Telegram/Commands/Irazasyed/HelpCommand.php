<?php

namespace App\Integrations\Telegram\Commands\Irazasyed;

use App\Facades\TelegramBot;
use App\Integrations\Telegram\Entities\BotCommand;

class HelpCommand extends Command
{
    /**
     * @var string Command name.
     */
    protected $name = 'help';

    /**
     * @var array Command aliases.
     */
    protected $aliases = ['listcommands'];

    /**
     * @var string Command description.
     */
    protected $description = 'Help command returns a list of commands.';

    /**
     * Handle the command.
     *
     * @param $arguments
     */
    public function handle($arguments)
    {
        $text = TelegramBot::listOfCommands()->reduce(function (string $text, BotCommand $botCommand) {
            return $text.vsprintf("/%s - %s\n", [
                $botCommand->getName(),
                $botCommand->getDescription(),
            ]);
        }, "Here is a list of available commands:\n");

        $this->replyWithMessage([
            'text' => $text,
        ]);
    }
}
