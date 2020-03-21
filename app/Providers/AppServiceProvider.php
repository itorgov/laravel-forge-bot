<?php

namespace App\Providers;

use App\Integrations\Telegram\LongmanTelegramBotApi;
use App\Integrations\Telegram\TelegramBotApi;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Longman\TelegramBot\TelegramLog;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(LongmanTelegramBotApi::class, function () {
            return new LongmanTelegramBotApi(
                config('services.telegram.bot.api_key'),
                config('services.telegram.bot.username')
            );
        });

        $this->app->bind(TelegramBotApi::class, LongmanTelegramBotApi::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        TelegramLog::initialize(Log::getLogger(), Log::getLogger());
    }
}
