<?php

namespace App\Console\Commands\Telegram;

use GuzzleHttp\Client;
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
    protected $description = 'Set the telegram webhook via Telegram Bot API';

    /**
     * Execute the console command.
     *
     * @param Client $client
     *
     * @return void
     */
    public function handle(Client $client): void
    {
        $token = env('TELEGRAM_WEBHOOK_TOKEN');

        $url = url("/webhook/telegram/{$token}", [], true);

        if (!$this->confirmToProceed($client, $url)) {
            return;
        }

        $this->info($this->setWebhook($client, $url));
    }

    /**
     * Confirm before setting the new webhook.
     *
     * @param Client $client
     * @param string $url
     *
     * @return bool
     */
    protected function confirmToProceed(Client $client, string $url): bool
    {
        if ($this->option('force')) {
            return true;
        }

        $response = $client->get('getWebhookInfo');
        $currentWebhook = json_decode($response->getBody()->getContents());

        if (optional($currentWebhook)->ok && !empty(data_get($currentWebhook, 'result.url'))) {
            $this->line('There is a current webhook.');
            $this->line('URL: ' . data_get($currentWebhook, 'result.url'));
            $this->line('Has custom certificate: ' . (data_get($currentWebhook, 'result.has_custom_certificate') ? 'true' : 'false'));
            $this->line('Pending update count: ' . data_get($currentWebhook, 'result.pending_update_count'));

            return $this->confirm("Do you want replace it to {$url}?");
        }

        return true;
    }

    /**
     * Set the new webhook.
     *
     * @param Client $client
     * @param string $url
     *
     * @return string|null
     */
    protected function setWebhook(Client $client, string $url): ?string
    {
        $response = $client->post('setWebhook', [
            'json' => [
                'url' => $url
            ]
        ]);

        $result = json_decode($response->getBody()->getContents());

        return optional($result)->description;
    }
}
