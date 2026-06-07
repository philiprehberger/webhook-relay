import { CodeBlock } from "../../../components/CodeBlock";
import { DocsLayout } from "../../../components/DocsLayout";

export const metadata = { title: "Quickstart" };

export default function Quickstart() {
  return (
    <DocsLayout>
      <h1>Quickstart</h1>
      <p>
        Send your first event in 5 minutes. You&apos;ll need an API key from
        the admin panel — see{" "}
        <a href="/about">About</a> for how to get one for the demo.
      </p>

      <h2>1. Create a subscription</h2>
      <p>
        A subscription is a URL that will receive events matching a filter.
      </p>
      <CodeBlock language="curl">
{`curl -X POST https://api.webhook-relay.dcsuniverse.com/v1/subscriptions \\
  -H "Authorization: Bearer whk_live_..." \\
  -d '{
    "name": "my receiver",
    "url": "https://your-app.example.com/webhooks",
    "event_filter": "order.*"
  }'`}
      </CodeBlock>
      <p>
        The response includes a <code>signing_secret</code> — store it. The
        secret is the input to the HMAC verifier; you cannot retrieve it
        later, only rotate it.
      </p>

      <h2>2. Ingest an event</h2>
      <p>
        Anything you POST to <code>/v1/events</code> with a matching{" "}
        <code>type</code> will be fanned out to your subscription.
      </p>
      <CodeBlock language="curl">
{`curl -X POST https://api.webhook-relay.dcsuniverse.com/v1/events \\
  -H "Authorization: Bearer whk_live_..." \\
  -H "Idempotency-Key: order-42-created" \\
  -d '{
    "type": "order.created",
    "payload": {"order_id": 42, "total_cents": 9900}
  }'`}
      </CodeBlock>
      <p>The response is a 202 with the event ID. Delivery is asynchronous.</p>

      <h2>3. Verify on your receiver</h2>
      <p>
        Every outbound POST carries an <code>X-Webhook-Signature</code>{" "}
        header. Verify it with the SDK helper for your language.
      </p>
      <CodeBlock language="typescript">
{`import { verifySignature } from "@philiprehberger/webhook-relay-client";

export async function POST(request: Request) {
  const body = await request.text();   // raw bytes — DO NOT JSON.parse first
  const ok = verifySignature(
    process.env.WEBHOOK_SECRET,
    body,
    request.headers.get("x-webhook-signature"),
  );
  if (!ok) return new Response("Bad signature", { status: 400 });

  const event = JSON.parse(body);
  // ... handle event
  return new Response("ok");
}`}
      </CodeBlock>
      <p>
        That&apos;s the whole loop. From here, browse the{" "}
        <a href="/docs/concepts/signing">signing concept</a> for the wire
        format details, or hit the{" "}
        <a href="/reference">try-it console</a> to fire calls without
        leaving the docs.
      </p>
    </DocsLayout>
  );
}
