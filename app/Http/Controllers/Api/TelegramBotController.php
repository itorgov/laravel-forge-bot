<?php

namespace App\Http\Controllers\Api;

use App\Facades\TelegramBot;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TelegramBotController extends Controller
{
    public function updates(Request $request): Response
    {
        TelegramBot::handle($request);

        return response('OK');
    }
}
