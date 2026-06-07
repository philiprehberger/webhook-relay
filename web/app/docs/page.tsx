import Link from "next/link";
import { DocsLayout } from "../../components/DocsLayout";

export const metadata = { title: "Docs" };

export default function DocsIndex() {
  return (
    <DocsLayout>
      <h1>Documentation</h1>
      <p>
        Webhook Relay is a REST API for the parts of webhook delivery that
        feel solved until you ship. The docs are organized around the
        questions that actually come up in production:
      </p>
      <ul>
        <li>
          <Link href="/docs/quickstart">Quickstart</Link> — send your first
          event in 5 minutes.
        </li>
        <li>
          <Link href="/docs/concepts/signing">HMAC signing</Link> — how the
          <code> X-Webhook-Signature </code> header is built and verified.
        </li>
        <li>
          <Link href="/docs/concepts/idempotency">Idempotency keys</Link> —
          why the dedup window exists and how to use it.
        </li>
        <li>
          <Link href="/docs/concepts/retries">
            Retries + dead-letter
          </Link>{" "}
          — backoff schedule, circuit breaker, replay.
        </li>
        <li>
          <Link href="/docs/concepts/filtering">Event filters</Link> — the
          <code> &quot;order.*&quot; </code> glob syntax.
        </li>
        <li>
          <Link href="/docs/concepts/receivers">Receiver patterns</Link> —
          framework-specific handlers in TS, PHP, Python, Go.
        </li>
      </ul>
      <p>
        Looking for the live API surface? Head to the{" "}
        <Link href="/reference">interactive reference</Link>.
      </p>
    </DocsLayout>
  );
}
