<?php

namespace App\Facades;

use App\Contracts\TelegramBotContract;
use Illuminate\Support\Facades\Facade;

class TelegramBot extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return TelegramBotContract::class;
    }
}
