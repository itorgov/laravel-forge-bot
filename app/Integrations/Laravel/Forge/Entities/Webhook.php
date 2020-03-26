<?php

namespace App\Integrations\Laravel\Forge\Entities;

use App\User;
use Illuminate\Support\Str;

class Webhook
{
    /**
     * @var int
     */
    public int $id;

    /**
     * @var string
     */
    public string $url;

    /**
     * Determines if webhook didn't create by this bot.
     *
     * @return bool
     */
    public function isAlien(): bool
    {
        return !Str::startsWith($this->url, config('app.url'));
    }

    /**
     * Returns name of the webhook based on chat's name.
     *
     * @return string
     */
    public function name(): string
    {
        if ($this->isAlien()) {
            return $this->url;
        }

        $user = User::findByHash(Str::afterLast($this->url, '/'));

        if ($user === null) {
            return $this->url;
        }

        return $user->name;
    }

    /**
     * Webhook constructor.
     *
     * @param int $id
     * @param string $url
     *
     * @return void
     */
    public function __construct(int $id, string $url)
    {
        $this->id = $id;
        $this->url = $url;
    }
}
