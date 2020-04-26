<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BotWasBlocked
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var string
     */
    public string $chatId;

    /**
     * Create a new event instance.
     *
     * @param string $chatId
     * @return void
     */
    public function __construct(string $chatId)
    {
        $this->chatId = $chatId;
    }
}
