<?php

namespace App\Integrations\Telegram\Menu\Screens;

use App\Facades\LaravelForge;
use App\Integrations\Laravel\Forge\Entities\Webhook;
use App\Integrations\Telegram\Entities\InlineKeyboard;
use App\Integrations\Telegram\Entities\InlineKeyboardButton;
use App\Menu;

class SiteScreen extends Screen
{
    public const NAME = 'site';

    public const ACTION_DEPLOY = 'deploy';
    public const ACTION_ADD_WEBHOOK = 'add-webhook';

    /**
     * @var Menu $menu
     */
    protected Menu $menu;

    /**
     * SiteScreen constructor.
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
     * Shows the "Site" screen.
     *
     * @return void
     */
    public function show(): void
    {
        $this->prepare();

        $keyboard = InlineKeyboard::make()
            ->button(InlineKeyboardButton::make('🧱 Deploy Now')->callbackData($this->generateCallbackData(self::NAME, self::ACTION_DEPLOY)))
            ->row()
            ->button(InlineKeyboardButton::make('➕ Add Notification Webhook')->callbackData($this->generateCallbackData(self::NAME, self::ACTION_ADD_WEBHOOK)));

        $webhooks = LaravelForge::setToken($this->menu->token)->webhooks($this->menu->server->id, $this->menu->site->id);

        foreach ($webhooks as $webhook) {
            if ($webhook->isAlien()) {
                continue;
            }

            $keyboard->row()->button($this->button($webhook));
        }

        $keyboard->row()->button($this->backButton());

        $this->updateMenu(
            "*{$this->menu->token->name}*\n*Server*: {$this->menu->server->formatted_name}\n*Site*: {$this->menu->site->name}\n\n" .
            "What do you want to do with the site? You can also set deployment notifications to your chats.",
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

    /**
     * Makes a button with a webhook remove action.
     *
     * @param Webhook $webhook
     *
     * @return InlineKeyboardButton
     */
    private function button(Webhook $webhook)
    {
        return InlineKeyboardButton::make("❌ Stop sending notifications to \"{$webhook->name()}\" chat")
            ->callbackData($this->generateCallbackData(self::NAME, $webhook->id));
    }
}
