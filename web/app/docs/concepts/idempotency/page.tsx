import { CodeBlock } from "../../../../components/CodeBlock";
import { DocsLayout } from "../../../../components/DocsLayout";

export const metadata = { title: "Idempotency keys" };

export default function IdempotencyGuide() {
  return (
    <DocsLayout>
      <h1>Idempotency keys</h1>
      <p>
        Network calls retry. Without dedup, that means duplicate events for
        anyone downstream. Webhook Relay supports the standard{" "}
        <code>Idempotency-Key</code> header on event ingest: pass the same
        key twice within 24 hours and you get back the original response,
        not a new event.
      </p>

      <h2>Using it</h2>
      <CodeBlock language="curl">
{`curl -X POST https://api.webhook-relay.dcsuniverse.com/v1/events \\
  -H "Authorization: Bearer whk_live_..." \\
  -H "Idempotency-Key: order-42-created" \\
  -d '{"type":"order.created","payload":{"order_id":42}}'`}
      </CodeBlock>
      <p>
        The first call returns 202 with an event ID. The second call with
        the same key returns the same 202 with the same event ID — no new
        event is created, no subscriptions are notified again.
      </p>

      <h2>Choosing a good key</h2>
      <ul>
        <li>
          <strong>Tie it to the business event, not the request.</strong>{" "}
          Use <code>order-42-created</code>, not a random UUID per request.
          A retry from your application should produce the same key.
        </li>
        <li>
          <strong>Keep it under 255 bytes.</strong> The server truncates
          longer keys.
        </li>
        <li>
          <strong>Scope it to your workspace.</strong> Keys are unique
          per-workspace; two workspaces can use the same key without
          colliding.
        </li>
      </ul>

      <h2>Different payload, same key</h2>
      <p>
        Reusing a key with a different request body returns{" "}
        <code>409 Conflict</code>. Same key + same fingerprint is a replay;
        same key + different fingerprint is a bug worth flagging.
      </p>
      <CodeBlock language="json">
{`{
  "type": "https://webhook-relay.dcsuniverse.com/errors/conflict",
  "title": "Conflict",
  "status": 409,
  "detail": "Idempotency-Key was already used with a different payload."
}`}
      </CodeBlock>

      <h2>The window</h2>
      <p>
        Records expire 24 hours after they were written. Past the window,
        the same key + same payload produces a new event ID — you&apos;re
        starting fresh. If your retry horizon is longer than a day, store
        the resulting event ID on your side and check before re-sending.
      </p>
    </DocsLayout>
  );
}
