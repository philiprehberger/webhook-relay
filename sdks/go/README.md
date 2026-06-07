# webhookrelay (Go)

Go SDK for the [Webhook Relay API](https://webhook-relay.dcsuniverse.com).
Includes a hand-tuned `VerifySignature` for receiver-side HMAC validation.

## Install

```bash
go get github.com/philiprehberger/webhook-relay/sdks/go
```

## Verify an incoming webhook (receiver side)

```go
import (
    "io"
    "net/http"

    "github.com/philiprehberger/webhook-relay/sdks/go"
)

func WebhookHandler(w http.ResponseWriter, r *http.Request) {
    body, _ := io.ReadAll(r.Body)
    // raw bytes — DO NOT json.Unmarshal + json.Marshal
    ok := webhookrelay.VerifySignature(
        os.Getenv("WEBHOOK_SECRET"),
        string(body),
        r.Header.Get("X-Webhook-Signature"),
        0, // default tolerance: 5 minutes
    )
    if !ok {
        http.Error(w, "bad signature", http.StatusBadRequest)
        return
    }
    // ... handle event
}
```

## Send an event (sender side)

The generated client lives under `./generated/`. Use it directly:

```go
import wrclient "github.com/philiprehberger/webhook-relay/sdks/go/generated"

cfg := wrclient.NewConfiguration()
cfg.Servers = wrclient.ServerConfigurations{
    {URL: "https://api.webhook-relay.dcsuniverse.com"},
}
cfg.DefaultHeader["Authorization"] = "Bearer " + os.Getenv("WEBHOOK_RELAY_KEY")
client := wrclient.NewAPIClient(cfg)

_, _, err := client.EventsAPI.CreateEvent(ctx).
    EventCreate(wrclient.EventCreate{Type: "order.created", Payload: map[string]any{"order_id": 42}}).
    IdempotencyKey("order-42-created").
    Execute()
```

## Links

- API docs: https://webhook-relay.dcsuniverse.com
- OpenAPI spec: https://webhook-relay.dcsuniverse.com/openapi.yaml
- Source: https://github.com/philiprehberger/webhook-relay/tree/main/sdks/go
