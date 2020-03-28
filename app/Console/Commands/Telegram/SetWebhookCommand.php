<?php

namespace App\Console\Commands\Telegram;

use App\Facades\TelegramBot;
use Illuminate\Console\Command;

class SetWebhookCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:set-webhook
                           {--force : Force the operation to set webhook}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set a url for receiving incoming updates from Telegram servers';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $url = route('integrations.telegram.webhook');

        if (! $this->confirmToProceed($url)) {
            return 0;
        }

        $this->info($this->setWebhook($url));

        return 0;
    }

    /**
     * Confirm before setting the new webhook.
     *
     * @param string $url
     *
     * @return bool
     */
    protected function confirmToProceed(string $url): bool
    {
        if ($this->option('force')) {
            return true;
        }

        $currentWebhookUrl = TelegramBot::getWebhookInfo()->result->url;

        if (! empty($currentWebhookUrl)) {
            $this->alert("There is a current webhook URL: {$currentWebhookUrl}");

            return $this->confirm("Do you want to replace it to {$url}?");
        }

        return true;
    }

    /**
     * Set the new webhook.
     *
     * @param string $url
     *
     * @return string|null
     */
    protected function setWebhook(string $url): ?string
    {
        return TelegramBot::setWebhook($url)->description;
    }
}
