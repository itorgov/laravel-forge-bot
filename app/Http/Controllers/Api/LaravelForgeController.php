<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LaravelForgeController extends Controller
{
    /**
     * Handle a request from Laravel Forge.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function __invoke(Request $request): Response
    {
        logger('Request from Laravel Forge', $request->all());

        return response('OK');
    }
}
