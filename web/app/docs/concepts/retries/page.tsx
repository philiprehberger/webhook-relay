import { CodeBlock } from "../../../../components/CodeBlock";
import { DocsLayout } from "../../../../components/DocsLayout";

export const metadata = { title: "Retries + dead-letter" };

export default function RetriesGuide() {
  return (
    <DocsLayout>
      <h1>Retries + dead-letter</h1>
      <p>
        Outbound deliveries fail. The relay decides how to react based on
        the receiver&apos;s response.
      </p>

      <h2>Decision table</h2>
      <table className="border-collapse text-sm">
        <thead>
          <tr>
            <th className="border border-zinc-800 px-3 py-2 text-left">Outcome</th>
            <th className="border border-zinc-800 px-3 py-2 text-left">Action</th>
            <th className="border border-zinc-800 px-3 py-2 text-left">Counter</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td className="border border-zinc-800 px-3 py-2">2xx</td>
            <td className="border border-zinc-800 px-3 py-2">delivery <code>success</code></td>
            <td className="border border-zinc-800 px-3 py-2">consecutive_failures resets to 0</td>
          </tr>
          <tr>
            <td className="border border-zinc-800 px-3 py-2">4xx</td>
            <td className="border border-zinc-800 px-3 py-2">delivery <code>dead</code>, no retries</td>
            <td className="border border-zinc-800 px-3 py-2">consecutive_failures += 1</td>
          </tr>
          <tr>
            <td className="border border-zinc-800 px-3 py-2">5xx / timeout / connection error</td>
            <td className="border border-zinc-800 px-3 py-2">retry with backoff; dead at attempt 6</td>
            <td className="border border-zinc-800 px-3 py-2">consecutive_failures += 1 per attempt</td>
          </tr>
        </tbody>
      </table>

      <h2>Backoff schedule</h2>
      <p>
        Five retries after the first attempt, spaced exponentially. Total
        worst-case time-to-dead is ~8 hours.
      </p>
      <CodeBlock language="text">
{`attempt 1 -> +30s    -> attempt 2
attempt 2 -> +2m     -> attempt 3
attempt 3 -> +10m    -> attempt 4
attempt 4 -> +1h     -> attempt 5
attempt 5 -> +6h     -> attempt 6
attempt 6 -> dead`}
      </CodeBlock>

      <h2>Circuit breaker</h2>
      <p>
        Eight consecutive failures on a subscription auto-pauses it. New
        events that would match a paused subscription are still ingested,
        but no delivery jobs are dispatched until you{" "}
        <code>POST /v1/subscriptions/&#123;id&#125;/resume</code>. Resuming
        resets <code>consecutive_failures</code> to 0.
      </p>
      <p>
        4xx counts toward the breaker too. A subscriber returning 400 for
        every request will eventually get itself paused; that&apos;s by
        design.
      </p>

      <h2>Dead-letter queue</h2>
      <p>
        Deliveries with <code>status=dead</code> land in the dead-letter
        view at <code>GET /v1/dead-letters</code>. Replay one with{" "}
        <code>POST /v1/dead-letters/&#123;id&#125;/replay</code> — it moves
        back to pending and a fresh attempt is queued. The original attempt
        history stays intact for audit.
      </p>
    </DocsLayout>
  );
}
