<?php

namespace App\Integrations\Telegram;

use App\Contracts\TelegramBotContract;
use App\Integrations\Telegram\Commands\Irazasyed\AddTokenCommand;
use App\Integrations\Telegram\Commands\Irazasyed\HelpCommand;
use App\Integrations\Telegram\Commands\Irazasyed\MenuCommand;
use App\Integrations\Telegram\Commands\Irazasyed\StartCommand;
use App\Integrations\Telegram\Entities\CallbackQueryAnswer;
use App\Integrations\Telegram\Entities\ChatAction;
use App\Integrations\Telegram\Entities\OutboundMessage;
use App\Integrations\Telegram\Entities\WebhookInfoResponse;
use App\Integrations\Telegram\Entities\WebhookResponse;
use App\Integrations\Telegram\Exceptions\TelegramBotException;
use App\Integrations\Telegram\Menu\MenuManager;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Objects\MessageEntity;
use Telegram\Bot\Objects\Update;

class IrazasyedTelegramBot implements TelegramBotContract
{
    /**
     * @var Api $telegram
     */
    private Api $telegram;

    /**
     * IrazasyedTelegramBot constructor.
     *
     * @param string $botApiKey
     *
     * @return void
     */
    public function __construct(string $botApiKey)
    {
        try {
            $this->telegram = new Api($botApiKey);
        } catch (TelegramSDKException $e) {
            throw new TelegramBotException($e->getMessage());
        }

        $this->telegram->addCommands([
            HelpCommand::class,
            StartCommand::class,
            AddTokenCommand::class,
            MenuCommand::class,
        ]);
    }

    /**
     * @inheritDoc
     */
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

    /**
     * @inheritDoc
     */
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

    /**
     * @inheritDoc
     */
    public function removeWebhook(): WebhookResponse
    {
        try {
            $response = $this->telegram->removeWebhook();
        } catch (TelegramSDKException $e) {
            throw new TelegramBotException($e->getMessage());
        }

        return new WebhookResponse($response->getDecodedBody());
    }

    /**
     * @inheritDoc
     */
    public function authenticate(Request $request): void
    {
        $chat = (new Update($request->all()))->getChat();

        switch ($chat->getType()) {
            case 'private':
                $name = trim("{$chat->getFirstName()} {$chat->getLastName()}");
                break;
            case 'group':
            case 'supergroup':
            case 'channel':
                $name = $chat->getTitle();
                break;
            default:
                $name = 'Unknown name';
        }

        $user = User::findOrCreateByTelegramChatId($chat->getId(), [
            'name' => $name,
        ]);

        Auth::login($user);
    }

    /**
     * Finds current dialog and processes it.
     *
     * @param Update $update
     *
     * @return void
     */
    private function processDialog(Update $update): void
    {
        if (!$update->isType('message')) {
            return;
        }

        $currentDialog = Auth::user()->dialogs()->current()->first();

        if ($currentDialog !== null) {
            $currentDialog->nextStep($update->getMessage()->getText());
        }
    }

    /**
     * Gets callback data and sends it to menu manager.
     *
     * @param Update $update
     *
     * @return void
     */
    private function processCallbackQuery(Update $update): void
    {
        MenuManager::forMessageId($update->getCallbackQuery()->getMessage()->getMessageId())
            ->handleCallback($update->getCallbackQuery()->getId(), $update->getCallbackQuery()->getData());
    }

    /**
     * Runs a command if it finds one.
     *
     * @param Update $update
     *
     * @return void
     */
    private function processCommand(Update $update): void
    {
        // Every command will finish all current user's dialogs.
        Auth::user()->finishCurrentDialogs();

        $this->telegram->processCommand($update);
    }

    /**
     * Determines if received update is a command.
     *
     * @param Update $update
     *
     * @return bool
     */
    private function updateIsCommand(Update $update): bool
    {
        if (!$update->isType('message')) {
            return false;
        }

        $entities = ($update->getMessage()->getEntities() ?? collect())->map(function ($entity) {
            return new MessageEntity($entity);
        });

        foreach ($entities as $entity) {
            if ($entity->getType() === 'bot_command' && $entity->getOffset() === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function handle(Request $request): void
    {
        logger()->debug('New update from Telegram', $request->all());
        $update = new Update($request->all());

        if ($update->isType('callback_query')) {
            $this->processCallbackQuery($update);
        } else if ($this->updateIsCommand($update)) {
            $this->processCommand($update);
        } else {
            $this->processDialog($update);
        }
    }

    /**
     * @inheritDoc
     */
    public function sendMessage(OutboundMessage $message): void
    {
        try {
            $this->telegram->sendMessage($message->toArray());
        } catch (TelegramSDKException $e) {
            throw new TelegramBotException($e->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function sendChatAction(ChatAction $chatAction): void
    {
        try {
            $this->telegram->sendChatAction($chatAction->toArray());
        } catch (TelegramSDKException $e) {
            throw new TelegramBotException($e->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function editMessage(OutboundMessage $message): void
    {
        try {
            $this->telegram->editMessageText($message->toArray());
        } catch (TelegramSDKException $e) {
            throw new TelegramBotException($e->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function answerCallbackQuery(CallbackQueryAnswer $answer): void
    {
        try {
            $this->telegram->answerCallbackQuery($answer->toArray());
        } catch (TelegramSDKException $e) {
            throw new TelegramBotException($e->getMessage());
        }
    }
}
