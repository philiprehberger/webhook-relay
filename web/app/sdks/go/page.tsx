import { SdkPage } from "../../../components/SdkPage";

export const metadata = { title: "Go SDK" };

export default function GoSdk() {
  return (
    <SdkPage
      snippets={{
        lang: "Go",
        pkg: "github.com/philiprehberger/webhook-relay/sdks/go",
        install: "go get github.com/philiprehberger/webhook-relay/sdks/go",
        sourceUrl:
          "https://github.com/philiprehberger/webhook-relay/tree/main/sdks/go",
        sendLang: "go",
        send: `import wrclient "github.com/philiprehberger/webhook-relay/sdks/go/generated"

cfg := wrclient.NewConfiguration()
cfg.Servers = wrclient.ServerConfigurations{
    {URL: "https://api.webhook-relay.dcsuniverse.com"},
}
cfg.DefaultHeader["Authorization"] = "Bearer " + os.Getenv("WEBHOOK_RELAY_KEY")
client := wrclient.NewAPIClient(cfg)

_, _, err := client.EventsAPI.CreateEvent(ctx).
    EventCreate(wrclient.EventCreate{Type: "order.created", Payload: map[string]any{"order_id": 42}}).
    IdempotencyKey("order-42-created").
    Execute()`,
        verifyLang: "go",
        verify: `import "github.com/philiprehberger/webhook-relay/sdks/go"

func WebhookHandler(w http.ResponseWriter, r *http.Request) {
    body, _ := io.ReadAll(r.Body)
    if !webhookrelay.VerifySignature(
        os.Getenv("WEBHOOK_SECRET"),
        string(body),
        r.Header.Get("X-Webhook-Signature"),
        0,  // default tolerance: 5 minutes
    ) {
        http.Error(w, "bad signature", http.StatusBadRequest)
        return
    }
    // ... handle event
}`,
      }}
    />
  );
}
