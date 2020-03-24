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
        $this->menu = Menu::query()->with('user')->firstOrCreate([
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

        switch ($parsedData['type']) {
            case self::SCREEN_TOKENS:
                $token = $this->menu->user->tokens()->find($parsedData['data']);

                if ($token !== null) {
                    $this->menu->token()->associate($token);
                    $this->showServersScreen();
                } else {
                    $this->resetMenu();
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
        $this->menu->token()->dissociate();
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

        OutboundMessage::make($this->menu->user, "*{$this->menu->token->name}*\n\nChoose your token:")
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
