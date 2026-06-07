<?php

namespace App\Services;

/**
 * Outbound URL allowlist for sandbox API keys. Receiver-testing services
 * that won't act on the payload and that we don't mind being a target of
 * relay traffic.
 */
class SandboxAllowlist
{
    private const ALLOWED_HOSTS = [
        'webhook.site',
        'requestbin.com',
        'httpbin.org',
    ];

    public static function isAllowed(string $url): bool
    {
        $host = parse_url($url, PHP_URL_HOST);
        if (! is_string($host)) {
            return false;
        }
        $host = strtolower($host);

        foreach (self::ALLOWED_HOSTS as $allowed) {
            if ($host === $allowed || str_ends_with($host, '.'.$allowed)) {
                return true;
            }
        }

        return false;
    }

    public static function allowed(): array
    {
        return self::ALLOWED_HOSTS;
    }
}
