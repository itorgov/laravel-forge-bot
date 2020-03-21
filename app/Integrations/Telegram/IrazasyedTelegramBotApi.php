<?php

namespace App\Integrations\Telegram;

use App\Integrations\Telegram\Entities\WebhookInfoResponse;
use App\Integrations\Telegram\Entities\WebhookResponse;
use App\Integrations\Telegram\Exceptions\TelegramBotException;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;

class IrazasyedTelegramBotApi implements TelegramBotApi
{
    /**
     * @var Api $telegram
     */
    private Api $telegram;

    public function __construct(string $botApiKey)
    {
        try {
            $this->telegram = new Api($botApiKey);
        } catch (TelegramSDKException $e) {
            throw new TelegramBotException($e->getMessage());
        }
    }

    public function setWebhook(string $hookUrl): WebhookResponse
    {
        try {
            $response = $this->telegram->setWebhook([
                'url' => $hookUrl,
            ]);
        } catch (TelegramSDKException $e) {
            throw new TelegramBotException($e->getMessage());
        }

        return new WebhookResponse($response->getDecodedBody());
    }

    public function getWebhookInfo(): WebhookInfoResponse
    {
        try {
            // This library doesn't have any method for getting info about webhook.
            // So I found this workaround.
            $response = $this->telegram->getWebhookInfo(null);
        } catch (TelegramSDKException $e) {
            throw new TelegramBotException($e->getMessage());
        }

        return new WebhookInfoResponse($response->getDecodedBody());
    }

    public function removeWebhook(): WebhookResponse
    {
        try {
            $response = $this->telegram->removeWebhook();
        } catch (TelegramSDKException $e) {
            throw new TelegramBotException($e->getMessage());
        }

        return new WebhookResponse($response->getDecodedBody());
    }

    public function handle(): void
    {
        $update = $this->telegram->getWebhookUpdate();

        try {
            $this->telegram->sendMessage([
                'chat_id' => $update->getChat()->getId(),
                'text' => $update->getMessage()->getText(),
            ]);
        } catch (TelegramSDKException $e) {
            throw new TelegramBotException($e->getMessage());
        }
    }
}
