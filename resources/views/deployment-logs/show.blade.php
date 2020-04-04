<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Deployment log [{{ $deploymentLog->site_name }}]</title>

    <!-- Styles -->
    <style>
        @font-face {
            font-family: 'JetBrains Mono';
            src: url('https://raw.githubusercontent.com/JetBrains/JetBrainsMono/master/web/woff2/JetBrainsMono-Regular.woff2') format('woff2');
            font-weight: normal;
            font-style: normal;
        }

        html {
            box-sizing: border-box;
            font-size: 62.5%; /* 10px => 1rem */
        }

        @media only screen and (max-width: 20em) {
            html {
                font-size: 50%; /* 8px */
            }
        }

        *,
        *::before,
        *::after {
            box-sizing: inherit;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: #fff;
            font-family: "Helvetica Neue", Arial, sans-serif;
            font-size: 1.6rem;
            line-height: 1.5;
            color: rgba(0, 0, 0, 0.8);
        }

        .container {
            display: flex;
            justify-content: center;
        }

        .content {
            width: 100%;
            max-width: 114rem;
            padding: 3rem;
        }

        .site-name {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            word-wrap: break-word;
            line-height: 1;
        }

        .subheader {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            flex-wrap: wrap;
            color: rgb(121, 130, 139);
        }

        .server-name {
            font-size: 2.5rem;
            font-weight: normal;
            margin-bottom: 1rem;
        }

        .date {
            margin-bottom: 1rem;
        }

        .log {
            border-top: 1px solid rgba(0, 0, 0, 0.25);
            padding-top: 1rem;
            font-family: "JetBrains Mono", monospace;
            word-break: break-word;
            white-space: pre-wrap;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="content">
        <header>
            <h1 class="site-name">{{ $deploymentLog->site_name }}</h1>
            <div class="subheader">
                <h2 class="server-name">{{ $deploymentLog->server_name }}</h2>
                <time class="date" datetime="{{ $deploymentLog->created_at->timestamp }}">
                    {{ $deploymentLog->created_at->toRfc850String() }}
                </time>
            </div>
        </header>
        <main>
            <pre class="log">{{ $deploymentLog->content }}</pre>
        </main>
    </div>
</div>

<!--
---------------------
Telegram IV template.
---------------------

~version: "2.1"

?path: /deployment-logs/.+

$header: //header
body: //div[has-class("content")]

@pre: (//pre)

title: $body//h1
subtitle: $body//h2
published_date: $header//time/@datetime

@remove: $header//time
-->
</body>
</html>
