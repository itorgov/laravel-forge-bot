<?php

namespace App\Integrations\Telegram\Menu\Screens;

use App\Integrations\Telegram\Entities\InlineKeyboard;
use App\Integrations\Telegram\Entities\InlineKeyboardButton;
use App\Menu;

class AddWebhookScreen extends Screen
{
    public const NAME = 'add-webhook';

    public const ACTION_THIS = 'this';
    public const ACTION_ANOTHER = 'another';

    /**
     * @var Menu $menu
     */
    protected Menu $menu;

    /**
     * AddWebhookScreen constructor.
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
     * Shows the "Add webhook" screen.
     *
     * @return void
     */
    public function show(): void
    {
        $this->prepare();

        $keyboard = InlineKeyboard::make()
            ->button(InlineKeyboardButton::make('To this chat')->callbackData($this->generateCallbackData(self::NAME, self::ACTION_THIS)))
            ->row()
            ->button(InlineKeyboardButton::make('To another chat')->callbackData($this->generateCallbackData(self::NAME, self::ACTION_ANOTHER)));

        $keyboard->row()->button($this->backButton());

        $this->updateMenu(
            "*{$this->menu->token->name}*\n*Server*: {$this->menu->server->formatted_name}\n*Site*: {$this->menu->site->name}\n\n" .
            "To which chat do you want to get deployment notifications?",
            $keyboard
        );
    }

    /**
     * Prepares the screen.
     *
     * @return void
     */
    private function prepare(): void
    {
        $this->menu->update([
            'waiting_message_for' => null,
        ]);
    }
}
