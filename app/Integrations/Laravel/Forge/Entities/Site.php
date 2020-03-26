<?php

namespace App\Integrations\Laravel\Forge\Entities;

class Site
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
     * Site constructor.
     *
     * @param int $id
     * @param string $name
     *
     * @return void
     */
    public function __construct(int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }
}
