<?php

namespace App\Contracts;

use App\Integrations\Telegram\Entities\BotCommand;
use App\Integrations\Telegram\Entities\CallbackQueryAnswer;
use App\Integrations\Telegram\Entities\ChatAction;
use App\Integrations\Telegram\Entities\OutboundMessage;
use App\Integrations\Telegram\Entities\WebhookInfo;
use App\Integrations\Telegram\Exceptions\TelegramBotException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

interface TelegramBotContract
{
    /**
     * Sets the webhook URL.
     *
     * @param string $hookUrl
     *
     * @return bool
     * @throws TelegramBotException
     */
    public function setWebhook(string $hookUrl): bool;

    /**
     * Returns info about current webhook.
     *
     * @return WebhookInfo
     * @throws TelegramBotException
     */
    public function getWebhookInfo(): WebhookInfo;

    /**
     * Removes a webhook.
     *
     * @return bool
     * @throws TelegramBotException
     */
    public function removeWebhook(): bool;

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

    /**
     * Sends answer to callback query sent from inline keyboard.
     *
     * @param CallbackQueryAnswer $answer
     *
     * @return void
     */
    public function answerCallbackQuery(CallbackQueryAnswer $answer): void;

    /**
     * Returns a collection of available commands (name and description).
     *
     * @return Collection|BotCommand[]
     */
    public function listOfCommands(): Collection;

    /**
     * Changes the list of the bot's commands.
     *
     * @return bool
     */
    public function setMyCommands(): bool;
}
