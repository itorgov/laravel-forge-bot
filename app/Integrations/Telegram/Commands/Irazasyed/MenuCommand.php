<?php

namespace App\Integrations\Telegram\Commands\Irazasyed;

use App\Integrations\Telegram\Menu\MenuManager;
use Telegram\Bot\Objects\Message;

class MenuCommand extends Command
{
    /**
     * @var string Command name.
     */
    protected $name = 'menu';

    /**
     * @var string Command description.
     */
    protected $description = 'Shows a menu for managing your Laravel Forge servers.';

    /**
     * Handle the command.
     *
     * @return void
     */
    public function handle()
    {
        /** @var Message $message */
        $message = $this->replyWithMessage([
            'text' => 'Creating a new menu...',
        ]);

        MenuManager::forMessageId($message->getMessageId());
    }
}
