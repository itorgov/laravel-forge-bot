<?php

namespace App\Http\Controllers\Api;

use App\Jobs\SendDeploymentNotificationJob;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LaravelForgeController extends Controller
{
    /**
     * Handle a request from Laravel Forge.
     *
     * @param Request $request
     * @param string $hash
     *
     * @return Response
     */
    public function __invoke(Request $request, string $hash): Response
    {
        logger('Request from Laravel Forge', $request->all());

        $user = User::findOrFailByHash($hash);

        SendDeploymentNotificationJob::dispatch($user, $request->all());

        return response('OK');
    }
}
