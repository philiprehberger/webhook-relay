<?php

namespace Tests\Feature;

use App\Services\SsrfGuard;
use Tests\TestCase;

/**
 * The trusted-host allowlist is a hole punched in a security control, so it is
 * tested for what it must NOT let through as much as for what it must allow.
 *
 * Context: this host maps fleet domains to 127.0.0.1 in /etc/hosts for
 * server-side rendering, so every sibling service resolves to loopback and the
 * guard blocked Webhook Relay from delivering to Switchyard — the handoff plan
 * 11.1 is built on. The allowlist is the narrowest fix that unblocks it.
 */
class SsrfGuardTrustedHostsTest extends TestCase
{
    private SsrfGuard $guard;

    protected function setUp(): void
    {
        parent::setUp();
        $this->guard = new SsrfGuard;
    }

    public function test_an_allowlisted_host_is_permitted_even_when_it_resolves_privately(): void
    {
        // `localhost` resolves to 127.0.0.1 everywhere, which is what makes it
        // a usable stand-in for a fleet domain pinned in /etc/hosts.
        config(['webhook-relay.ssrf.trusted_hosts' => ['localhost']]);

        $this->assertNull($this->guard->check('https://localhost/webhooks/inbound/x/y'));
    }

    public function test_the_same_host_is_blocked_when_not_allowlisted(): void
    {
        config(['webhook-relay.ssrf.trusted_hosts' => []]);

        $this->assertSame('private_ip_blocked', $this->guard->check('https://localhost/x'));
    }

    public function test_the_default_configuration_allows_nothing(): void
    {
        // A deployment that sets no env var must behave exactly as before.
        $this->assertSame([], config('webhook-relay.ssrf.trusted_hosts'));
    }

    public function test_matching_is_exact_and_not_a_suffix(): void
    {
        // The attack this forbids: registering a domain that ENDS WITH a
        // trusted name and having it match.
        config(['webhook-relay.ssrf.trusted_hosts' => ['localhost']]);

        $this->assertNotNull($this->guard->check('https://localhost.attacker.test/x'));
        $this->assertNotNull($this->guard->check('https://evil-localhost/x'));
    }

    public function test_an_allowlisted_name_does_not_bypass_scheme_validation(): void
    {
        config(['webhook-relay.ssrf.trusted_hosts' => ['localhost']]);

        $this->assertSame('invalid_scheme', $this->guard->check('file://localhost/etc/passwd'));
        $this->assertSame('invalid_scheme', $this->guard->check('gopher://localhost/x'));
    }

    public function test_a_raw_private_ip_is_still_blocked_when_a_hostname_is_allowlisted(): void
    {
        // Allowlisting a NAME must not allowlist the address it resolves to:
        // an attacker who can supply a URL cannot simply write the IP.
        config(['webhook-relay.ssrf.trusted_hosts' => ['localhost']]);

        $this->assertSame('private_ip_blocked', $this->guard->check('https://127.0.0.1/x'));
        $this->assertSame('private_ip_blocked', $this->guard->check('https://169.254.169.254/latest/meta-data/'));
        $this->assertSame('private_ip_blocked', $this->guard->check('https://10.0.0.5/x'));
    }

    public function test_matching_is_case_insensitive(): void
    {
        config(['webhook-relay.ssrf.trusted_hosts' => ['LocalHost']]);

        $this->assertNull($this->guard->check('https://LOCALHOST/x'));
    }

    public function test_an_empty_entry_never_matches(): void
    {
        // A stray comma in the env var must not allowlist everything.
        config(['webhook-relay.ssrf.trusted_hosts' => ['', '  ']]);

        $this->assertSame('private_ip_blocked', $this->guard->check('https://localhost/x'));
    }
}
