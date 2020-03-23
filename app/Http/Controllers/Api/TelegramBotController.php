<?php

namespace App\Http\Controllers\Api;

use App\Facades\TelegramBot;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TelegramBotController extends Controller
{
    /**
     * Handle an update from Telegram.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function updates(Request $request): Response
    {
        TelegramBot::handle($request);

        return response('OK');
    }
}
