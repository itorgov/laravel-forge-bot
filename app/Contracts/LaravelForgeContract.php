<?php

namespace App\Contracts;

use App\Integrations\Laravel\Forge\Entities\Server;
use App\Token;
use Illuminate\Support\Collection;

interface LaravelForgeContract
{
    /**
     * Sets the token to work with Laravel Forge API.
     *
     * @param Token $token
     *
     * @return $this
     */
    public function setToken(Token $token): self;

    /**
     * Returns user's name and email in one string.
     *
     * @return string
     */
    public function user(): string;

    /**
     * Returns collection of servers.
     *
     * @return Collection|Server[]
     */
    public function servers(): Collection;
}
