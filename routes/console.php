<?php

use App\Facades\TelegramBot;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('telegram:set-webhook', function () {
    TelegramBot::setWebhook(route('integrations.telegram.webhook'));
})->describe('Set a url for receiving incoming updates from Telegram servers.');

Artisan::command('telegram:delete-webhook', function () {
    TelegramBot::deleteWebhook();
})->describe('Remove webhook integration.');
