<?php

namespace App\Integrations\Laravel\Forge;

use App\Contracts\LaravelForgeContract;
use App\Integrations\Laravel\Forge\Exceptions\LaravelForgeException;
use App\Token;
use Exception;
use Illuminate\Support\Collection;
use Themsaid\Forge\Forge;
use Themsaid\Forge\Resources\Server;

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

    /**
     * @inheritDoc
     */
    public function servers(): Collection
    {
        try {
            return collect($this->forge->servers())->map(function (Server $server) {
                return new Entities\Server($server->id, $server->name, $server->ipAddress);
            });
        } catch (Exception $e) {
            throw new LaravelForgeException($e->getMessage());
        }
    }
}
