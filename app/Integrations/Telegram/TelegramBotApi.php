<?php

namespace App\Integrations\Telegram;

use Illuminate\Http\Request;

interface TelegramBotApi
{
    public function setWebhook(string $hookUrl): void;

    public function deleteWebhook(): void;

    public function handle(Request $request): void;
}
