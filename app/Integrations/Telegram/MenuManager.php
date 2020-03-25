<?php

namespace App\Integrations\Telegram;

use App\Facades\LaravelForge;
use App\Integrations\Laravel\Forge\Entities\Server;
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
     * Handles returned data (callback data) from user's action.
     *
     * @param string $callbackData
     *
     * @return void
     */
    public function handle(string $callbackData): void
    {
        $parsedData = $this->parseCallbackData($callbackData);

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
                } else {
                    $this->backToTokensScreen();
                }

                break;

            case self::SCREEN_SERVERS:
                if (!empty($parsedData['data'])) {
                    $server = LaravelForge::setToken($this->menu->token)->server($parsedData['data']);
                    $this->goToServerScreen($server);
                } else {
                    $this->backToServersScreen();
                }
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

        OutboundMessage::make($this->menu->user, 'Choose your token:')
            ->withInlineKeyboard($buttons)
            ->edit($this->menu->message_id);
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

        $buttons = $buttons->row()->button($this->backButton(self::SCREEN_TOKENS));

        OutboundMessage::make($this->menu->user, "*{$this->menu->token->name}*\n\nChoose your server:")
            ->parseMode(OutboundMessage::PARSE_MODE_MARKDOWN)
            ->withInlineKeyboard($buttons)
            ->edit($this->menu->message_id);
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
        $buttons = InlineKeyboard::make()
            ->button(InlineKeyboardButton::make('Reboot server')->callbackData($this->generateCallbackData(self::SCREEN_SERVER, 'reboot')))
            ->row()
            ->button(InlineKeyboardButton::make('Reboot MySQL')->callbackData($this->generateCallbackData(self::SCREEN_SERVER, 'reboot-mysql')))
            ->button(InlineKeyboardButton::make('Reboot PostgreSQL')->callbackData($this->generateCallbackData(self::SCREEN_SERVER, 'reboot-postgresql')))
            ->row()
            ->button(InlineKeyboardButton::make('Reboot PHP')->callbackData($this->generateCallbackData(self::SCREEN_SERVER, 'reboot-php')))
            ->button(InlineKeyboardButton::make('Reboot NGINX')->callbackData($this->generateCallbackData(self::SCREEN_SERVER, 'reboot-nginx')))
            ->row()
            ->button($this->backButton(self::SCREEN_SERVERS));

        OutboundMessage::make($this->menu->user, "*{$this->menu->token->name}*\n\n*Server*: {$this->menu->server_name}\n\nWhat do you want to do with the server?")
            ->parseMode(OutboundMessage::PARSE_MODE_MARKDOWN)
            ->withInlineKeyboard($buttons)
            ->edit($this->menu->message_id);
    }

    /**
     * Makes a "Back" button for selected screen.
     *
     * @param string $screen Target screen (typically it's previous).
     *
     * @return InlineKeyboardButton
     */
    private function backButton(string $screen)
    {
        return InlineKeyboardButton::make('Back')
            ->callbackData($this->generateCallbackData($screen, ''));
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
