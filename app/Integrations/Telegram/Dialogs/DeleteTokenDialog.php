<?php

namespace App\Integrations\Telegram\Dialogs;

use App\Contracts\DialogContract;
use App\Dialog;
use App\Exceptions\Dialogs\InvalidDialogName;
use App\Integrations\Telegram\Dialogs\Dialog as BaseDialog;
use App\Integrations\Telegram\Entities\ChatAction;
use App\Integrations\Telegram\Entities\KeyboardButton;
use App\Integrations\Telegram\Entities\OutboundMessage;
use App\Integrations\Telegram\Entities\ReplyKeyboard;
use Illuminate\Support\Facades\Auth;

class DeleteTokenDialog extends BaseDialog implements DialogContract
{
    /**
     * @var Dialog
     */
    protected Dialog $dialog;

    /**
     * DeleteTokenDialog constructor.
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
                'token_name' => null,
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
        if ($this->dialog->data['token_name'] === null) {
            if ($message === null) {
                $this->askForTokenName();
            } else {
                $this->parseMessageForTokenName($message);
            }
        } else {
            $this->dialog->finish();
            $this->sendConfirmation();
        }

        return $this;
    }

    /**
     * Sends message to the user with a reply keyboard.
     *
     * @return void
     */
    private function askForTokenName(): void
    {
        $tokens = $this->dialog->user->tokens;

        if ($tokens->isEmpty()) {
            OutboundMessage::make($this->dialog->user, 'You don\'t have any tokens yet.')->send();

            return;
        }

        $keyboard = ReplyKeyboard::make()->resizeKeyboard(true)->oneTimeKeyboard(false);

        foreach ($tokens as $token) {
            $keyboard->row()->button(KeyboardButton::make($token->name));
        }

        OutboundMessage::make(
            $this->dialog->user,
            'Send me name of a Laravel Forge API token which you want to delete. '.
            'I sent you a special keyboard for convenience.'
        )->withReplyKeyboard($keyboard)->send();
    }

    /**
     * Parses user's message for a valid token name.
     *
     * @param string $message
     *
     * @return void
     */
    private function parseMessageForTokenName(string $message): void
    {
        ChatAction::make($this->dialog->user)->typing()->send();

        if ($this->messageContainsValidTokenName($message)) {
            $this->deleteToken($message);
            $this->nextStep();
        } else {
            $this->askForValidTokenName();
        }
    }

    /**
     * Checks if user's message is a valid token name.
     *
     * @param string $tokenName
     *
     * @return bool
     */
    private function messageContainsValidTokenName(string $tokenName): bool
    {
        return $this->dialog->user->tokens()->name($tokenName)->exists();
    }

    /**
     * Saves the token to the database and finishes dialog's step about getting the token.
     *
     * @param string $tokenName
     *
     * @return void
     */
    private function deleteToken(string $tokenName): void
    {
        $this->dialog->user->tokens()->name($tokenName)->delete();

        $this->dialog->data = [
            'token_name' => $tokenName,
        ];
        $this->dialog->save();
    }

    /**
     * Sends a message to the user about invalid token name.
     *
     * @return void
     */
    private function askForValidTokenName(): void
    {
        OutboundMessage::make(
            $this->dialog->user,
            'You don\'t have tokens with this name. Try again.'
        )->send();
    }

    /**
     * Sends a confirmation message to the user.
     *
     * @return void
     */
    private function sendConfirmation(): void
    {
        OutboundMessage::make(
            $this->dialog->user,
            "Your token *{$this->dialog->data['token_name']}* succesfully deleted. ".
            "Note that previous menus with this token won't work. Please, create a new menu using /menu command."
        )->removeReplyKeyboard()->parseMode(OutboundMessage::PARSE_MODE_MARKDOWN)->send();
    }
}
