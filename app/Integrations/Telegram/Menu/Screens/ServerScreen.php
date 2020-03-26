<?php

namespace App\Integrations\Telegram\Menu\Screens;

use App\Facades\LaravelForge;
use App\Integrations\Laravel\Forge\Entities\Site;
use App\Integrations\Telegram\Entities\InlineKeyboard;
use App\Integrations\Telegram\Entities\InlineKeyboardButton;
use App\Menu;

class ServerScreen extends Screen
{
    public const NAME = 'server';

    public const ACTION_REBOOT = 'reboot';
    public const ACTION_REBOOT_MYSQL = 'reboot-mysql';
    public const ACTION_REBOOT_POSTRGESQL = 'reboot-postgresql';
    public const ACTION_REBOOT_PHP = 'reboot-php';
    public const ACTION_REBOOT_NGINX = 'reboot-nginx';

    /**
     * @var Menu $menu
     */
    protected Menu $menu;

    /**
     * ServerScreen constructor.
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
     * Shows the "Server" screen.
     *
     * @return void
     */
    public function show(): void
    {
        $this->prepare();

        $buttons = InlineKeyboard::make()
            ->button(InlineKeyboardButton::make('Reboot Server')->callbackData($this->generateCallbackData(self::NAME, self::ACTION_REBOOT)))
            ->row()
            ->button(InlineKeyboardButton::make('Reboot MySQL')->callbackData($this->generateCallbackData(self::NAME, self::ACTION_REBOOT_MYSQL)))
            ->button(InlineKeyboardButton::make('Reboot PostgreSQL')->callbackData($this->generateCallbackData(self::NAME, self::ACTION_REBOOT_POSTRGESQL)))
            ->row()
            ->button(InlineKeyboardButton::make('Reboot PHP')->callbackData($this->generateCallbackData(self::NAME, self::ACTION_REBOOT_PHP)))
            ->button(InlineKeyboardButton::make('Reboot NGINX')->callbackData($this->generateCallbackData(self::NAME, self::ACTION_REBOOT_NGINX)));

        $sites = LaravelForge::setToken($this->menu->token)->sites($this->menu->server->id);

        foreach ($sites as $site) {
            $buttons->row()->button($this->button($site));
        }

        $buttons->row()->button($this->backButton());

        $this->updateMenu(
            "*{$this->menu->token->name}*\n*Server*: {$this->menu->server->formatted_name}\n\n" .
            "What do you want to do with the server? If you want manage server's sites just select needed one.",
            $buttons
        );
    }

    /**
     * Prepares the screen.
     *
     * @return void
     */
    private function prepare(): void
    {
        $this->menu->site()->delete();

        $this->menu->update([
            'waiting_message_for' => null,
        ]);
    }

    /**
     * Makes a button with a site.
     *
     * @param Site $site
     *
     * @return InlineKeyboardButton
     */
    private function button(Site $site)
    {
        return InlineKeyboardButton::make("🌐 {$site->name}")
            ->callbackData($this->generateCallbackData(self::NAME, $site->id));
    }
}
