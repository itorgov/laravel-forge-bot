<?php

namespace App\Console\Commands\Telegram;

use App\Facades\TelegramBot;
use Illuminate\Console\Command;

class SetCommandsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:set-commands';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates the list of the bot\'s commands.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $result = TelegramBot::setMyCommands();

        if (! $result) {
            $this->error('Unable to set commands.');

            return 1;
        }

        $this->info('Commands successfully set!');

        return 0;
    }
}
