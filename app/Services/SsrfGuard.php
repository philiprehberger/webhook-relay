<?php

namespace App\Services;

/**
 * Refuses outbound HTTP to private / loopback / link-local IPs so a hostile
 * subscriber URL can't be used to relay traffic to internal services.
 *
 * Resolves the URL host once via gethostbyname (good enough for a portfolio
 * demo). For production you'd want DNS rebinding protection too — pin the
 * resolved IP and use it as the connect address.
 *
 * TRUSTED INTRA-FLEET DESTINATIONS
 *
 * This host maps the fleet's own domains to 127.0.0.1 in /etc/hosts, so
 * server-side rendering can reach sibling apps without a public round trip.
 * A correct consequence is that this guard blocks deliveries between fleet
 * services: `api.switchyard.philiprehberger.com` resolves to loopback here,
 * and loopback is exactly what the guard exists to refuse.
 *
 * The allowlist is an explicit, operator-configured exception for named hosts
 * — NOT a general relaxation:
 *
 *   - matched on the HOSTNAME as written, before resolution, so a hostile URL
 *     cannot reach a private address by resolving to one;
 *   - exact match only, no wildcards or suffix matching, so `evil-
 *     api.switchyard.philiprehberger.com.attacker.test` does not match;
 *   - scheme and URL validity are still enforced;
 *   - empty by default. A deployment that configures nothing behaves exactly
 *     as before.
 *
 * It is deliberately narrow: the threat this guard addresses is a *tenant*
 * supplying a subscriber URL, and a tenant cannot add entries here.
 */
class SsrfGuard
{
    /**
     * @return string|null  Null if allowed; an error code string if blocked.
     */
    public function check(string $url): ?string
    {
        $parsed = parse_url($url);
        if ($parsed === false || empty($parsed['host'])) {
            return 'invalid_url';
        }

        $scheme = strtolower($parsed['scheme'] ?? '');
        if (! in_array($scheme, ['http', 'https'], true)) {
            return 'invalid_scheme';
        }

        $host = $parsed['host'];

        // Checked before resolution and before any IP reasoning: an operator
        // naming a host here is vouching for that name, not for whatever it
        // happens to resolve to.
        if ($this->isTrustedHost($host)) {
            return null;
        }

        // Allow plain hostnames; reject IPs in private ranges.
        $ip = filter_var($host, FILTER_VALIDATE_IP)
            ? $host
            : @gethostbyname($host);

        if ($ip === $host && ! filter_var($ip, FILTER_VALIDATE_IP)) {
            // gethostbyname returns the input on failure — DNS lookup failed.
            return 'dns_failed';
        }

        if (! $this->isPublicIp($ip)) {
            return 'private_ip_blocked';
        }

        return null;
    }

    /** Exact hostname match against the configured allowlist, case-insensitive. */
    private function isTrustedHost(string $host): bool
    {
        $trusted = config('webhook-relay.ssrf.trusted_hosts', []);

        if (! is_array($trusted) || $trusted === []) {
            return false;
        }

        $host = strtolower(trim($host));

        foreach ($trusted as $candidate) {
            if ($host !== '' && $host === strtolower(trim((string) $candidate))) {
                return true;
            }
        }

        return false;
    }

    private function isPublicIp(string $ip): bool
    {
        // FILTER_FLAG_NO_PRIV_RANGE blocks 10/8, 172.16/12, 192.168/16, fc00::/7, fd00::/8
        // FILTER_FLAG_NO_RES_RANGE blocks 127/8, 169.254/16, 224/4, 240/4, and reserved IPv6 ranges
        return (bool) filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE,
        );
    }
}
