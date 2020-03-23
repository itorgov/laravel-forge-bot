<?php

namespace App\Contracts;

use App\Token;

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
}
