<?php

namespace App\Integrations\Telegram\Menu\Screens;

use App\Integrations\Telegram\Entities\InlineKeyboard;
use App\Integrations\Telegram\Entities\InlineKeyboardButton;
use App\Integrations\Telegram\Entities\OutboundMessage;
use App\Menu;

abstract class Screen
{
    /**
     * @var Menu $menu
     */
    protected Menu $menu;

    /**
     * @inheritDoc
     */
    abstract function __construct(Menu $menu);

    /**
     * @inheritDoc
     */
    abstract function show(): void;

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
     * @param InlineKeyboard $buttons
     *
     * @return void
     */
    protected function updateMenu(string $text, InlineKeyboard $buttons): void
    {
        OutboundMessage::make($this->menu->user, $text)
            ->parseMode(OutboundMessage::PARSE_MODE_MARKDOWN)
            ->withInlineKeyboard($buttons)
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
