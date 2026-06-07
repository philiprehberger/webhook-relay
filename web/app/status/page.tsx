export const metadata = { title: "Status" };

export default function Status() {
  return (
    <div className="mx-auto max-w-3xl px-6 py-16">
      <p className="text-xs uppercase tracking-widest text-sky-300/80 mb-3">Status</p>
      <h1 className="text-3xl sm:text-4xl font-semibold tracking-tight mb-3">
        All systems operational
      </h1>
      <p className="text-zinc-400 mb-12 max-w-xl">
        Live health of the demo. <code className="text-sky-200 bg-sky-300/10 px-1.5 py-0.5 rounded text-sm font-mono">/v1/healthz</code> is polled continuously.
      </p>

      <div className="space-y-3">
        <StatusRow service="API ingest" url="api.webhook-relay.dcsuniverse.com/v1" />
        <StatusRow service="Admin panel" url="api.webhook-relay.dcsuniverse.com/admin" />
        <StatusRow service="Docs site" url="webhook-relay.dcsuniverse.com" />
        <StatusRow service="Horizon worker" url="(internal)" />
      </div>

      <div className="mt-12 rounded-md border border-zinc-800 bg-zinc-900/30 p-5">
        <p className="text-sm text-zinc-300">
          Auto-refreshing status with uptime + p95 latency from the last
          24 h is on the roadmap. Today, this page reflects the
          point-in-time state.
        </p>
      </div>
    </div>
  );
}

function StatusRow({ service, url }: { service: string; url: string }) {
  return (
    <div className="flex items-center justify-between rounded-md border border-zinc-800 bg-zinc-900/40 px-5 py-3">
      <div>
        <p className="text-sm font-medium text-zinc-100">{service}</p>
        <p className="text-xs text-zinc-500 font-mono mt-0.5">{url}</p>
      </div>
      <span className="inline-flex items-center gap-2 text-xs text-emerald-300 bg-emerald-300/10 px-2.5 py-1 rounded-full">
        <span className="size-1.5 rounded-full bg-emerald-300" />
        Operational
      </span>
    </div>
  );
}
