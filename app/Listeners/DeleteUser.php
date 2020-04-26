<?php

namespace App\Listeners;

use App\Events\BotWasBlocked;
use App\User;
use Exception;

class DeleteUser
{
    /**
     * Handle the event.
     *
     * @param BotWasBlocked $event
     * @return void
     *
     * @throws Exception
     */
    public function handle(BotWasBlocked $event)
    {
        $user = User::findByTelegramChatId($event->chatId);

        if ($user) {
            $user->delete();
        }
    }
}
