<?php

namespace App\Providers;

use App\Contracts\LaravelForgeContract;
use App\Contracts\TelegramBotContract;
use App\Integrations\Laravel\Forge\ThemsaidLaravelForge;
use App\Integrations\Telegram\IrazasyedTelegramBot;
use Hashids\Hashids;
use Illuminate\Support\Facades\URL;
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
        if ($this->app->isLocal()) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }

        $this->app->bind(IrazasyedTelegramBot::class, function () {
            return new IrazasyedTelegramBot(config('services.telegram.bot.api_key'));
        });

        $this->app->bind(Hashids::class, function () {
            // Don't think that somebody will want to change minimal hash lenght.
            return new Hashids(config('app.key'), 20);
        });

        $this->app->bind(TelegramBotContract::class, IrazasyedTelegramBot::class);
        $this->app->bind(LaravelForgeContract::class, ThemsaidLaravelForge::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        URL::forceScheme('https');
    }
}
