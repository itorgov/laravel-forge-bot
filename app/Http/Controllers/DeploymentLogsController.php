<?php

namespace App\Http\Controllers;

use App\DeploymentLog;
use Illuminate\Contracts\Support\Renderable;

class DeploymentLogsController extends Controller
{
    /**
     * Page with deployment log.
     *
     * @param DeploymentLog $deploymentLog
     *
     * @return Renderable
     */
    public function show(DeploymentLog $deploymentLog): Renderable
    {
        return view('deployment-logs.show', [
            'deploymentLog' => $deploymentLog,
        ]);
    }
}
