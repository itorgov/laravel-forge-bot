<?php

namespace App\Jobs\Telegram;

use GuzzleHttp\Client;
use App\Jobs\Job;

class StartJob extends Job
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
            $client->post('sendMessage', [
                'json' => [
                    'chat_id' => $chatId,
                    'text' => 'What\'s up buddy! Let\'s start with /webhook command.'
                ]
            ]);
        }
    }
}
