<?php

namespace App\Http\Controllers\Api;

use App\Facades\TelegramBot;
use Illuminate\Http\Response;

class TelegramBotController extends Controller
{
    public function updates(): Response
    {
        TelegramBot::handle();

        return response('OK');
    }
}
