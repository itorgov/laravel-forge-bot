<?php

namespace App\Integrations\Telegram\Entities;

use App\Facades\TelegramBot;
use App\User;
use Illuminate\Contracts\Support\Arrayable;

class ChatAction implements Arrayable
{
    /**
     * Sets chat status as Typing.
     *
     * @var string
     */
    public const TYPING = 'typing';

    /**
     * @var array
     */
    private array $params = [];

    /**
     * ChatAction constructor.
     *
     * @param string $chatId
     *
     * @return void
     */
    private function __construct(string $chatId)
    {
        $this->params['chat_id'] = $chatId;
    }

    /**
     * Makes a new instance of this class.
     *
     * @param User $user
     *
     * @return static
     */
    public static function make(User $user): self
    {
        // Set "typing" action as default.
        return (new self($user->telegram_chat_id))->typing();
    }

    /**
     * Sets action type to "typing".
     *
     * @return $this
     */
    public function typing(): self
    {
        $this->params['action'] = self::TYPING;

        return $this;
    }

    /**
     * Sends action to the user.
     *
     * @return void
     */
    public function send(): void
    {
        TelegramBot::sendChatAction($this);
    }

    /**
     * Returns params.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->params;
    }
}
