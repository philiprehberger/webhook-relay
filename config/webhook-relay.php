<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SSRF guard
    |--------------------------------------------------------------------------
    |
    | `trusted_hosts` is an explicit allowlist of destination hostnames that
    | skip the private-IP check in App\Services\SsrfGuard.
    |
    | It exists for one reason: this host maps the fleet's own domains to
    | 127.0.0.1 in /etc/hosts so server-side rendering can reach sibling apps
    | without a public round trip. That makes every fleet service resolve to
    | loopback locally, and the guard correctly refuses loopback — which blocks
    | Webhook Relay from delivering to Switchyard, the exact handoff plan 11.1
    | is built on.
    |
    | Deliberately narrow. The threat the guard addresses is a TENANT supplying
    | a hostile subscriber URL, and a tenant cannot edit this file. Entries are
    | matched on the hostname as written, before DNS resolution, and exactly —
    | no wildcards, no suffix matching. Empty by default, so a deployment that
    | configures nothing behaves exactly as it did before this existed.
    |
    | Set as a comma-separated list:
    |   WEBHOOK_RELAY_TRUSTED_HOSTS=api.switchyard.example.com,api.other.example
    |
    */

    'ssrf' => [
        'trusted_hosts' => array_values(array_filter(array_map(
            'trim',
            explode(',', (string) env('WEBHOOK_RELAY_TRUSTED_HOSTS', '')),
        ))),
    ],

];
