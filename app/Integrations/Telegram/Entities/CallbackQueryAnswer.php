<?php

namespace App\Integrations\Telegram\Entities;

use App\Facades\TelegramBot;
use App\Integrations\Telegram\Exceptions\CallbackAnswerTextTooLongException;
use Illuminate\Contracts\Support\Arrayable;

class CallbackQueryAnswer implements Arrayable
{
    /**
     * @var array $params
     */
    private array $params = [];

    /**
     * CallbackQueryAnswer constructor.
     *
     * @param string $callbackQueryId
     * @param string $text
     *
     * @return void
     */
    private function __construct(string $callbackQueryId, string $text)
    {
        $this->params['callback_query_id'] = $callbackQueryId;
        $this->params['text'] = $text;
    }

    /**
     * Makes a new instance of this class.
     *
     * @param string $callbackQueryId
     * @param string $text
     *
     * @return static
     */
    public static function make(string $callbackQueryId, string $text): self
    {
        if (strlen($text) > 200) {
            throw new CallbackAnswerTextTooLongException;
        }

        return new self($callbackQueryId, $text);
    }

    /**
     * Shows text at the middle of the chat screen as a popup.
     * Telegram calls it an alert.
     *
     * @return $this
     */
    public function showAsModal(): self
    {
        $this->params['show_alert'] = true;

        return $this;
    }

    /**
     * Sends the answer.
     *
     * @return void
     */
    public function send(): void
    {
        TelegramBot::answerCallbackQuery($this);
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
