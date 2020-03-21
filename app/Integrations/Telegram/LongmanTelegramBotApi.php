<?php

namespace App\Integrations\Telegram;

use App\Integrations\Telegram\Exceptions\TelegramBotException;
use Illuminate\Http\Request;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Telegram;

class LongmanTelegramBotApi implements TelegramBotApi
{
    private Telegram $telegram;

    public function __construct($botApiKey, $botUsername)
    {
        try {
            $this->telegram = new Telegram($botApiKey, $botUsername);
            $this->telegram->enableLimiter();
        } catch (TelegramException $e) {
            throw new TelegramBotException($e->getMessage());
        }
    }

    public function setWebhook(string $hookUrl): void
    {
        try {
            $result = $this->telegram->setWebhook($hookUrl);

            if (!$result->isOk()) {
                throw new TelegramBotException($result->getDescription());
            }
        } catch (TelegramException $e) {
            throw new TelegramBotException($e->getMessage());
        }

        info($result->getDescription());
    }

    public function deleteWebhook(): void
    {
        try {
            $result = $this->telegram->deleteWebhook();

            if (!$result->isOk()) {
                throw new TelegramBotException($result->getDescription());
            }
        } catch (TelegramException $e) {
            throw new TelegramBotException($e->getMessage());
        }

        info($result->getDescription());
    }

    public function handle(Request $request): void
    {
        $this->telegram->setCustomInput($request->getContent())->handle();
    }
}
