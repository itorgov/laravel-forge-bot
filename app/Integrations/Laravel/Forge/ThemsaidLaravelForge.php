<?php

namespace App\Integrations\Laravel\Forge;

use App\Contracts\LaravelForgeContract;
use App\Integrations\Laravel\Forge\Exceptions\LaravelForgeException;
use App\Token;
use Exception;
use Illuminate\Support\Collection;
use Themsaid\Forge\Forge;
use Themsaid\Forge\Resources\Server;
use Themsaid\Forge\Resources\Site;
use Themsaid\Forge\Resources\Webhook;

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

    /**
     * @inheritDoc
     */
    public function server(int $serverId): Entities\Server
    {
        try {
            $server = $this->forge->server($serverId);

            return new Entities\Server($server->id, $server->name, $server->ipAddress);
        } catch (Exception $e) {
            throw new LaravelForgeException($e->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function rebootServer(int $serverId): void
    {
        try {
            $this->forge->rebootServer($serverId);
        } catch (Exception $e) {
            throw new LaravelForgeException($e->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function rebootMysql(int $serverId): void
    {
        try {
            $this->forge->rebootMysql($serverId);
        } catch (Exception $e) {
            throw new LaravelForgeException($e->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function rebootPostgresql(int $serverId): void
    {
        try {
            $this->forge->rebootPostgres($serverId);
        } catch (Exception $e) {
            throw new LaravelForgeException($e->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function rebootPhp(int $serverId): void
    {
        try {
            $this->forge->rebootPHP($serverId);
        } catch (Exception $e) {
            throw new LaravelForgeException($e->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function rebootNginx(int $serverId): void
    {
        try {
            $this->forge->rebootNginx($serverId);
        } catch (Exception $e) {
            throw new LaravelForgeException($e->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function sites(int $serverId): Collection
    {
        try {
            return collect($this->forge->sites($serverId))->map(function (Site $site) {
                return new Entities\Site($site->id, $site->name);
            });
        } catch (Exception $e) {
            throw new LaravelForgeException($e->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function site(int $serverId, int $siteId): Entities\Site
    {
        try {
            $site = $this->forge->site($serverId, $siteId);

            return new Entities\Site($site->id, $site->name);
        } catch (Exception $e) {
            throw new LaravelForgeException($e->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function deploySite(int $serverId, int $siteId): void
    {
        try {
            $this->forge->deploySite($serverId, $siteId, false);
        } catch (Exception $e) {
            throw new LaravelForgeException($e->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function webhooks(int $serverId, int $siteId): Collection
    {
        try {
            return collect($this->forge->webhooks($serverId, $siteId))->map(function (Webhook $webhook) {
                return new Entities\Webhook($webhook->id, $webhook->url);
            });
        } catch (Exception $e) {
            throw new LaravelForgeException($e->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function createWebhook(int $serverId, int $siteId, string $url): void
    {
        try {
            $this->forge->createWebhook($serverId, $siteId, [
                'url' => $url,
            ]);
        } catch (Exception $e) {
            throw new LaravelForgeException($e->getMessage());
        }
    }
}
