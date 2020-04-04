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
     * @var Forge
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
     * {@inheritdoc}
     */
    public function setToken(Token $token): self
    {
        $this->forge->setApiKey($token->value, null);

        return $this;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
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

    /**
     * {@inheritdoc}
     */
    public function deleteWebhook(int $serverId, int $siteId, int $webhookId): void
    {
        try {
            $this->forge->deleteWebhook($serverId, $siteId, $webhookId);
        } catch (Exception $e) {
            throw new LaravelForgeException($e->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDeploymentLog(int $serverId, int $siteId): string
    {
        try {
            return $this->forge->siteDeploymentLog($serverId, $siteId);
        } catch (Exception $e) {
            throw new LaravelForgeException($e->getMessage());
        }
    }
}
