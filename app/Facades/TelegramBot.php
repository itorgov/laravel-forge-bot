<?php

namespace App\Facades;

use App\Integrations\Telegram\TelegramBotApi;
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
        return TelegramBotApi::class;
    }
}
