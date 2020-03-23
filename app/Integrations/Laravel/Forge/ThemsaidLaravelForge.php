<?php

namespace App\Integrations\Laravel\Forge;

use App\Contracts\LaravelForgeContract;
use App\Integrations\Laravel\Forge\Exceptions\LaravelForgeException;
use App\Token;
use Exception;
use Themsaid\Forge\Forge;

class ThemsaidLaravelForge implements LaravelForgeContract
{
    /**
     * @var Forge $forge
     */
    private Forge $forge;

    /**
     * ThemsaidLaravelForge constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->forge = new Forge();
    }

    /**
     * @inheritDoc
     */
    public function setToken(Token $token): self
    {
        $this->forge->setApiKey($token->value, null);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function user(): string
    {
        try {
            $user = $this->forge->user();
        } catch (Exception $e) {
            throw new LaravelForgeException($e->getMessage());
        }

        return "{$user->name} <{$user->email}>";
    }
}
