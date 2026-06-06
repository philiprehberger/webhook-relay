# Webhook Relay

> A portfolio API: production-shaped webhook delivery infrastructure with
> HMAC signing, idempotency keys, exponential-backoff retries, dead-letter
> handling, and per-attempt observability. Ships with hand-authored OpenAPI,
> generated SDKs in four languages, and an interactive docs site.

- **Docs / dashboard:** [webhook-relay.dcsuniverse.com](https://webhook-relay.dcsuniverse.com)
- **API host:** [api.webhook-relay.dcsuniverse.com](https://api.webhook-relay.dcsuniverse.com)
- **Stack:** Laravel 13 + MySQL + Redis (Horizon) + Next.js 16 + Scalar
- **Plan:** see `~/projects/income-ops/.scratch/plans/webhook_relay_api_portfolio.md`

This is not a production service. It's a portfolio demonstration that the
same architect can ship the *whole* API product surface a serious client
expects — endpoints, ergonomics, reliability semantics, generated SDKs,
downloadable docs, and a try-it console — not just an API behind a
README.

## Repo layout

```
webhook-relay/
├── app/             Laravel 13 application (API + dashboard)
├── bootstrap/       Laravel
├── config/          Laravel
├── database/        Migrations, seeders, factories
├── public/          Laravel public root (DocumentRoot on the API host)
├── resources/       Blade views, Filament (later phases)
├── routes/          API + web routes
├── storage/         Logs, framework cache, app files
├── tests/           Pest + PHPUnit
│
├── openapi/
│   └── spec.yaml    OpenAPI 3.1 — source of truth, drives SDKs and tests
│
├── web/             Next.js 16 docs + marketing site (separate deploy)
│   ├── app/
│   ├── public/
│   └── deploy.config.js
│
├── sdks/            Generated SDKs (TS, PHP, Python, Go) — later phases
│
├── infra/
│   ├── apache/      Vhost + Let's Encrypt configs (canonical copies)
│   └── cron/        Scheduled jobs (retry sweeper, idempotency TTL)
│
└── scripts/
    └── deploy/      Atomic release-based deploy (release dir + symlink)
```

## Local development

### Prerequisites

- PHP 8.3+
- Composer 2.9+
- Node 22+ / npm 10+
- MySQL 8 running locally on `127.0.0.1:3306`

### Setup

```bash
# API
composer install
cp .env.example .env    # then edit DB_PASSWORD if your local MySQL needs one
php artisan key:generate

# One-time: create the local MySQL database + user (will prompt for sudo)
sudo mysql -e "CREATE DATABASE webhook_relay CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; \
               CREATE USER 'webhook_relay'@'localhost' IDENTIFIED BY ''; \
               GRANT ALL PRIVILEGES ON webhook_relay.* TO 'webhook_relay'@'localhost'; \
               FLUSH PRIVILEGES;"

php artisan migrate
php artisan serve   # http://localhost:8000

# Docs site (separate terminal)
cd web
npm install
npm run dev         # http://localhost:3000
```

The queue runs in `sync` mode locally (no Redis needed). Production uses Redis
+ Horizon — install `redis-server` locally and set `QUEUE_CONNECTION=redis` in
`.env` if you want to exercise the worker.

### Validating the OpenAPI spec

```bash
npx @stoplight/spectral-cli lint openapi/spec.yaml
```

CI runs this on every push. Controllers conform to the spec, not the other
way around.

## Deployment

Both halves build locally and rsync to the EC2 host. No CI-hosted builds.

```bash
# Copy + fill out the deployment env (gitignored)
cp .env.deployment.example .env.deployment

# Deploy the API (atomic release with shared .env + storage symlinks)
npm run deploy

# Deploy the docs site
cd web
npm run deploy

# Verify
bash scripts/deploy/health-check.sh https://api.webhook-relay.dcsuniverse.com/v1/healthz
```

The deploy script follows the same pattern as `client-portal-laravel`:
release-based atomic switches, shared `.env` and `storage/`, automatic
cleanup of old releases, optional rollback via `npm run deploy:rollback`.

Apache vhosts and Let's Encrypt SSL are pre-provisioned on the EC2 host.
The canonical vhost files are tracked in `infra/apache/` — if you change
them on the server, rsync them back into the repo.

## Roadmap

Phase 1 (scaffold) is in progress. See the linked plan for the rest:

1. ~~Skeleton + spec~~
2. Ingest + persistence (POST/GET `/v1/events`, idempotency dedup)
3. Subscriptions + fan-out (Horizon + HMAC signing)
4. Retries + circuit breaker + dead-letter
5. Dashboard UI (Filament)
6. SDK generation (TS, PHP, Python, Go)
7. Docs site (Scalar + try-it console + Live Echo)
8. Deploy + polish
9. Portfolio cross-linking
