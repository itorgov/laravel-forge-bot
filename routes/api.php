<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post(
    'integrations/telegram/' . config('services.telegram.bot.webhook_token') . '/webhook',
    'TelegramBotController@updates'
)->middleware('auth.telegram')->name('integrations.telegram.webhook');
