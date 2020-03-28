<?php

namespace App\Integrations\Telegram\Dialogs;

use App\Contracts\DialogContract;
use App\Dialog;
use App\Exceptions\Dialogs\InvalidDialogName;
use App\Integrations\Telegram\Dialogs\Dialog as BaseDialog;
use App\Integrations\Telegram\Entities\OutboundMessage;
use App\User;
use Illuminate\Support\Facades\Auth;

class AskForChatIdDialog extends BaseDialog implements DialogContract
{
    /**
     * @var Dialog
     */
    protected Dialog $dialog;

    /**
     * AskForChatIdDialog constructor.
     *
     * @param Dialog $dialog
     *
     * @return void
     */
    private function __construct(Dialog $dialog)
    {
        $this->dialog = $dialog;
    }

    /**
     * {@inheritdoc}
     */
    public static function start(array $additionalData = []): self
    {
        $dialog = Auth::user()->dialogs()->create([
            'name' => self::class,
            'data' => [
                'chat_id' => null,
                'additional_data' => $additionalData,
            ],
        ]);

        return (new self($dialog))->nextStep();
    }

    /**
     * {@inheritdoc}
     */
    public static function next(Dialog $dialog, string $message): self
    {
        if ($dialog->name !== self::class) {
            throw new InvalidDialogName;
        }

        return (new self($dialog))->nextStep($message);
    }

    /**
     * Determines a next step of the dialog and runs it.
     *
     * @param string|null $message
     *
     * @return $this
     */
    private function nextStep(?string $message = null): self
    {
        if ($this->dialog->data['chat_id'] === null) {
            if ($message === null) {
                $this->askForChatId();
            } else {
                $this->parseMessageForChatId($message);
            }
        } else {
            $this->dialog->finish();
        }

        return $this;
    }

    /**
     * @return void
     */
    private function askForChatId(): void
    {
        OutboundMessage::make(
            $this->dialog->user,
            'Send me the id of that chat. First of all you have to add me to that chat. '.
            'Then you can use my command /showchatid@'.config('services.telegram.bot.username').' to get an id.'
        )->send();
    }

    /**
     * Parses user's message for a valid chat id.
     *
     * @param string $message
     *
     * @return void
     */
    private function parseMessageForChatId(string $message): void
    {
        if ($this->canSendMessagesToChatId($message)) {
            $this->saveChatId($message);
            $this->nextStep();
        } else {
            $this->askForValidChatId();
        }
    }

    /**
     * Checks if have ability to send messages to the target chat.
     *
     * @param string $chatId
     *
     * @return bool
     */
    private function canSendMessagesToChatId(string $chatId): bool
    {
        return User::findByTelegramChatId($chatId) !== null;
    }

    /**
     * Saves the chat id to the database and finishes dialog's step about getting the chat id.
     *
     * @param string $chatId
     *
     * @return void
     */
    private function saveChatId(string $chatId): void
    {
        $this->dialog->data = array_merge($this->dialog->data, [
            'chat_id' => $chatId,
        ]);
        $this->dialog->save();
    }

    /**
     * Sends a message to the user about an invalid chat id.
     *
     * @return void
     */
    private function askForValidChatId(): void
    {
        OutboundMessage::make(
            $this->dialog->user,
            'It seems that I can\'t send messages to that chat. Make sure that you correctly added me to that chat and send me the chat id again.'
        )->send();
    }
}
