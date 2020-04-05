<?php

use App\DeploymentLog;
use Illuminate\Database\Eloquent\Factory;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

/** @var Factory $factory */

$factory->define(DeploymentLog::class, function () {
    return [
        'server_name' => 'Test server',
        'site_name' => 'Test site',
        'content' => "Rendering Complete, saving .css file...\nWrote CSS to /home/forge/example.com/css/style.css\nDone in 2.62s.",
    ];
});
