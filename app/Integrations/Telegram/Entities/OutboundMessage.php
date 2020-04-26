<?php

namespace App\Integrations\Telegram\Entities;

use App\Events\BotWasBlocked;
use App\Facades\TelegramBot;
use App\Integrations\Telegram\Exceptions\TelegramBotException;
use App\User;
use Illuminate\Contracts\Support\Arrayable;

class OutboundMessage implements Arrayable
{
    public const PARSE_MODE_MARKDOWN = 'Markdown';
    public const PARSE_MODE_HTML = 'HTML';

    /**
     * @var array
     */
    private array $params = [];

    /**
     * OutboundMessage constructor.
     *
     * @param string $chatId
     * @param string $text
     *
     * @return void
     */
    private function __construct(string $chatId, string $text)
    {
        $this->params['chat_id'] = $chatId;
        $this->params['text'] = $text;
    }

    /**
     * Makes a new instance of this class.
     *
     * @param User $user
     * @param string $text
     *
     * @return static
     */
    public static function make(User $user, string $text): self
    {
        return new self($user->telegram_chat_id, $text);
    }

    /**
     * Sets parse mode.
     *
     * @param string $value
     *
     * @return $this
     */
    public function parseMode(string $value): self
    {
        $this->params['parse_mode'] = $value;

        return $this;
    }

    /**
     * Disables/enables web page preview.
     *
     * @param bool $value
     *
     * @return $this
     */
    public function disableWebPagePreview(bool $value): self
    {
        $this->params['disable_web_page_preview'] = $value;

        return $this;
    }

    /**
     * Disables/enables notification about this message to the user.
     *
     * @param bool $value
     *
     * @return $this
     */
    public function disableNotification(bool $value): self
    {
        $this->params['disable_notification'] = $value;

        return $this;
    }

    /**
     * Adds a reply keyboard to the message.
     *
     * @param ReplyKeyboard $keyboard
     *
     * @return $this
     */
    public function withReplyKeyboard(ReplyKeyboard $keyboard): self
    {
        $this->params['reply_markup'] = json_encode($keyboard->toArray());

        return $this;
    }

    /**
     * Removes a reply keyboard.
     *
     * @return $this
     */
    public function removeReplyKeyboard(): self
    {
        $this->params['reply_markup'] = json_encode(['remove_keyboard' => true]);

        return $this;
    }

    /**
     * Adds inline keyboard to the message.
     *
     * @param InlineKeyboard $keyboard
     *
     * @return $this
     */
    public function withInlineKeyboard(InlineKeyboard $keyboard): self
    {
        $this->params['reply_markup'] = json_encode($keyboard->toArray());

        return $this;
    }

    /**
     * Sends message to the user.
     *
     * @return void
     */
    public function send(): void
    {
        try {
            TelegramBot::sendMessage($this);
        } catch (TelegramBotException $exception) {
            if ($exception->getMessage() === 'Forbidden: bot was blocked by the user') {
                event(new BotWasBlocked($this->params['chat_id']));
            } else {
                throw $exception;
            }
        }
    }

    /**
     * Edits message by it's id.
     *
     * @param int $id
     *
     * @return void
     */
    public function edit(int $id): void
    {
        $this->params['message_id'] = $id;
        TelegramBot::editMessage($this);
        unset($this->params['message_id']);
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
