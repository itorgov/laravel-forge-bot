<?php

namespace App\Integrations\Telegram\Dialogs;

use App\Contracts\DialogContract;
use App\Dialog;
use App\Exceptions\Dialogs\InvalidDialogName;
use App\Facades\LaravelForge;
use App\Integrations\Laravel\Forge\Exceptions\LaravelForgeException;
use App\Integrations\Telegram\Entities\ChatAction;
use App\Integrations\Telegram\Entities\OutboundMessage;
use App\Token;
use Illuminate\Support\Facades\Auth;

class AddTokenDialog implements DialogContract
{
    /**
     * @var Dialog $dialog
     */
    private Dialog $dialog;

    /**
     * AddTokenDialog constructor.
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
     * @inheritDoc
     */
    public static function start(): self
    {
        $dialog = Auth::user()->dialogs()->create([
            'name' => self::class,
            'data' => [
                'token' => false,
            ],
        ]);

        return (new self($dialog))->nextStep();
    }

    /**
     * @inheritDoc
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
        if (!$this->dialog->data['token']) {
            if ($message === null) {
                $this->askForToken();
            } else {
                $this->parseMessageForToken($message);
            }
        } else {
            $this->dialog->finish();
            $this->sendThanks();
        }

        return $this;
    }

    /**
     * @return void
     */
    private function askForToken(): void
    {
        $hasTokens = $this->dialog->user->tokens()->exists();

        if (!$hasTokens) {
            $text = 'Let\'s add your first Laravel Forge API token. ' .
                'Go to [API](https://forge.laravel.com/user/profile#/api) section in your ' .
                'Laravel Forge account settings and generate a new token. ' .
                'Then just send it me.';
        } else {
            $text = 'Send me your Laravel Forge API token.';
        }

        OutboundMessage::make($this->dialog->user, $text)->parseMode(OutboundMessage::PARSE_MODE_MARKDOWN)->send();
    }

    /**
     * Parses user's message for valid token.
     *
     * @param string $message
     *
     * @return void
     */
    private function parseMessageForToken(string $message): void
    {
        ChatAction::make($this->dialog->user)->typing()->send();

        if ($this->messageContainsValidToken($message)) {
            $this->saveToken($message);
            $this->nextStep();
        } else {
            $this->askForValidToken();
        }
    }

    /**
     * Checks if user's message is a valid token.
     *
     * @param string $token
     *
     * @return bool
     */
    private function messageContainsValidToken(string $token): bool
    {
        try {
            $user = LaravelForge::setToken(new Token(['value' => $token]))->user();
        } catch (LaravelForgeException $e) {
            return false;
        }

        return !empty($user);
    }

    /**
     * Saves the token to the database and finishes dialog's step about getting the token.
     *
     * @param string $token
     *
     * @return void
     */
    private function saveToken(string $token): void
    {
        $this->dialog->user->tokens()->create([
            'name' => LaravelForge::setToken(new Token(['value' => $token]))->user(),
            'value' => $token,
        ]);

        $this->dialog->data = [
            'token' => true,
        ];
        $this->dialog->save();
    }

    /**
     * Sends a message to the user about invalid token.
     *
     * @return void
     */
    private function askForValidToken(): void
    {
        OutboundMessage::make(
            $this->dialog->user,
            'You sent an invalid token. I can\'t use it for connectiong to your Laravel Forge account. Please, send me a valid token.'
        )->send();
    }

    /**
     * @return void
     */
    private function sendThanks(): void
    {
        OutboundMessage::make(
            $this->dialog->user,
            'You succesfully added your Laravel Forge API token. You can add another one by using /addtoken command. Now you can use /menu command.'
        )->send();
    }
}
