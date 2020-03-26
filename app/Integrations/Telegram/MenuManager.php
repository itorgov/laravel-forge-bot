<?php

namespace App\Integrations\Telegram;

use App\Facades\LaravelForge;
use App\Integrations\Laravel\Forge\Entities\Server;
use App\Integrations\Laravel\Forge\Entities\Site;
use App\Integrations\Laravel\Forge\Entities\Webhook;
use App\Integrations\Telegram\Entities\CallbackQueryAnswer;
use App\Integrations\Telegram\Entities\InlineKeyboard;
use App\Integrations\Telegram\Entities\InlineKeyboardButton;
use App\Integrations\Telegram\Entities\OutboundMessage;
use App\Menu;
use App\Token;
use App\User;
use Exception;
use Illuminate\Support\Str;

class MenuManager
{
    private const SCREEN_TOKENS = 'tokens';
    private const SCREEN_SERVERS = 'servers';
    private const SCREEN_SERVER = 'server';
    private const SCREEN_SITE = 'site';
    private const SCREEN_ADD_WEBHOOK = 'add-webhook';

    private const SERVER_REBOOT = 'reboot';
    private const SERVER_REBOOT_MYSQL = 'reboot-mysql';
    private const SERVER_REBOOT_POSTRGESQL = 'reboot-postgresql';
    private const SERVER_REBOOT_PHP = 'reboot-php';
    private const SERVER_REBOOT_NGINX = 'reboot-nginx';

    private const SITE_DEPLOY = 'deploy';
    private const SITE_ADD_WEBHOOK = 'add-webhook';

    private const ADD_WEBHOOK_THIS = 'this';
    private const ADD_WEBHOOK_ANOTHER = 'another';

    private Menu $menu;

    /**
     * MenuManager constructor.
     *
     * @param User $user
     * @param $messageId
     *
     * @return void
     */
    private function __construct(User $user, $messageId)
    {
        $this->menu = Menu::query()->with('user', 'token')->firstOrCreate([
            'user_id' => $user->id,
            'message_id' => $messageId,
        ]);

        if ($this->menu->wasRecentlyCreated) {
            $this->showTokensScreen();
        }
    }

    /**
     * Make an instanse. Just for convenience.
     *
     * @param User $user
     * @param $messageId
     *
     * @return static
     */
    public static function make(User $user, $messageId): self
    {
        return new self($user, $messageId);
    }

    /**
     * Handles returned id and data from callback.
     *
     * @param string $id
     * @param string $data
     *
     * @return void
     */
    public function handle(string $id, string $data): void
    {
        $parsedData = $this->parseCallbackData($data);

        // Reset the menu if token missed and it's not a select token screen.
        if ($this->menu->token === null && $parsedData['type'] !== self::SCREEN_TOKENS) {
            $this->resetMenu();

            return;
        }

        switch ($parsedData['type']) {
            case self::SCREEN_TOKENS:
                $token = $this->menu->user->tokens()->find($parsedData['data']);

                if ($token !== null) {
                    $this->goToServersScreen($token);
                }

                break;

            case self::SCREEN_SERVERS:
                if (empty($parsedData['data'])) {
                    $this->backToTokensScreen();
                } else {
                    $server = LaravelForge::setToken($this->menu->token)->server($parsedData['data']);
                    $this->goToServerScreen($server);
                }

                break;

            case self::SCREEN_SERVER:
                switch ($parsedData['data']) {
                    case self::SERVER_REBOOT:
                        LaravelForge::setToken($this->menu->token)->rebootServer($this->menu->server_id);
                        CallbackQueryAnswer::make($id, 'Rebooting Server (give it a minute)...')->showAsModal()->send();
                        break;
                    case self::SERVER_REBOOT_MYSQL:
                        LaravelForge::setToken($this->menu->token)->rebootMysql($this->menu->server_id);
                        CallbackQueryAnswer::make($id, 'Rebooting MySQL Server...')->showAsModal()->send();
                        break;
                    case self::SERVER_REBOOT_POSTRGESQL:
                        LaravelForge::setToken($this->menu->token)->rebootPostgresql($this->menu->server_id);
                        CallbackQueryAnswer::make($id, 'Rebooting Postgres Server...')->showAsModal()->send();
                        break;
                    case self::SERVER_REBOOT_PHP:
                        LaravelForge::setToken($this->menu->token)->rebootPhp($this->menu->server_id);
                        CallbackQueryAnswer::make($id, 'Rebooting PHP...')->showAsModal()->send();
                        break;
                    case self::SERVER_REBOOT_NGINX:
                        LaravelForge::setToken($this->menu->token)->rebootNginx($this->menu->server_id);
                        CallbackQueryAnswer::make($id, 'Rebooting Nginx Server...')->showAsModal()->send();
                        break;
                    default:
                        $siteId = $parsedData['data'];

                        if (empty($siteId)) {
                            $this->backToServersScreen();
                        } else {
                            $site = LaravelForge::setToken($this->menu->token)->site($this->menu->server_id, $siteId);
                            $this->goToSiteScreen($site);
                        }
                }

                break;

            case self::SCREEN_SITE:
                switch ($parsedData['data']) {
                    case self::SITE_DEPLOY:
                        LaravelForge::setToken($this->menu->token)->deploySite($this->menu->server_id, $this->menu->site_id);
                        CallbackQueryAnswer::make($id, 'Deploying pushed code...')->showAsModal()->send();

                        break;

                    case self::SITE_ADD_WEBHOOK:
                        $this->goToAddWebhookScreen();

                        break;

                    default:
                        $webhookId = $parsedData['data'];

                        if (empty($webhookId)) {
                            $this->backToServerScreen();
                        } else {
                            // Go to webhook page.
                        }
                }

                break;

            case self::SCREEN_ADD_WEBHOOK:
                switch ($parsedData['data']) {
                    case self::ADD_WEBHOOK_THIS:
                        LaravelForge::setToken($this->menu->token)->createWebhook(
                            $this->menu->server_id,
                            $this->menu->site_id,
                            $this->menu->user->forgeWebhookUrl()
                        );
                        CallbackQueryAnswer::make($id, 'Deployment Webhook successfully added!')->send();
                        $this->backToSiteScreen();

                        break;

                    case self::ADD_WEBHOOK_ANOTHER:
                        //

                        break;

                    default:
                        $this->backToSiteScreen();
                }

                break;
        }
    }

    /**
     * Removes all existing data and opens first screen (tokens).
     *
     * @return void
     */
    private function resetMenu(): void
    {
        $this->menu->update([
            'token_id' => null,
            'server_id' => null,
            'server_name' => null,
            'site_id' => null,
            'site_name' => null,
            'waiting_message_for' => null,
        ]);

        $this->showTokensScreen();
    }

    /**
     * The first screen.
     * Shows list of user's tokens.
     *
     * @return void
     */
    private function showTokensScreen(): void
    {
        $tokens = $this->menu->user->tokens;

        if ($tokens->isEmpty()) {
            OutboundMessage::make($this->menu->user, 'You haven\'t any Laravel Forge API tokens yet. Please add one by using /addtoken command.')->send();

            try {
                $this->menu->delete();
            } catch (Exception $exception) {
                // This exception happes really rarely. Almost never.
                // So, just report about it.
                report($exception);
            }

            return;
        }

        $buttons = InlineKeyboard::make();

        foreach ($tokens as $token) {
            $buttons = $buttons->row()->button($this->tokenButton($token));
        }

        $this->updateMenu('Choose your token:', $buttons);
    }

    /**
     * Syntactic shugar. Just for readability.
     *
     * @return void
     */
    private function backToTokensScreen(): void
    {
        $this->resetMenu();
    }

    /**
     * Opens the servers screen.
     *
     * @param Token $token
     *
     * @return void
     */
    private function goToServersScreen(Token $token): void
    {
        $this->menu->token()->associate($token);
        $this->menu->save();
        $this->showServersScreen();
    }

    /**
     * The second screen.
     * Shows list of servers for chosen token.
     *
     * @return void
     */
    private function showServersScreen(): void
    {
        $servers = LaravelForge::setToken($this->menu->token)->servers();
        $buttons = InlineKeyboard::make();

        foreach ($servers as $server) {
            $buttons = $buttons->row()->button($this->serverButton($server));
        }

        $buttons = $buttons->row()->button($this->backToTokensButton());

        $this->updateMenu("*{$this->menu->token->name}*\n\nChoose your server:", $buttons);
    }

    /**
     * Returns user to the servers screen.
     *
     * @return void
     */
    private function backToServersScreen(): void
    {
        $this->menu->update([
            'server_id' => null,
            'server_name' => null,
        ]);

        $this->showServersScreen();
    }

    /**
     * Opens the server screen.
     *
     * @param Server $server
     *
     * @return void
     */
    private function goToServerScreen(Server $server): void
    {
        $this->menu->update([
            'server_id' => $server->id,
            'server_name' => "{$server->name} ({$server->ip})",
        ]);

        $this->showServerScreen();
    }

    /**
     * The third screen.
     * Shows action which user can do with a server.
     * Also shows server's sites.
     *
     * @return void
     */
    private function showServerScreen(): void
    {
        $sites = LaravelForge::setToken($this->menu->token)->sites($this->menu->server_id);

        $buttons = InlineKeyboard::make()
            ->button(InlineKeyboardButton::make('Reboot Server')->callbackData($this->generateCallbackData(self::SCREEN_SERVER, self::SERVER_REBOOT)))
            ->row()
            ->button(InlineKeyboardButton::make('Reboot MySQL')->callbackData($this->generateCallbackData(self::SCREEN_SERVER, self::SERVER_REBOOT_MYSQL)))
            ->button(InlineKeyboardButton::make('Reboot PostgreSQL')->callbackData($this->generateCallbackData(self::SCREEN_SERVER, self::SERVER_REBOOT_POSTRGESQL)))
            ->row()
            ->button(InlineKeyboardButton::make('Reboot PHP')->callbackData($this->generateCallbackData(self::SCREEN_SERVER, self::SERVER_REBOOT_PHP)))
            ->button(InlineKeyboardButton::make('Reboot NGINX')->callbackData($this->generateCallbackData(self::SCREEN_SERVER, self::SERVER_REBOOT_NGINX)));

        foreach ($sites as $site) {
            $buttons->row()->button($this->siteButton($site));
        }

        $buttons->row()->button($this->backToServersButton());

        $this->updateMenu(
            "*{$this->menu->token->name}*\n*Server*: {$this->menu->server_name}\n\n" .
            "What do you want to do with the server? If you want manage server's sites just select needed one.",
            $buttons
        );
    }

    /**
     * Returns user to the server screen.
     *
     * @return void
     */
    private function backToServerScreen(): void
    {
        $this->menu->update([
            'site_id' => null,
            'site_name' => null,
        ]);

        $this->showServerScreen();
    }

    /**
     * Opens the site screen.
     *
     * @param Site $site
     *
     * @return void
     */
    private function goToSiteScreen(Site $site): void
    {
        $this->menu->update([
            'site_id' => $site->id,
            'site_name' => $site->name,
        ]);

        $this->showSiteScreen();
    }

    /**
     * The fourth screen.
     * Shows action which user can do with a site.
     * Also shows site's webhooks.
     *
     * @return void
     */
    private function showSiteScreen(): void
    {
        $buttons = InlineKeyboard::make()
            ->button(InlineKeyboardButton::make('Deploy Now')->callbackData($this->generateCallbackData(self::SCREEN_SITE, self::SITE_DEPLOY)))
            ->row()
            ->button(InlineKeyboardButton::make('Add Notification Webhook')->callbackData($this->generateCallbackData(self::SCREEN_SITE, self::SITE_ADD_WEBHOOK)));

        $webhooks = LaravelForge::setToken($this->menu->token)->webhooks($this->menu->server_id, $this->menu->site_id);

        foreach ($webhooks as $webhook) {
            if ($webhook->isAlien()) {
                continue;
            }

            $buttons->row()->button($this->webhookButton($webhook));
        }

        $buttons->row()->button($this->backToServerButton());

        $this->updateMenu(
            "*{$this->menu->token->name}*\n*Server*: {$this->menu->server_name}\n*Site*: {$this->menu->site_name}\n\n" .
            "What do you want to do with the site? You can also manage your site's webhooks, you can see them at the bottom.",
            $buttons
        );
    }

    /**
     * Returns user to the site screen.
     *
     * @return void
     */
    private function backToSiteScreen(): void
    {
        $this->menu->update([
            'waiting_message_for' => null,
        ]);

        $this->showSiteScreen();
    }

    /**
     * Opens the "Add Webhook" screen.
     *
     * @return void
     */
    private function goToAddWebhookScreen(): void
    {
        $this->showAddWebhookScreen();
    }

    /**
     * The fifth screen.
     * Determine chat for notifications.
     *
     * @return void
     */
    private function showAddWebhookScreen(): void
    {
        $buttons = InlineKeyboard::make()
            ->button(InlineKeyboardButton::make('To this chat')->callbackData($this->generateCallbackData(self::SCREEN_ADD_WEBHOOK, self::ADD_WEBHOOK_THIS)))
            ->row()
            ->button(InlineKeyboardButton::make('To another chat')->callbackData($this->generateCallbackData(self::SCREEN_ADD_WEBHOOK, self::ADD_WEBHOOK_ANOTHER)));

        $buttons->row()->button($this->backToSiteButton());

        $this->updateMenu(
            "*{$this->menu->token->name}*\n*Server*: {$this->menu->server_name}\n*Site*: {$this->menu->site_name}\n\n" .
            "To which chat do you want to get deployment notifications?",
            $buttons
        );
    }

    /**
     * Updates the menu message.
     *
     * @param string $text
     * @param InlineKeyboard $buttons
     *
     * @return void
     */
    private function updateMenu(string $text, InlineKeyboard $buttons): void
    {
        OutboundMessage::make($this->menu->user, $text)
            ->parseMode(OutboundMessage::PARSE_MODE_MARKDOWN)
            ->withInlineKeyboard($buttons)
            ->edit($this->menu->message_id);
    }

    /**
     * Makes a "Back" button for selected screen.
     *
     * @param string $screen Current screen.
     *
     * @return InlineKeyboardButton
     */
    private function backButton(string $screen)
    {
        return InlineKeyboardButton::make('← Back')
            ->callbackData($this->generateCallbackData($screen, ''));
    }

    /**
     * "Back" button on "Servers" screen.
     *
     * @return InlineKeyboardButton
     */
    private function backToTokensButton()
    {
        return $this->backButton(self::SCREEN_SERVERS);
    }

    /**
     * "Back" button on "Server" screen.
     *
     * @return InlineKeyboardButton
     */
    private function backToServersButton()
    {
        return $this->backButton(self::SCREEN_SERVER);
    }

    /**
     * "Back" button on "Site" screen.
     *
     * @return InlineKeyboardButton
     */
    private function backToServerButton()
    {
        return $this->backButton(self::SCREEN_SITE);
    }

    /**
     * "Back" button on "Add Webhook" screen.
     *
     * @return InlineKeyboardButton
     */
    private function backToSiteButton()
    {
        return $this->backButton(self::SCREEN_ADD_WEBHOOK);
    }

    /**
     * Makes a button for the first (tokens) screen.
     *
     * @param Token $token
     *
     * @return InlineKeyboardButton
     */
    private function tokenButton(Token $token)
    {
        return InlineKeyboardButton::make($token->name)
            ->callbackData($this->generateCallbackData(self::SCREEN_TOKENS, $token->id));
    }

    /**
     * Makes a button for the second (servers) screen.
     *
     * @param Server $server
     *
     * @return InlineKeyboardButton
     */
    private function serverButton(Server $server)
    {
        return InlineKeyboardButton::make("{$server->name} ({$server->ip})")
            ->callbackData($this->generateCallbackData(self::SCREEN_SERVERS, $server->id));
    }

    /**
     * Makes a button for the third (server) screen.
     *
     * @param Site $site
     *
     * @return InlineKeyboardButton
     */
    private function siteButton(Site $site)
    {
        return InlineKeyboardButton::make("🌐 {$site->name}")
            ->callbackData($this->generateCallbackData(self::SCREEN_SERVER, $site->id));
    }

    /**
     * Makes a button for the fourth (site) screen.
     *
     * @param Webhook $webhook
     *
     * @return InlineKeyboardButton
     */
    private function webhookButton(Webhook $webhook)
    {
        return InlineKeyboardButton::make("💬 {$webhook->name()}")
            ->callbackData($this->generateCallbackData(self::SCREEN_SITE, $webhook->id));
    }

    /**
     * Packs data for the callback query.
     *
     * @param string $screen
     * @param string $data
     *
     * @return string
     */
    private function generateCallbackData(string $screen, string $data)
    {
        return "{$screen}:{$data}";
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
