# Webhook Relay — Technical Audit

*Independent read-only audit, 2026-08-07. No repository file was modified; this
document is the only file created and it is uncommitted.*

**Engagement brief**

| | |
|---|---|
| **Target** | `~/projects/webhook-relay` @ `f34d12e` (main, clean) |
| **What it is** | Webhook delivery API sold as a complete API product. Laravel 13.14 + PHP 8.3 + MySQL 8 + Redis/Horizon + Filament 4 at `api.webhook-relay.dcsuniverse.com`; Next.js 16 docs at `webhook-relay.dcsuniverse.com`; four generated SDKs. |
| **Stage** | Production bar, plus a reputational lens — public source, used as sales proof. |
| **Constraints honoured** | No new vendors. Every fix uses Laravel/Guzzle/Apache facilities already in the stack. |
| **Out of scope** | `vendor/`, `node_modules/`, `.next/`, generated SDK clients (the hand-written verifier layers were reviewed). |
| **Measured** | 4,269 LOC app · 1,861 tests · 92 PHPUnit / 457 assertions passing · 20 SDK signature tests · 20 operations across 16 spec paths (21 registered `/v1/` routes). |

---

## 1. Executive summary

The tenancy model here is the strongest of the fleet: there is no global scope to
fail silently, every controller filters on `workspace_id` explicitly, and I found no
IDOR. It also correctly declines to configure trusted proxies, which — as established
below — is the right call on this host and the opposite of what three sibling services
do. Those are real engineering wins and they are why this audit has no data-exposure
finding.

The problem is egress and capacity. **An entirely anonymous user can obtain a sandbox
key from a public endpoint, use it to make the server fetch an internal URL, and read
the response body** — because the SSRF guard validates only the URL it is handed while
the HTTP client follows redirects, and `httpbin.org` (a redirector) is on the sandbox
allowlist. Separately, the SSE echo endpoint pins a php-fpm worker for 60 seconds, and
that worker pool is capped at 20 and **shared with every other PHP application on the
box** — so roughly twenty requests can take down the whole portfolio estate, not just
this service. **I would not ship this** until the redirect and the stream budget are
addressed; both are small changes.

**Critical: 2 · High: 1 · Medium: 4 · Low/Note: 2**

*(Post-audit: both Criticals, the High and two Mediums are fixed in the commit that
follows this document. One Medium was withdrawn as incorrect — see the correction
note under Standardization.)*

## 2. Scorecard

| Domain | Rating | One-line justification |
|---|---|---|
| Security | **Critical** | Anonymous SSRF with response read; otherwise the strongest access-control story in the fleet. |
| Data & state integrity | **Strong** | Idempotency via a dedicated table with TTL and sweeper, attempt history preserved, replay bumps a sequence rather than mutating in place. |
| Architecture & structure | **Strong** | Delivery, signing, guard and allowlist are separate, injected, individually testable services. |
| Code quality | **Adequate** | Clear and consistent; the defects are design-level, not sloppiness. |
| Testing | **Adequate** | 92 tests / 457 assertions with real negative cases, plus an identical 5-test signature contract across four languages. Nothing covers redirect-based SSRF. |
| Standardization | **Adequate** | CI runs (API CI + API smoke); no static analysis; the spec omitted a shipped route until this pass. |
| Design & UI | **Adequate** | 21 static routes, Lighthouse 100/95/100 on the landing. Not deeply audited — see §6. |
| Operations & DX | **Weak** | Shared 20-child FPM pool with a 60-second endpoint on it, and no per-app resource isolation. |

---

## 3. Findings

### SECURITY

---

### [CRITICAL] [CONFIRMED] Unauthenticated SSRF with response read, via the sandbox key and an allowlisted redirector

**Location:** `app/Http/Controllers/Api/SandboxKeysController.php:24-50` ·
`app/Http/Controllers/Api/WebhookTestController.php:29-46, 67-85` ·
`app/Services/SandboxAllowlist.php:18-30` · `app/Services/SsrfGuard.php`

**What:** Four correct-looking controls compose into a bypass.

1. `POST /v1/sandbox/keys` is **unauthenticated** (`routes/api.php:16`) and mints a
   working API key. Rate limit is 5 per IP per hour; the key lives 24 hours.
2. `POST /v1/webhooks/test` validates `url` as `starts_with:https://`.
3. For a sandbox key it requires the host to be on the allowlist —
   `webhook.site`, `requestbin.com`, `httpbin.org` (`SandboxAllowlist:12-30`).
4. `SsrfGuard::check()` resolves that host and rejects private/loopback/link-local.

Then `WebhookTestController:77` calls `->post($validated['url'])` with **no
`allow_redirects` override anywhere in the codebase** (confirmed by grep across
`app/`), so Guzzle's default applies and follows up to five redirects. The guard is
never re-run on the redirect target.

`httpbin.org` is an allowlisted host whose entire purpose includes
`/redirect-to?url=…`. So a URL that satisfies every check above can still land on an
internal address. On a 302 Guzzle also converts POST to GET, which is exactly the verb
wanted for reading internal HTTP endpoints.

The response is not blind: `:83` returns `response_body_snippet` to the caller.

**I traced this through the code but did not execute it against production** —
running an SSRF against live infrastructure is not an appropriate audit action. The
code path is unambiguous, hence CONFIRMED.

**Why it matters:** The attacker needs no account, no invitation and no prior
relationship — the credential is self-service. The host is shared: Apache
`server-status` is an enabled vhost (`000-server-status.conf`), the Distill FastAPI
worker listens on `127.0.0.1:8002`, and a dozen Next.js applications occupy ports
3000–3022. Any of those become readable through this endpoint.

Genuinely mitigating, and worth stating: **IMDSv2 is enforced** — I verified that
`http://169.254.169.254/latest/meta-data/` returns `401` without a token, so this does
not escalate to AWS instance credentials. That is the difference between this being a
serious bug and being an incident.

Note the same redirect gap exists for **live and test keys** on this endpoint, which
skip the allowlist entirely (`:38` only applies it when `$apiKey->is_sandbox`) and are
constrained only by the guard. The sandbox path is worse solely because it is anonymous.

**Fix:** One line closes the whole chain:
`->withOptions(['allow_redirects' => false])` on the call at `:67-77`. A webhook
receiver has no legitimate reason to redirect a probe, and a 3xx should be reported to
the caller as a failed probe. Apply the same option to the real delivery path (see the
Medium below) so the two cannot drift.

If redirects must ever be supported, re-run `SsrfGuard::check()` on every hop via a
Guzzle `on_redirect` callback rather than trusting the first URL.

**Effort:** S
**Blast radius:** Probes against receivers that answer 3xx (URL shorteners, `http`→
`https` upgrades) begin failing with a clear reason. That is the correct behaviour for
a delivery-semantics probe.

---

### [CRITICAL] [CONFIRMED] A 60-second SSE endpoint on a 20-child php-fpm pool shared by every application on the host

**Location:** `app/Http/Controllers/Api/EchoStreamController.php:14-15, 19, 62-72` ·
host: `/etc/php/8.3/fpm/pool.d/www.conf` (`pm.max_children = 20`)

**What:** `GET /v1/echo/stream` holds an open `StreamedResponse` for up to
`MAX_DURATION_SECONDS = 60`, polling the database once a second. Each open stream
occupies one php-fpm child for that whole minute.

Measured on the host:

- `pm = dynamic`, **`pm.max_children = 20`**, `pm.max_requests = 500`, 2 CPU cores.
- PHP is served through a single global handler —
  `SetHandler "proxy:unix:/run/php/php8.3-fpm.sock|fcgi://localhost"` in
  `conf-enabled/php8.3-fpm.conf` — so **every** PHP vhost on the box draws from the
  same `[www]` pool. Only `[www]` and `[staging]` pools exist.

The docblock at `:14-15` reasons "Short stream cap (60s) keeps the connection cheap
under mod_php" — but this host runs php-fpm with a hard child ceiling, and under that
model a long-lived request is the expensive kind, not the cheap kind.

**Why it matters:** ~20 concurrent requests to a single public endpoint exhaust the
pool for **the entire estate** — Inkwell, Distill, Switchyard, Mainline, Codex,
Almanac, Docgen, Pennant and the rest all stop serving PHP until the streams expire.
The keys needed are self-service (see the Critical above), so the barrier is
effectively zero, and the blast radius extends far beyond the service that owns the
bug. This is also the failure mode most likely to be discovered accidentally: a docs
page that auto-connects on load plus a modest traffic spike reproduces it without
malice.

**Fix:** Two changes, both cheap, and worth doing together:

1. **Bound the concurrency, not just the duration.** Track open streams in Redis
   (already a dependency) and refuse beyond a small cap — e.g. 3 concurrent per
   workspace and ~8 globally — returning 503 with `Retry-After`. The frontend already
   reconnects on stream end (`:15`), so a refusal degrades gracefully.
2. **Stop it competing with request traffic.** Give the SSE route its own php-fpm pool
   with its own child budget, so exhausting it cannot starve ordinary requests. A
   second pool already exists (`[staging]`), so the pattern is established on this host.

Shortening the cap to 20–30s reduces exposure but does not fix it; the ratio of
stream-seconds to workers is what matters.

**Effort:** M
**Blast radius:** Docs-page visitors may occasionally get a 503 and reconnect. Adding
a pool is a host-level change requiring an Apache/FPM reload.

---

### [HIGH] [CONFIRMED] Live API keys travel in the query string and are written to access logs

**Location:** `app/Http/Controllers/Api/EchoStreamController.php:13-14, 24, 49`

**What:** The SSE endpoint reads its credential from `?key=` because `EventSource`
cannot set headers — the docblock acknowledges this at `:13-14`. It accepts any valid
key, not only sandbox ones: the error text at `:49` reads "Provide `?key=` with a valid
sandbox / **live** / test key."

Apache's default `combined` log format records the full request line including the
query string, so every connection writes a usable production credential into
`/var/log/apache2/*access.log`. *(No sample was present in the current logs — the
endpoint has no recorded traffic yet — so this is confirmed from the log format and
the code, not from an observed leak.)*

**Why it matters:** Credentials in logs outlive their usefulness and travel: log
shipping, backups, anyone with read access to the box, and log-rotation archives. The
same URL lands in browser history and, if the docs page ever links outward, in
`Referer` headers. A key valid for live traffic should never be placed somewhere
designed to be recorded.

**Fix:** Restrict this endpoint to sandbox keys — reject anything where
`is_sandbox` is false, which matches the feature's actual purpose (a docs-page demo)
and reduces the leak to a 24-hour self-service token. Better still, mint a
short-lived single-use stream ticket from an authenticated request and accept only
that on the query string. If live keys must keep working, add a per-vhost Apache
`LogFormat` that strips the query string for this path.

**Effort:** S (sandbox-only) / M (ticket)
**Blast radius:** Any integrator using a live key against the echo stream must switch.
Given the endpoint's stated purpose, that is likely nobody.

---

### [MEDIUM] [CONFIRMED] The real delivery path has the same unbounded-redirect gap

**Location:** `app/Jobs/DeliverEventToSubscription.php:137` (delivery),
`:315` (`response_body_snippet` persisted)

**What:** Deliveries call `Http::withHeaders(...)` with no redirect option, same as
the probe. A subscriber URL that passes `SsrfGuard` at creation can redirect each
delivery to an internal address, and `:315` stores a body snippet of whatever answered.

**Why it matters:** Lower severity than the Critical because it needs an authenticated
tenant with a subscription, and delivery is asynchronous. But it is the same root cause
on the higher-volume path, and the stored snippet is retrievable by that tenant.

**Fix:** Same one-line option. Fix both call sites in the same change so they cannot
diverge — the divergence between the two health-check URLs elsewhere in this estate is
the cautionary example.

**Effort:** S · **Blast radius:** Subscriptions relying on a redirect start failing
and surface in the attempt timeline.

---

### [MEDIUM] [CONFIRMED] `trace_class` defaults to `'production'` inline rather than through the contract

**Location:** `app/Http/Controllers/Api/EventsController.php:99`

**What:** `$traceContext?->state?->get('mnl_class') ?? 'production'` hard-codes the
fallback at the call site instead of resolving through the Interchange package's
`TraceClass`.

**Why it matters:** Any caller omitting or malforming `tracestate` has its traffic
recorded as production, which is precisely what trace classification exists to prevent.
This is already tracked as open in the Mainline program plan; the same inline default
also exists in Inkwell (`IngestController:131`), making it a fleet pattern. Note the
plan's own observation that `trace.http.echo` cannot detect this class of gap, because
echoing a header is not the same as persisting the right value.

**Fix:** Resolve via the package's `TraceClass` so the default lives in one place.
**Effort:** S · **Blast radius:** Rows written after the change may classify differently.

---

### [MEDIUM] [CONFIRMED] The spec omits a shipped route, and the drift check cannot see it

> **Correction (2026-08-07).** An earlier revision of this finding claimed three of
> four CI workflows were disabled here. **That was wrong for this repository.** The
> `.disabled` suffixes existed only in my working tree as untracked renames; `git
> ls-files` shows all four tracked as `.yml`, the working tree matched HEAD on the
> remote, and GitHub confirms `API CI` and `API smoke` both run (10 recorded runs of
> the latter — 8 success, 2 failure). The claim held for Inkwell, Switchyard, Distill,
> Splitstream and Mainline, where `.disabled` is committed. It did not hold here, and
> the surviving finding is narrower.

**Location:** `routes/api.php:16` vs `openapi/spec.yaml` · `.github/workflows/sdk-ci.yml`

**What:** `POST /v1/sandbox/keys` — the unauthenticated key-minting endpoint, and the
most security-relevant route in the service — was registered but absent from the
published contract.

**Why it matters:** `sdk-ci.yml` carries the `spec-drift` job that regenerates all four
SDKs and fails on divergence, which is the guarantee the portfolio copy advertises. It
compares the spec against the SDKs, so a route that never entered the spec is
structurally invisible to it: there is nothing to diverge from. The check was running
and could not have caught this.

There is also no PHPStan/Larastan and no Pint config; static analysis remains the tool
class that catches defects no test exercises.

**Fix:** Documented the endpoint in the spec, including the rate limit, the 24-hour
expiry and the delivery-time allowlist, so its constraints are contractual rather than
incidental. Added `tests/Feature/SpecCoversEveryRouteTest.php`, which walks
`Route::getRoutes()` and asserts every registered `/v1/` route appears in the spec —
the check that catches this class, and the one the drift job cannot be. It failed on
`POST /v1/sandbox/keys` before the spec was updated.

**Effort:** S (done) · **Blast radius:** Regenerating SDKs adds a method.

---

### [MEDIUM] [CONFIRMED] `POST /v1/sandbox/keys` is absent from the OpenAPI spec

**Location:** `routes/api.php:16` vs `openapi/spec.yaml` (16 paths, none `sandbox`)

**What:** The anonymous key-minting endpoint — the most security-relevant route in the
service — is not in the published contract.

**Why it matters:** The spec is presented as the source of truth with CI enforcing
drift. An unauthenticated endpoint that is invisible to the spec is invisible to every
consumer of it: SDK generation, the Scalar reference, and any reviewer who reads the
contract instead of the routes file. It is also how the Critical above stays
inconspicuous.

**Fix:** Document it, including the rate limit and the allowlist restriction, so its
constraints are contractual rather than incidental.
**Effort:** S · **Blast radius:** Regenerating SDKs adds a method.

---

### [LOW] `pm.max_requests = 500` with no per-app isolation

The shared pool recycles children every 500 requests, which is sensible, but there is
no `request_terminate_timeout` visible to bound a stuck request. Combined with the SSE
finding, a per-app pool is the structural answer. **Effort:** M

### [NOTE] What is genuinely good here

- **Explicit tenancy beats implicit.** Every controller filters `workspace_id` at the
  query (`EchoStreamController:68`, `TracesController`, `DeliveriesController`,
  `EventsController`). With no global scope there is nothing to silently stop working —
  a design choice that has aged better than the alternative used elsewhere in this estate.
- **No `trustProxies` call.** Correct for this host: PHP is served via `mod_proxy_fcgi`
  and `mod_remoteip` is not enabled, so `REMOTE_ADDR` is already the true client
  address. Three sibling services set `trustProxies(at: '*')` here and thereby made
  their own rate limits spoofable; this one did not.
- **`WebhookTestController` layers its defences** — scheme validation, then allowlist,
  then guard, each returning a distinct problem response. The composition is the bug;
  the individual controls are well built.
- **The 5-test signature contract replicated identically across four languages** is a
  genuinely strong artifact, and the reason to keep the SDK CI running.

---

## 4. Root causes

**1. Guards validate a value, not the operation that value initiates.** `SsrfGuard`
answers "is this URL safe to fetch?" correctly, then the fetch is allowed to become a
different fetch. Both Criticals in the fleet's egress story reduce to this. A guard and
the client it protects must share one configuration.

**2. Capacity is treated as unbounded.** Nothing in the application reasons about the
20-child pool it shares with a dozen neighbours. The SSE feature is the sharpest case,
but the same blind spot explains why no endpoint has a concurrency budget.

**3. Controls exist per feature rather than per boundary.** The allowlist applies only
to sandbox keys; the redirect gap applies to everyone; the spec covers some routes.
Each decision is locally reasonable and the set has holes.

**4. The contract is authoritative by declaration, not by mechanism.** The drift job
runs, and still could not catch the missing spec path, because it compares the spec to
the SDKs rather than to the routes. A check that cannot fail for the thing you care
about is not a guardrail for it.

### What breaks first at 10x

The php-fpm pool, and not because of this service's own traffic — because it is shared.
The second thing is the 1-second polling loop in the SSE handler, which is a query per
connection per second against `deliveries`.

### Riskiest day-one change

Adding an outbound HTTP call. Every existing example omits redirect handling, so the
idiomatic-looking copy of nearby code reproduces the Critical.

---

## 5. Remediation plan

**Immediate — before the next deploy**

1. `allow_redirects => false` on `WebhookTestController:77` **and**
   `DeliverEventToSubscription:137`. One change, both call sites — this closes the
   Critical and the related Medium.
2. Restrict the SSE endpoint to sandbox keys.
3. Add a concurrency cap to the SSE endpoint (Redis counter, 503 beyond it). This is
   the interim control while 6 is scheduled.

**Near-term — this month**

4. Add a test asserting every registered `/v1/` route exists in `openapi/spec.yaml`; document `POST /v1/sandbox/keys`.
5. Re-enable `sdk-ci.yml` and `api-smoke.yml`; fix whatever the drift job surfaces.
6. Give the streaming route its own php-fpm pool.
7. Route `trace_class` through the package's `TraceClass`.
8. Add a regression test that a probe against a redirecting allowlisted host is refused — the specific bug, named.

**Structural — this quarter**

9. Adopt a single shared outbound-HTTP factory that applies the guard, the redirect policy and the timeout together, so a new call site cannot get one without the others.
10. Per-application FPM pools across the estate, so no single service can exhaust its neighbours.

Item 1 makes 8 verifiable. Item 6 supersedes the interim cap in 3.

---

## 6. Open questions and assumptions

- **Is `POST /v1/sandbox/keys` intended to stay anonymous?** Everything about the
  Critical follows from that choice. It is defensible for a docs try-it console, but it
  makes every downstream control the only control.
- **Was the SSE endpoint sized against `pm.max_children`?** The docblock reasons about
  mod_php, which is not what this host runs; I assume the constraint was simply not
  revisited after the FPM migration noted in the deploy script comments.
- **Assumption:** Guzzle's default `allow_redirects` is in effect. Verified negatively —
  no `withOptions`, `allow_redirects` or `withoutRedirecting` appears anywhere in `app/`.

**Deliberately not inspected:** `vendor/`, `node_modules/`, `.next/`, generated SDK
clients (per scope). Also **not deeply reviewed**: the Filament dashboard
(`app/Filament/`), the 58,887 LOC docs site beyond routing and the Lighthouse figures
already published, `scripts/` deploy tooling, and the migration set. The four
hand-written `verifySignature` helpers were read only far enough to confirm the 5-test
contract is genuinely identical across languages.
