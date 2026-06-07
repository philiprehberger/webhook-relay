import { SdkPage } from "../../../components/SdkPage";

export const metadata = { title: "TypeScript SDK" };

export default function TypeScriptSdk() {
  return (
    <SdkPage
      snippets={{
        lang: "TypeScript",
        pkg: "@philiprehberger/webhook-relay-client",
        install: "npm i @philiprehberger/webhook-relay-client",
        sourceUrl:
          "https://github.com/philiprehberger/webhook-relay/tree/main/sdks/typescript",
        sendLang: "typescript",
        send: `import { EventsApi, Configuration } from "@philiprehberger/webhook-relay-client/generated";

const events = new EventsApi(new Configuration({
  basePath: "https://api.webhook-relay.dcsuniverse.com",
  accessToken: process.env.WEBHOOK_RELAY_KEY,    // whk_live_...
}));

await events.createEvent({
  eventCreate: { type: "order.created", payload: { orderId: 42 } },
  idempotencyKey: "order-42-created",
});`,
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
