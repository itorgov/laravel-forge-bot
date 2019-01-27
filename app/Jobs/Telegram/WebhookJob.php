<?php

namespace App\Jobs\Telegram;

use GuzzleHttp\Client;
use App\Jobs\Job;

class WebhookJob extends Job
{
    /**
     * @var array $message
     */
    private $message;

    /**
     * Create a new job instance.
     *
     * @param array $message
     *
     * @return void
     */
    public function __construct(array $message)
    {
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @param Client $client
     *
     * @return void
     */
    public function handle(Client $client): void
    {
        $chatId = data_get($this->message, 'chat.id');

        if (is_int($chatId)) {

            $webhook = db('webhooks')->where('chat_id', $chatId)->first();

            if ($webhook === null) {
                $token = sha1(str_random() . $chatId);

                db('webhooks')->insert([
                    'chat_id' => $chatId,
                    'token' => $token,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            } else {
                $token = $webhook->token;
            }

            $url = url("/webhook/forge/{$token}", [], true);

            $client->post('sendPhoto', [
                'json' => [
                    'chat_id' => $chatId,
                    'photo' => url('/images/forge_webhook_place.png', [], true),
                    'caption' => "Paste this URL $url to that input field in your app settings and press \"Add Webhook\"."
                ]
            ]);
        }
    }
}
