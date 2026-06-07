import { CodeBlock } from "../../../../components/CodeBlock";
import { DocsLayout } from "../../../../components/DocsLayout";

export const metadata = { title: "Receiver patterns" };

export default function ReceiversGuide() {
  return (
    <DocsLayout>
      <h1>Receiver patterns</h1>
      <p>
        A receiver is a publicly-reachable HTTPS endpoint that accepts a
        signed POST, verifies the signature, returns 2xx fast, and does any
        slow work asynchronously. The shape is identical across languages;
        the snippets below are deliberately complete — copy them.
      </p>

      <h2>Next.js (App Router)</h2>
      <CodeBlock language="typescript">
{`// app/api/webhooks/route.ts
import { verifySignature } from "@philiprehberger/webhook-relay-client";

export async function POST(request: Request) {
  const body = await request.text();
  const ok = verifySignature(
    process.env.WEBHOOK_SECRET!,
    body,
    request.headers.get("x-webhook-signature"),
  );
  if (!ok) return new Response("Bad signature", { status: 400 });

  // Acknowledge fast, do work in the background.
  enqueue(JSON.parse(body));
  return new Response("ok", { status: 200 });
}`}
      </CodeBlock>

      <h2>Laravel</h2>
      <CodeBlock language="php">
{`// routes/api.php
use WebhookRelay\\Client\\WebhookSignature;

Route::post('/webhooks/relay', function (Request $request) {
    $body = $request->getContent();
    if (! WebhookSignature::verify(
        secret: env('WEBHOOK_SECRET'),
        body: $body,
        header: $request->header('X-Webhook-Signature', ''),
    )) {
        return response()->json(['error' => 'bad_signature'], 400);
    }
    HandleEventJob::dispatch(json_decode($body, true));
    return response()->noContent();
});`}
      </CodeBlock>

      <h2>FastAPI</h2>
      <CodeBlock language="python">
{`from fastapi import FastAPI, Request, Response
from webhook_relay import verify_signature

app = FastAPI()

@app.post("/webhooks/relay")
async def webhook(request: Request):
    body = await request.body()                            # raw bytes
    if not verify_signature(
        secret=os.environ["WEBHOOK_SECRET"],
        body=body,
        header=request.headers.get("x-webhook-signature"),
    ):
        return Response("Bad signature", status_code=400)
    queue.enqueue(json.loads(body))
    return Response(status_code=204)`}
      </CodeBlock>

      <h2>Go (net/http)</h2>
      <CodeBlock language="go">
{`func WebhookHandler(w http.ResponseWriter, r *http.Request) {
    body, _ := io.ReadAll(r.Body)
    if !webhookrelay.VerifySignature(
        os.Getenv("WEBHOOK_SECRET"),
        string(body),
        r.Header.Get("X-Webhook-Signature"),
        0,
    ) {
        http.Error(w, "bad signature", http.StatusBadRequest)
        return
    }
    queue.Enqueue(body)
    w.WriteHeader(http.StatusNoContent)
}`}
      </CodeBlock>

      <h2>Five rules</h2>
      <ol>
        <li>
          <strong>Verify against the raw body.</strong> Never JSON-parse and
          re-stringify before verification — the bytes change.
        </li>
        <li>
          <strong>Return fast.</strong> Acknowledge with 2xx the moment the
          signature checks out. Push slow work to a queue. The relay
          retries on timeouts.
        </li>
        <li>
          <strong>Be idempotent.</strong> Use the event ID as your dedup
          key downstream. Retries happen; design for them.
        </li>
        <li>
          <strong>Don&apos;t throw on 4xx-shaped input.</strong> Return 400
          and let the relay dead-letter it. Throwing turns into a 500 →
          retry storm.
        </li>
        <li>
          <strong>Log the signature header.</strong> When a receiver
          rejects a webhook, the signature header in your logs is what
          lets the operator reproduce the verification locally.
        </li>
      </ol>
    </DocsLayout>
  );
}
