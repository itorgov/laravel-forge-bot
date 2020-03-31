<?php

namespace App\Integrations\Telegram\Menu;

use App\Facades\LaravelForge;
use App\Integrations\Telegram\Dialogs\AskForChatIdDialog;
use App\Integrations\Telegram\Entities\CallbackQueryAnswer;
use App\Integrations\Telegram\Menu\Screens\AddWebhookScreen;
use App\Integrations\Telegram\Menu\Screens\ServerScreen;
use App\Integrations\Telegram\Menu\Screens\ServersScreen;
use App\Integrations\Telegram\Menu\Screens\SiteScreen;
use App\Integrations\Telegram\Menu\Screens\TokensScreen;
use App\Menu;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MenuManager
{
    /**
     * @var Menu
     */
    private Menu $menu;

    /**
     * @var ScreenManager
     */
    private ScreenManager $screens;

    /**
     * MenuManager constructor.
     *
     * @param User $user
     * @param int $messageId
     *
     * @return void
     */
    private function __construct(User $user, int $messageId)
    {
        $this->menu = Menu::query()
            ->with('user', 'token', 'server', 'site')
            ->firstOrCreate([
                'user_id' => $user->id,
                'message_id' => $messageId,
            ]);

        $this->screens = new ScreenManager($this->menu);

        if ($this->menu->wasRecentlyCreated) {
            $this->screens->screen(TokensScreen::NAME)->show();
        }
    }

    /**
     * Make an instanse. Just for convenience.
     *
     * @param int $messageId
     *
     * @return static
     */
    public static function forMessageId(int $messageId): self
    {
        return new self(Auth::user(), $messageId);
    }

    /**
     * Handles a callback.
     *
     * @param string $id
     * @param string $data
     *
     * @return void
     */
    public function handleCallback(string $id, string $data): void
    {
        $parsedData = $this->parseCallbackData($data);

        switch ($parsedData['type']) {
            case TokensScreen::NAME:
                $this->tokensScreenAction($parsedData['data']);

                break;

            case ServersScreen::NAME:
                $this->serversScreenAction($parsedData['data']);

                break;

            case ServerScreen::NAME:
                $this->serverScreenAction($id, $parsedData['data']);

                break;

            case SiteScreen::NAME:
                $this->siteScreenAction($id, $parsedData['data']);

                break;

            case AddWebhookScreen::NAME:
                $this->addWebhookScreenAction($id, $parsedData['data']);

                break;
        }
    }

    /**
     * Handles a text message.
     *
     * @param string $text
     *
     * @return void
     */
    public function handleMessage(string $text): void
    {
        switch ($this->menu->waiting_message_for) {
            case sprintf('%s,%s', AddWebhookScreen::NAME, AddWebhookScreen::ACTION_ANOTHER):
                $currentDialog = Auth::user()->dialogs()->named(AskForChatIdDialog::class)->current()->first();

                if ($currentDialog === null) {
                    // Back to the "Site" screen.
                    $this->screens->screen(SiteScreen::NAME)->show();
                    break;
                }

                if ($currentDialog->nextStep($text)->isFinished()) {
                    $this->addWebhookScreenAction($currentDialog->data['additional_data']['callback_id'], $text);
                }

                break;
        }
    }

    /**
     * Callback from the "Tokens" screen.
     *
     * @param string $tokenId
     *
     * @return void
     */
    private function tokensScreenAction(string $tokenId): void
    {
        $token = $this->menu->user->tokens()->find($tokenId);

        if ($token === null) {
            return;
        }

        $this->menu->token()->associate($token);
        $this->menu->save();

        // To the "Servers" screen.
        $this->screens->screen(ServersScreen::NAME)->show();
    }

    /**
     * Callback from the "Servers" screen.
     *
     * @param string $serverId
     *
     * @return void
     */
    private function serversScreenAction(string $serverId): void
    {
        if (empty($serverId)) {
            // Back to the "Tokens" screen.
            $this->screens->screen(TokensScreen::NAME)->show();

            return;
        }

        $server = LaravelForge::setToken($this->menu->token)->server($serverId);

        $this->menu->server()->delete();
        $this->menu->server()->create([
            'id' => $server->id,
            'name' => $server->name,
            'ip' => $server->ip,
        ]);
        $this->menu->load('server');

        // To the "Server" screen.
        $this->screens->screen(ServerScreen::NAME)->show();
    }

    /**
     * Callback from the "Server" screen.
     *
     * @param string $callbackId
     * @param string $callbackData
     *
     * @return void
     */
    private function serverScreenAction(string $callbackId, string $callbackData): void
    {
        if (empty($callbackData)) {
            // Back to the "Servers" screen.
            $this->screens->screen(ServersScreen::NAME)->show();

            return;
        }

        switch ($callbackData) {
            case ServerScreen::ACTION_REBOOT:
                LaravelForge::setToken($this->menu->token)->rebootServer($this->menu->server->id);
                CallbackQueryAnswer::make($callbackId, 'Rebooting Server (give it a minute)...')->showAsModal()->send();
                break;
            case ServerScreen::ACTION_REBOOT_MYSQL:
                LaravelForge::setToken($this->menu->token)->rebootMysql($this->menu->server->id);
                CallbackQueryAnswer::make($callbackId, 'Rebooting MySQL Server...')->showAsModal()->send();
                break;
            case ServerScreen::ACTION_REBOOT_POSTRGESQL:
                LaravelForge::setToken($this->menu->token)->rebootPostgresql($this->menu->server->id);
                CallbackQueryAnswer::make($callbackId, 'Rebooting Postgres Server...')->showAsModal()->send();
                break;
            case ServerScreen::ACTION_REBOOT_PHP:
                LaravelForge::setToken($this->menu->token)->rebootPhp($this->menu->server->id);
                CallbackQueryAnswer::make($callbackId, 'Rebooting PHP...')->showAsModal()->send();
                break;
            case ServerScreen::ACTION_REBOOT_NGINX:
                LaravelForge::setToken($this->menu->token)->rebootNginx($this->menu->server->id);
                CallbackQueryAnswer::make($callbackId, 'Rebooting Nginx Server...')->showAsModal()->send();
                break;
            default:
                $site = LaravelForge::setToken($this->menu->token)->site($this->menu->server->id, $callbackData);

                $this->menu->site()->delete();
                $this->menu->site()->create([
                    'id' => $site->id,
                    'name' => $site->name,
                ]);
                $this->menu->load('site');

                // To the "Site" screen.
                $this->screens->screen(SiteScreen::NAME)->show();
        }
    }

    /**
     * Callback from the "Site" screen.
     *
     * @param string $callbackId
     * @param string $callbackData
     *
     * @return void
     */
    private function siteScreenAction(string $callbackId, string $callbackData): void
    {
        if (empty($callbackData)) {
            // Back to the "Server" screen.
            $this->screens->screen(ServerScreen::NAME)->show();

            return;
        }

        switch ($callbackData) {
            case SiteScreen::ACTION_DEPLOY:
                LaravelForge::setToken($this->menu->token)->deploySite($this->menu->server->id, $this->menu->site->id);
                CallbackQueryAnswer::make($callbackId, 'Deploying pushed code...')->showAsModal()->send();

                break;

            case SiteScreen::ACTION_ADD_WEBHOOK:
                // To the "Add webhook" screen.
                $this->screens->screen(AddWebhookScreen::NAME)->show();

                break;

            default:
                LaravelForge::setToken($this->menu->token)->deleteWebhook($this->menu->server->id, $this->menu->site->id, $callbackData);
                CallbackQueryAnswer::make($callbackId, 'Webhook successfully deleted!')->showAsModal()->send();

                // Refresh the screen.
                $this->screens->screen(SiteScreen::NAME)->show();
        }
    }

    /**
     * Callback from the "Add webhook" screen.
     *
     * @param string $callbackId
     * @param string $callbackData
     *
     * @return void
     */
    private function addWebhookScreenAction(string $callbackId, string $callbackData): void
    {
        if (empty($callbackData)) {
            // Back to the "Site" screen.
            $this->screens->screen(SiteScreen::NAME)->show();

            return;
        }

        switch ($callbackData) {
            case AddWebhookScreen::ACTION_THIS:
                LaravelForge::setToken($this->menu->token)->createWebhook(
                    $this->menu->server->id,
                    $this->menu->site->id,
                    $this->menu->user->forgeWebhookUrl()
                );
                CallbackQueryAnswer::make($callbackId, 'Deployment Webhook successfully added!')->send();

                // Back to the "Site" screen.
                $this->screens->screen(SiteScreen::NAME)->show();

                break;

            case AddWebhookScreen::ACTION_ANOTHER:
                $this->menu->user->finishAllCurrentDialogs();
                $this->menu->update([
                    'waiting_message_for' => vsprintf('%s,%s', [
                        AddWebhookScreen::NAME,
                        AddWebhookScreen::ACTION_ANOTHER,
                    ]),
                ]);

                AskForChatIdDialog::start([
                    'callback_id' => $callbackId,
                ]);

                break;

            default:
                $user = User::findByTelegramChatId($callbackData);

                LaravelForge::setToken($this->menu->token)->createWebhook(
                    $this->menu->server->id,
                    $this->menu->site->id,
                    $user->forgeWebhookUrl()
                );
                CallbackQueryAnswer::make($callbackId, 'Deployment Webhook successfully added!')->send();

                // Back to the "Site" screen.
                $this->screens->screen(SiteScreen::NAME)->show();
        }
    }

    /**
     * Unpacks data from the Telegram's callback.
     *
     * @param string $data
     *
     * @return array
     */
    private function parseCallbackData(string $data)
    {
        return [
            'type' => Str::before($data, ':'),
            'data' => Str::after($data, ':'),
        ];
    }
}
