# @philiprehberger/webhook-relay-client

TypeScript SDK for the [Webhook Relay API](https://webhook-relay.dcsuniverse.com).
Includes a hand-tuned `verifySignature` helper for receiver-side HMAC
validation — the only thing most webhook integrators actually need.

## Install

```bash
npm install @philiprehberger/webhook-relay-client
```

## Verify an incoming webhook (receiver side)

```typescript
import { verifySignature } from "@philiprehberger/webhook-relay-client";

const ok = verifySignature(
  process.env.WEBHOOK_SECRET!,                  // whsec_...
  rawRequestBody,                               // raw bytes — NOT JSON.parse'd
  request.headers["x-webhook-signature"],       // t=...,v1=...
);

if (!ok) {
  return new Response("Bad signature", { status: 400 });
}
```

The raw request body must be passed exactly as received. JSON-parsing and
re-stringifying it will reorder keys or change whitespace and break the
signature.

## Send an event (sender side)

The generated client is in `./generated/`. Use it directly:

```typescript
import { EventsApi, Configuration } from "@philiprehberger/webhook-relay-client/generated";

const events = new EventsApi(new Configuration({
  basePath: "https://api.webhook-relay.dcsuniverse.com",
  accessToken: process.env.WEBHOOK_RELAY_KEY,   // whk_live_... or whk_test_...
}));

await events.createEvent({
  eventCreate: {
    type: "order.created",
    payload: { orderId: 42 },
  },
  idempotencyKey: "order-42-created",            // optional, 24h dedup
});
```

## Links

- API docs: https://webhook-relay.dcsuniverse.com
- OpenAPI spec: https://webhook-relay.dcsuniverse.com/openapi.yaml
- Source: https://github.com/philiprehberger/webhook-relay/tree/main/sdks/typescript
