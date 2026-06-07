import Link from "next/link";
import { CodeBlock } from "../components/CodeBlock";
import { LiveEcho } from "../components/LiveEcho";

export default function Home() {
  return (
    <div>
      <section className="mx-auto max-w-5xl px-6 pt-20 pb-24">
        <p className="text-xs uppercase tracking-widest text-sky-300/80 mb-4">
          Webhook delivery infrastructure
        </p>
        <h1 className="text-4xl sm:text-5xl md:text-6xl font-semibold tracking-tight leading-[1.05] mb-6 max-w-3xl">
          A REST API for delivering webhooks the way they should have shipped the first time.
        </h1>
        <p className="text-lg text-zinc-400 leading-relaxed max-w-2xl mb-10">
          HMAC signing, idempotency keys, exponential-backoff retries, circuit
          breakers, dead-letter handling, per-attempt observability. The thirty
          features your team would otherwise build over six months, behind one
          endpoint.
        </p>
        <div className="flex flex-wrap gap-3">
          <Link
            href="/reference"
            className="rounded-md bg-sky-400 text-sky-950 hover:bg-sky-300 transition-colors px-4 py-2 text-sm font-medium"
          >
            Try it →
          </Link>
          <Link
            href="/docs/quickstart"
            className="rounded-md border border-zinc-700 hover:border-zinc-500 transition-colors px-4 py-2 text-sm"
          >
            5-minute quickstart
          </Link>
          <Link
            href="https://github.com/philiprehberger/webhook-relay"
            className="rounded-md border border-zinc-800 hover:border-zinc-600 hover:text-zinc-200 transition-colors px-4 py-2 text-sm text-zinc-400"
          >
            View source
          </Link>
        </div>
      </section>

      <section className="mx-auto max-w-5xl px-6 pb-24">
        <div className="grid lg:grid-cols-2 gap-6">
          <div>
            <p className="text-xs uppercase tracking-widest text-zinc-500 mb-3">
              Ingest an event
            </p>
            <CodeBlock language="curl">
{`curl -X POST https://api.webhook-relay.dcsuniverse.com/v1/events \\
  -H "Authorization: Bearer whk_live_..." \\
  -H "Idempotency-Key: order-42-created" \\
  -d '{
    "type": "order.created",
    "payload": {"order_id": 42}
  }'`}
            </CodeBlock>
          </div>
          <div>
            <p className="text-xs uppercase tracking-widest text-zinc-500 mb-3">
              Verify on the receiver
            </p>
            <CodeBlock language="typescript">
{`import { verifySignature } from "@philiprehberger/webhook-relay-client";

const ok = verifySignature(
  process.env.WEBHOOK_SECRET,
  rawBody,
  request.headers["x-webhook-signature"],
);
if (!ok) return new Response("Bad signature", { status: 400 });`}
            </CodeBlock>
          </div>
        </div>
      </section>

      <section className="mx-auto max-w-5xl px-6 pb-24">
        <h2 className="text-2xl font-semibold mb-8">What you get out of the box</h2>
        <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
          {FEATURES.map((f) => (
            <div
              key={f.title}
              className="rounded-lg border border-zinc-800 bg-zinc-900/40 p-5"
            >
              <p className="text-sm font-medium text-zinc-100 mb-1.5">{f.title}</p>
              <p className="text-sm text-zinc-400 leading-relaxed">{f.body}</p>
            </div>
          ))}
        </div>
      </section>

      <section className="mx-auto max-w-5xl px-6 pb-24 border-t border-zinc-900 pt-16">
        <h2 className="text-2xl font-semibold mb-2">See it work, live.</h2>
        <p className="text-zinc-400 mb-8 max-w-2xl">
          Paste a sandbox key from the{" "}
          <Link href="/reference" className="text-sky-300 hover:underline underline-offset-4">
            reference page
          </Link>{" "}
          and watch deliveries stream in real time via Server-Sent Events.
        </p>
        <LiveEcho />
      </section>

      <section className="mx-auto max-w-5xl px-6 pb-24 border-t border-zinc-900 pt-16">
        <div className="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-6 mb-8">
          <div>
            <h2 className="text-2xl font-semibold mb-2">SDKs in four languages</h2>
            <p className="text-zinc-400">
              Generated from the OpenAPI spec, hand-tuned signature verifiers,
              one install per language.
            </p>
          </div>
          <Link
            href="/sdks"
            className="text-sm text-sky-300 hover:text-sky-200 transition-colors"
          >
            All SDKs →
          </Link>
        </div>
        <div className="grid sm:grid-cols-2 lg:grid-cols-4 gap-3">
          {SDKS.map((s) => (
            <Link
              key={s.href}
              href={s.href}
              className="rounded-lg border border-zinc-800 bg-zinc-900/40 p-4 hover:border-zinc-700 hover:bg-zinc-900/70 transition-colors"
            >
              <p className="text-sm font-medium text-zinc-100">{s.lang}</p>
              <p className="text-xs text-zinc-500 mt-1 font-mono break-all">{s.install}</p>
            </Link>
          ))}
        </div>
      </section>
    </div>
  );
}

const FEATURES = [
  { title: "HMAC signing", body: "Stripe-style t=…,v1=… header. Signature verifier ships in every SDK." },
  { title: "Idempotency keys", body: "24-hour dedup window per workspace. Safe to retry from your side." },
  { title: "Exponential backoff", body: "30s, 2m, 10m, 1h, 6h. Five retries after the first attempt, then dead-letter." },
  { title: "Circuit breaker", body: "Eight consecutive failures auto-pauses a subscription. Notifications, no surprises." },
  { title: "Dead-letter queue", body: "Replay individual failures with one POST. Operator-facing list filters by sub and since." },
  { title: "Per-attempt observability", body: "Status, latency, signature header, response snippet — every attempt, every event." },
  { title: "SSRF protections", body: "Private + loopback IPs blocked at the egress. One less production incident." },
  { title: "Workspace rate limits", body: "Per-workspace token bucket, X-RateLimit-* headers on every response." },
  { title: "Signing key rotation", body: "Rotate with a 48-hour grace window. Receivers update at their own pace." },
];

const SDKS = [
  { lang: "TypeScript", install: "npm i @philiprehberger/webhook-relay-client", href: "/sdks/typescript" },
  { lang: "PHP", install: "composer require philiprehberger/webhook-relay-client", href: "/sdks/php" },
  { lang: "Python", install: "pip install webhook-relay-client", href: "/sdks/python" },
  { lang: "Go", install: "go get github.com/philiprehberger/webhook-relay/sdks/go", href: "/sdks/go" },
];
