<?php

if (! function_exists('get_subdomain')) {
    function get_subdomain(): ?string
    {
        $host = request()->getHost();
        $parts = explode('.', $host);

        if (count($parts) < 3) {
            return null;
        }

        return $parts[0];
    }
}

if (! function_exists('app_host')) {
    function app_host(): string
    {
        return parse_url(config('app.url'), PHP_URL_HOST);
    }
}

if (! function_exists('unislash')) {

    /**
     * Remove all double slashes outside of protocol
     */
    function unislash(string $url): string
    {
        $regex = '/(?<!:)\/\/+/';

        return preg_replace($regex, '/', $url);
    }
}
