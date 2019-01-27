<?php

/** @var \Laravel\Lumen\Routing\Router $router */
$router->post('/webhook/telegram/' . env('TELEGRAM_WEBHOOK_TOKEN'), 'WebhookController@telegram');
$router->post('/webhook/forge/{token}', 'WebhookController@forge');
