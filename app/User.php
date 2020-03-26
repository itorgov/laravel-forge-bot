<?php

namespace App;

use App\Facades\Hashids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Arr;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Finds and returns the user by Telegram chat id.
     *
     * @param string $chatId
     *
     * @return static
     */
    public static function findByTelegramChatId(string $chatId): ?self
    {
        return self::query()->where('telegram_chat_id', $chatId)->first();
    }

    /**
     * Finds and returns the user by Telegram chat id.
     * Creates a new user if didn't find any.
     *
     * @param string $chatId
     * @param array $paramsForCreate
     *
     * @return static
     */
    public static function findOrCreateByTelegramChatId(string $chatId, array $paramsForCreate = []): self
    {
        return self::query()->firstOrCreate([
            'telegram_chat_id' => $chatId,
        ], $paramsForCreate);
    }

    /**
     * Finds the user by hash.
     *
     * @param string $hash
     *
     * @return static
     */
    public static function findByHash(string $hash): self
    {
        $id = Arr::first(Hashids::decode($hash));

        return self::query()->find($id);
    }

    /**
     * Finds the user by hash.
     *
     * @param string $hash
     *
     * @return static
     */
    public static function findOrFailByHash(string $hash): self
    {
        $id = Arr::first(Hashids::decode($hash));

        return self::query()->findOrFail($id);
    }

    /**
     * Returns a hash for user's id.
     *
     * @return string
     */
    public function hash()
    {
        return Hashids::encode($this->id);
    }

    /**
     * Returns a URL for Laravel Forge webhook.
     *
     * @return string
     */
    public function forgeWebhookUrl(): string
    {
        return route('integrations.forge.webhook', ['hash' => $this->hash()]);
    }

    /**
     * Finishes all current user's dialogs.
     *
     * @return void
     */
    public function finishCurrentDialogs(): void
    {
        $this->dialogs()->current()->get()->each->finish();
    }

    /**
     * User's tokens.
     *
     * @return HasMany
     */
    public function tokens(): HasMany
    {
        return $this->hasMany(Token::class);
    }

    /**
     * User's dialogs.
     *
     * @return HasMany
     */
    public function dialogs(): HasMany
    {
        return $this->hasMany(Dialog::class);
    }
}
