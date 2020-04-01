<?php

namespace App\Console\Commands\Telegram;

use App\Facades\TelegramBot;
use Illuminate\Console\Command;

class RemoveWebhookCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:remove-webhook';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove webhook integration';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->info(TelegramBot::removeWebhook() ? 'Webhook was deleted' : 'Webhook wasn\'t deleted');

        return 0;
    }
}
