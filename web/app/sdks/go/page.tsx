import { SdkPage } from "../../../components/SdkPage";

export const metadata = { title: "Go SDK" };

export default function GoSdk() {
  return (
    <SdkPage
      snippets={{
        lang: "Go",
        pkg: "github.com/philiprehberger/go-webhook-relay-client",
        install: "go get github.com/philiprehberger/go-webhook-relay-client",
        sourceUrl: "https://github.com/philiprehberger/go-webhook-relay-client",
        sendLang: "go",
        send: `import webhookrelay "github.com/philiprehberger/go-webhook-relay-client"

client := webhookrelay.NewClient(os.Getenv("WEBHOOK_RELAY_KEY"))

event, err := client.Ingest(ctx, webhookrelay.EventCreateInput{
    Type:           "order.created",
    Payload:        map[string]any{"orderId": 42},
    IdempotencyKey: "order-42-created",
})
if err != nil {
    log.Fatal(err)
}
fmt.Println(event["id"])`,
        verifyLang: "go",
        verify: `import webhookrelay "github.com/philiprehberger/go-webhook-relay-client"

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
