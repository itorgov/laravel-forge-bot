<?php

namespace App\Contracts;

use App\Integrations\Laravel\Forge\Entities\Server;
use App\Integrations\Laravel\Forge\Entities\Site;
use App\Integrations\Laravel\Forge\Entities\Webhook;
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

    /**
     * Returns a server by its id.
     *
     * @param int $serverId
     *
     * @return Server
     */
    public function server(int $serverId): Server;

    /**
     * Reboots the server.
     *
     * @param int $serverId
     *
     * @return void
     */
    public function rebootServer(int $serverId): void;

    /**
     * Reboots MySQL on the server.
     *
     * @param int $serverId
     *
     * @return void
     */
    public function rebootMysql(int $serverId): void;

    /**
     * Reboots PostgreSQL on the server.
     *
     * @param int $serverId
     *
     * @return void
     */
    public function rebootPostgresql(int $serverId): void;

    /**
     * Reboots PHP on the server.
     *
     * @param int $serverId
     *
     * @return void
     */
    public function rebootPhp(int $serverId): void;

    /**
     * Reboots Nginx on the server.
     *
     * @param int $serverId
     *
     * @return void
     */
    public function rebootNginx(int $serverId): void;

    /**
     * Returns collection of server's sites.
     *
     * @param int $serverId
     *
     * @return Collection|Site[]
     */
    public function sites(int $serverId): Collection;

    /**
     * Returns a server's site.
     *
     * @param int $serverId
     * @param int $siteId
     *
     * @return Site
     */
    public function site(int $serverId, int $siteId): Site;

    /**
     * Starts deploying the website.
     *
     * @param int $serverId
     * @param int $siteId
     *
     * @return void
     */
    public function deploySite(int $serverId, int $siteId): void;

    /**
     * Returns collection of site's webhooks.
     *
     * @param int $serverId
     * @param int $siteId
     *
     * @return Collection|Webhook[]
     */
    public function webhooks(int $serverId, int $siteId): Collection;

    /**
     * Creates a new deployment webhook.
     *
     * @param int $serverId
     * @param int $siteId
     * @param string $url
     *
     * @return void
     */
    public function createWebhook(int $serverId, int $siteId, string $url): void;
}
