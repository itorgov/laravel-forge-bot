<?php

namespace App\Contracts;

use App\Integrations\Telegram\Entities\ChatAction;
use App\Integrations\Telegram\Entities\OutboundMessage;
use App\Integrations\Telegram\Entities\WebhookInfoResponse;
use App\Integrations\Telegram\Entities\WebhookResponse;
use App\Integrations\Telegram\Exceptions\TelegramBotException;
use Illuminate\Http\Request;

interface TelegramBotContract
{
    /**
     * Sets the webhook URL.
     *
     * @param string $hookUrl
     *
     * @return WebhookResponse
     * @throws TelegramBotException
     */
    public function setWebhook(string $hookUrl): WebhookResponse;

    /**
     * Returns info about current webhook.
     *
     * @return WebhookInfoResponse
     * @throws TelegramBotException
     */
    public function getWebhookInfo(): WebhookInfoResponse;

    /**
     * Removes a webhook.
     *
     * @return WebhookResponse
     * @throws TelegramBotException
     */
    public function removeWebhook(): WebhookResponse;

    /**
     * Authenticates a user using user's credentionals from Telegram.
     *
     * @param Request $request
     *
     * @return void
     */
    public function authenticate(Request $request): void;

    /**
     * Handles a request and works with it.
     *
     * @param Request $request
     *
     * @return void
     */
    public function handle(Request $request): void;

    /**
     * Sends a message.
     *
     * @param OutboundMessage $message
     *
     * @return void
     * @throws TelegramBotException
     */
    public function sendMessage(OutboundMessage $message): void;

    /**
     * Sends a chat action (like typing).
     *
     * @param ChatAction $chatAction
     *
     * @return void
     * @throws TelegramBotException
     */
    public function sendChatAction(ChatAction $chatAction): void;

    /**
     * Edits a message.
     *
     * @param OutboundMessage $message
     *
     * @return void
     * @throws TelegramBotException
     */
    public function editMessage(OutboundMessage $message): void;
}
