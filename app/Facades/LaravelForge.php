<?php

namespace App\Facades;

use App\Contracts\LaravelForgeContract;
use App\Token;
use Illuminate\Support\Facades\Facade;

/**
 * Class LaravelForge.
 *
 * @method static LaravelForgeContract setToken(Token $token)
 */
class LaravelForge extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return LaravelForgeContract::class;
    }
}
