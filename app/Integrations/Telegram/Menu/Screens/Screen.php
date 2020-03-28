<?php

namespace App\Integrations\Telegram\Menu\Screens;

use App\Integrations\Telegram\Entities\InlineKeyboard;
use App\Integrations\Telegram\Entities\InlineKeyboardButton;
use App\Integrations\Telegram\Entities\OutboundMessage;
use App\Menu;

abstract class Screen
{
    /**
     * @var Menu
     */
    protected Menu $menu;

    abstract public function __construct(Menu $menu);

    abstract public function show(): void;

    /**
     * Makes a "Back" button for the current screen.
     *
     * @return InlineKeyboardButton
     */
    protected function backButton()
    {
        return InlineKeyboardButton::make('← Back')
            ->callbackData($this->generateCallbackData(static::NAME, ''));
    }

    /**
     * Updates the menu message.
     *
     * @param string $text
     * @param InlineKeyboard $keyboard
     *
     * @return void
     */
    protected function updateMenu(string $text, InlineKeyboard $keyboard): void
    {
        OutboundMessage::make($this->menu->user, $text)
            ->parseMode(OutboundMessage::PARSE_MODE_MARKDOWN)
            ->withInlineKeyboard($keyboard)
            ->edit($this->menu->message_id);
    }

    /**
     * Packs data for the callback query.
     *
     * @param string $screen
     * @param string $data
     *
     * @return string
     */
    protected function generateCallbackData(string $screen, string $data)
    {
        return "{$screen}:{$data}";
    }
}
