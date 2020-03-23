<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
     * Finds and returns user by Telegram chat id.
     * Creates a new user if didn't find any.
     *
     * @param string $chatId
     * @param array $paramsForCreate
     *
     * @return static
     */
    public static function firstOrCreateForTelegramChatId(string $chatId, array $paramsForCreate = []): self
    {
        return self::query()->firstOrCreate([
            'telegram_chat_id' => $chatId,
        ], $paramsForCreate);
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
