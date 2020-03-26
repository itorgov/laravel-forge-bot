<?php

namespace App\Integrations\Telegram\Menu;

use App\Integrations\Telegram\Exceptions\UnknownScreenException;
use App\Integrations\Telegram\Menu\Screens\AddWebhookScreen;
use App\Integrations\Telegram\Menu\Screens\Screen;
use App\Integrations\Telegram\Menu\Screens\ServerScreen;
use App\Integrations\Telegram\Menu\Screens\ServersScreen;
use App\Integrations\Telegram\Menu\Screens\SiteScreen;
use App\Integrations\Telegram\Menu\Screens\TokensScreen;
use App\Menu;

class ScreenManager
{
    /**
     * @var Menu $menu
     */
    protected Menu $menu;

    /**
     * ScreenManager constructor.
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
     * Returns needed screen instance.
     *
     * @param string $name Screen name.
     *
     * @return Screen
     */
    public function screen(string $name): Screen
    {
        switch ($name) {
            case TokensScreen::NAME:
                return new TokensScreen($this->menu);
            case ServersScreen::NAME:
                return new ServersScreen($this->menu);
            case ServerScreen::NAME:
                return new ServerScreen($this->menu);
            case SiteScreen::NAME:
                return new SiteScreen($this->menu);
            case AddWebhookScreen::NAME:
                return new AddWebhookScreen($this->menu);
            default:
                throw new UnknownScreenException;
        }
    }
}
