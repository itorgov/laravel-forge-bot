<?php

namespace App\Integrations\Telegram\Commands\Irazasyed;

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
    protected $description = 'Help command, returns a list of commands.';

    /**
     * Handle the command.
     *
     * @param $arguments
     */
    public function handle($arguments)
    {
        $commands = $this->telegram->getCommands();

        $text = '';
        foreach ($commands as $name => $handler) {
            $text .= sprintf('/%s - %s' . PHP_EOL, $name, $handler->getDescription());
        }

        $this->replyWithMessage([
            'text' => $text,
        ]);
    }
}
