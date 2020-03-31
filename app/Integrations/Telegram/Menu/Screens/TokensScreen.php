<?php

namespace App\Integrations\Telegram\Menu\Screens;

use App\Integrations\Telegram\Entities\InlineKeyboard;
use App\Integrations\Telegram\Entities\InlineKeyboardButton;
use App\Integrations\Telegram\Entities\OutboundMessage;
use App\Menu;
use App\Token;
use Exception;

class TokensScreen extends Screen
{
    public const NAME = 'tokens';

    /**
     * @var Menu
     */
    protected Menu $menu;

    /**
     * TokensScreen constructor.
     *
     * @param Menu $menu
     *
     * @return void
     */
    public function __construct(Menu $menu)
    {
        $this->menu = $menu;
    }

    /**
     * Shows the "Tokens" screen.
     *
     * @return void
     */
    public function show(): void
    {
        $this->prepare();

        $tokens = $this->menu->user->tokens;

        if ($tokens->isEmpty()) {
            OutboundMessage::make($this->menu->user, 'You haven\'t any Laravel Forge API tokens yet. Please add one by using the /addtoken command.')->send();

            try {
                $this->menu->delete();
            } catch (Exception $exception) {
                // This exception happes really rarely. Almost never.
                // So, just report about it.
                report($exception);
            }

            return;
        }

        $keyboard = InlineKeyboard::make();

        foreach ($tokens as $token) {
            $keyboard->row()->button($this->button($token));
        }

        $this->updateMenu('Choose your token:', $keyboard);
    }

    /**
     * Prepares the screen.
     *
     * @return void
     */
    private function prepare(): void
    {
        $this->menu->server()->delete();
        $this->menu->site()->delete();

        $this->menu->update([
            'token_id' => null,
            'waiting_message_for' => null,
        ]);
    }

    /**
     * Makes a button with a token.
     *
     * @param Token $token
     *
     * @return InlineKeyboardButton
     */
    private function button(Token $token)
    {
        return InlineKeyboardButton::make($token->name)
            ->callbackData($this->generateCallbackData(self::NAME, $token->id));
    }
}
