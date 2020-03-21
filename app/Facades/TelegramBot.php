<?php

namespace App\Facades;

use App\Integrations\Telegram\TelegramBotApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;

/**
 * Class TelegramBot
 * @package App\Facades
 *
 * @method static void setWebhook(string $hookUrl)
 * @method static void deleteWebhook()
 * @method static void handle(Request $request)
 */
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
