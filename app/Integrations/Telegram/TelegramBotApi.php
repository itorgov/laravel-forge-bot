<?php

namespace App\Integrations\Telegram;

use App\Integrations\Telegram\Entities\WebhookInfoResponse;
use App\Integrations\Telegram\Entities\WebhookResponse;

interface TelegramBotApi
{
    public function setWebhook(string $hookUrl): WebhookResponse;

    public function getWebhookInfo(): WebhookInfoResponse;

    public function removeWebhook(): WebhookResponse;

    public function handle(): void;
}
