<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\TelegramRequest;
use App\Jobs\Telegram\SendDeploymentStatusJob;

class WebhookController extends Controller
{
    /**
     * Telegram webhook handler.
     *
     * @param TelegramRequest $request
     *
     * @return Response
     */
    public function telegram(TelegramRequest $request)
    {
        // Execute a first command.
        if ($request->commands->isNotEmpty()) {
            $jobClassName = '\\App\\Jobs\\Telegram\\' . studly_case(str_before(str_after($request->commands->first(), '/'), '@')) . 'Job';

            if (class_exists($jobClassName)) {
                dispatch(new $jobClassName(request('message')));
            }
        }

        return response('OK');
    }

    /**
     * Laravel Forge webhook handler.
     *
     * @param Request $request
     * @param string $token
     *
     * @return Response
     */
    public function forge(Request $request, string $token)
    {
        $webhook = db('webhooks')->where('token', $token)->first();

        if ($webhook !== null) {
            dispatch(new SendDeploymentStatusJob((int)$webhook->chat_id, $request->all()));
        }

        return response('OK');
    }
}
