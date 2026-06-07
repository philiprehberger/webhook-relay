import { SdkPage } from "../../../components/SdkPage";

export const metadata = { title: "TypeScript SDK" };

export default function TypeScriptSdk() {
  return (
    <SdkPage
      snippets={{
        lang: "TypeScript",
        pkg: "@philiprehberger/webhook-relay-client",
        install: "npm i @philiprehberger/webhook-relay-client",
        sourceUrl: "https://github.com/philiprehberger/ts-webhook-relay-client",
        sendLang: "typescript",
        send: `import { WebhookRelayClient } from "@philiprehberger/webhook-relay-client";

const relay = new WebhookRelayClient({
  apiKey: process.env.WEBHOOK_RELAY_KEY!,    // whk_live_... / whk_test_... / whk_sandbox_...
});

const event = await relay.ingest({
  type: "order.created",
  payload: { orderId: 42 },
  idempotencyKey: "order-42-created",
});

console.log(event.id, event.deliveries_summary);`,
        verifyLang: "typescript",
        verify: `import { verifySignature } from "@philiprehberger/webhook-relay-client";

const body = await request.text();    // raw bytes — DO NOT JSON.parse first
const ok = verifySignature(
  process.env.WEBHOOK_SECRET!,
  body,
  request.headers.get("x-webhook-signature"),
);
if (!ok) return new Response("Bad signature", { status: 400 });`,
      }}
    />
  );
}
