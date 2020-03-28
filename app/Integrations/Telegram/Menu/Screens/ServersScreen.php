<?php

namespace App\Integrations\Telegram\Menu\Screens;

use App\Facades\LaravelForge;
use App\Integrations\Laravel\Forge\Entities\Server;
use App\Integrations\Telegram\Entities\InlineKeyboard;
use App\Integrations\Telegram\Entities\InlineKeyboardButton;
use App\Menu;

class ServersScreen extends Screen
{
    public const NAME = 'servers';

    /**
     * @var Menu
     */
    protected Menu $menu;

    /**
     * ServersScreen constructor.
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
     * Shows the "Servers" screen.
     *
     * @return void
     */
    public function show(): void
    {
        $this->prepare();

        $servers = LaravelForge::setToken($this->menu->token)->servers();
        $keyboard = InlineKeyboard::make();

        foreach ($servers as $server) {
            $keyboard->row()->button($this->button($server));
        }

        $keyboard = $keyboard->row()->button($this->backButton());

        $this->updateMenu("*{$this->menu->token->name}*\n\nChoose your server:", $keyboard);
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
            'waiting_message_for' => null,
        ]);
    }

    /**
     * Makes a button with a server.
     *
     * @param Server $server
     *
     * @return InlineKeyboardButton
     */
    private function button(Server $server)
    {
        return InlineKeyboardButton::make("{$server->name} ({$server->ip})")
            ->callbackData($this->generateCallbackData(self::NAME, $server->id));
    }
}
