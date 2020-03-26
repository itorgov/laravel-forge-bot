<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Hashids
 * @package App\Facades
 *
 * @method static string encode(...$numbers) Encode parameters to generate a hash.
 * @method static array decode(string $hash) Decode a hash to the original parameter values.
 */
class Hashids extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \Hashids\Hashids::class;
    }
}
