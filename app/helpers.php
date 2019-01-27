<?php

if (!function_exists('preg_match_first')) {
    /**
     * Regexp finder.
     *
     * @param string $pattern
     * @param string $subject
     *
     * @return string|null
     */
    function preg_match_first(string $pattern, string $subject): ?string
    {
        $matches = [];
        $result = preg_match($pattern, $subject, $matches);

        if ($result === 1) {
            return array_get($matches, 1);
        } else {
            return null;
        }
    }
}

if (!function_exists('logger')) {
    /**
     * Log a debug message to the logs.
     *
     * @param string $message
     * @param array $context
     *
     * @return \Illuminate\Log\LogManager|null
     */
    function logger($message = null, array $context = [])
    {
        if (is_null($message)) {
            return app('log');
        }

        return app('log')->debug($message, $context);
    }
}

if (!function_exists('request')) {
    /**
     * Get an instance of the current request or an input item from the request.
     *
     * @param array|string $key
     * @param mixed $default
     *
     * @return \Illuminate\Http\Request|string|array
     */
    function request($key = null, $default = null)
    {
        if (is_null($key)) {
            return app('request');
        }

        if (is_array($key)) {
            return app('request')->only($key);
        }

        $value = app('request')->__get($key);

        return is_null($value) ? value($default) : $value;
    }
}

if (!function_exists('db')) {
    /**
     * Get a new database query builder instance.
     *
     * @param string $table Table name.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    function db(string $table)
    {
        return app('db')->table($table);
    }
}

if (!function_exists('now')) {
    /**
     * Create a new Carbon instance for the current time.
     *
     * @param \DateTimeZone|string|null $tz
     *
     * @return \Illuminate\Support\Carbon
     */
    function now($tz = null)
    {
        return \Illuminate\Support\Carbon::now($tz);
    }
}
