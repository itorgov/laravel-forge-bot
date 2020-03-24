<?php

namespace App\Integrations\Laravel\Forge\Entities;

class Server
{
    /**
     * @var int
     */
    public int $id;

    /**
     * @var string
     */
    public string $name;

    /**
     * @var string
     */
    public string $ip;

    /**
     * Server constructor.
     *
     * @param int $id
     * @param string $name
     * @param string $ip
     *
     * @return void
     */
    public function __construct(int $id, string $name, string $ip)
    {
        $this->id = $id;
        $this->name = $name;
        $this->ip = $ip;
    }
}
