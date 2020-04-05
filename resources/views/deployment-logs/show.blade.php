<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Deployment log [{{ $deploymentLog->site_name }}]</title>

    <!-- Styles -->
    <link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}">
</head>
<body>
<div class="deployment-log">
    <header>
        <h1 class="deployment-log__title">{{ $deploymentLog->site_name }}</h1>
        <div class="deployment-log__subtitle-row">
            <h2 class="deployment-log__subtitle">{{ $deploymentLog->server_name }}</h2>
            <time class="deployment-log__date" datetime="{{ $deploymentLog->created_at->timestamp }}">
                {{ $deploymentLog->formatted_date }}
            </time>
        </div>
    </header>
    <main>
        <pre class="deployment-log__content">{{ $deploymentLog->content }}</pre>
    </main>
</div>

<!--
---------------------
Telegram IV template.
---------------------

~version: "2.1"

?path: /deployment-logs/.+

$header: //header
body: //div[has-class("deployment-log")]

@pre: (//pre)

title: $body//h1
subtitle: $body//h2
published_date: $header//time/@datetime

@remove: $header//time
-->
</body>
</html>
