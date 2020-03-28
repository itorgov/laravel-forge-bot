<?php

namespace App\Integrations\Telegram\Commands\Irazasyed;

class ShowChatIdCommand extends Command
{
    /**
     * @var string Command name.
     */
    protected $name = 'showchatid';

    /**
     * @var string Command description.
     */
    protected $description = 'Shows a chat ID.';

    /**
     * Handle the command.
     *
     * @param $arguments
     */
    public function handle($arguments)
    {
        $this->replyWithMessage([
            'text' => "ID of this chat is `{$this->user()->telegram_chat_id}`.",
            'parse_mode' => 'Markdown',
        ]);
    }
}
