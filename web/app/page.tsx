export default function Home() {
  return (
    <div className="min-h-screen bg-zinc-950 text-zinc-100 font-sans">
      <main className="mx-auto max-w-3xl px-6 py-24 sm:py-32">
        <p className="text-xs uppercase tracking-widest text-sky-300/80 mb-4">
          Webhook delivery infrastructure
        </p>
        <h1 className="text-4xl sm:text-5xl font-semibold tracking-tight leading-tight mb-6">
          Webhook Relay
        </h1>
        <p className="text-lg leading-relaxed text-zinc-300 mb-6">
          A production-shaped REST API for fanning out events to subscriber
          endpoints. HMAC signing, idempotency keys, exponential-backoff
          retries, circuit breakers, dead-letter handling, per-attempt
          observability.
        </p>
        <p className="text-lg leading-relaxed text-zinc-300 mb-12">
          The docs site, interactive try-it console, and downloadable SDKs are
          under construction.
        </p>

        <section className="grid gap-6 sm:grid-cols-2 mb-16">
          <Card label="API host">
            <code className="text-sky-300 text-sm">
              api.webhook-relay.dcsuniverse.com
            </code>
          </Card>
          <Card label="Status">
            <span className="text-amber-300">Phase 1 — scaffold</span>
          </Card>
        </section>

        <section className="mb-16">
          <h2 className="text-sm uppercase tracking-widest text-zinc-500 mb-4">
            What will ship here
          </h2>
          <ul className="space-y-3 text-zinc-300">
            <Li>Interactive OpenAPI reference with live try-it console.</Li>
            <Li>Conceptual guides — signing, idempotency, retries, filtering.</Li>
            <Li>Downloadable SDKs in TypeScript, PHP, Python, Go.</Li>
            <Li>Downloadable OpenAPI spec, Postman collection, and PDF reference.</Li>
            <Li>Live Echo widget — fire a test event, watch deliveries stream in real time.</Li>
          </ul>
        </section>

        <footer className="border-t border-zinc-800 pt-8 text-sm text-zinc-500">
          <p>
            A portfolio build by{" "}
            <a
              href="https://philiprehberger.com"
              className="text-zinc-300 hover:text-sky-300 transition-colors underline-offset-4 hover:underline"
            >
              Philip Rehberger
            </a>
            . Not a production service.
          </p>
        </footer>
      </main>
    </div>
  );
}

function Card({ label, children }: { label: string; children: React.ReactNode }) {
  return (
    <div className="rounded-lg border border-zinc-800 bg-zinc-900/40 p-5">
      <p className="text-xs uppercase tracking-widest text-zinc-500 mb-2">{label}</p>
      <div className="text-base">{children}</div>
    </div>
  );
}

function Li({ children }: { children: React.ReactNode }) {
  return (
    <li className="flex gap-3">
      <span className="text-sky-300 mt-1.5 select-none">→</span>
      <span>{children}</span>
    </li>
  );
}
