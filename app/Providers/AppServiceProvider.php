<?php

namespace App\Providers;

use App\Integrations\Telegram\IrazasyedTelegramBotApi;
use App\Integrations\Telegram\TelegramBotApi;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(IrazasyedTelegramBotApi::class, function () {
            return new IrazasyedTelegramBotApi(
                config('services.telegram.bot.api_key')
            );
        });

        $this->app->bind(TelegramBotApi::class, IrazasyedTelegramBotApi::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
