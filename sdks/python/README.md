# webhook-relay-client

Python SDK for the [Webhook Relay API](https://webhook-relay.dcsuniverse.com).
Includes a hand-tuned `verify_signature` helper for receiver-side HMAC
validation.

## Install

```bash
pip install webhook-relay-client            # verifier only
pip install 'webhook-relay-client[generated]'   # + the generated API client
```

## Verify an incoming webhook (receiver side)

```python
from webhook_relay import verify_signature

def webhook_handler(request):
    raw_body = request.body  # bytes — DO NOT json.loads + re-dumps
    if not verify_signature(
        secret=os.environ["WEBHOOK_SECRET"],
        body=raw_body,
        header=request.headers.get("X-Webhook-Signature"),
    ):
        return Response("Bad signature", status=400)

    event = json.loads(raw_body)
    # ... handle event
```

## Send an event (sender side)

The generated client lives in `./generated/`. After installing the
`[generated]` extra:

```python
from webhook_relay_client import Configuration, ApiClient
from webhook_relay_client.api import EventsApi
from webhook_relay_client.models import EventCreate

config = Configuration(
    host="https://api.webhook-relay.dcsuniverse.com",
    access_token=os.environ["WEBHOOK_RELAY_KEY"],   # whk_live_...
)
events = EventsApi(ApiClient(config))

events.create_event(
    event_create=EventCreate(type="order.created", payload={"order_id": 42}),
    idempotency_key="order-42-created",
)
```

## Links

- API docs: https://webhook-relay.dcsuniverse.com
- OpenAPI spec: https://webhook-relay.dcsuniverse.com/openapi.yaml
- Source: https://github.com/philiprehberger/webhook-relay/tree/main/sdks/python
