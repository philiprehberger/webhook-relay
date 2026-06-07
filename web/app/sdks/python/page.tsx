import { SdkPage } from "../../../components/SdkPage";

export const metadata = { title: "Python SDK" };

export default function PythonSdk() {
  return (
    <SdkPage
      snippets={{
        lang: "Python",
        pkg: "philiprehberger-webhook-relay-client",
        install: "pip install philiprehberger-webhook-relay-client",
        sourceUrl: "https://github.com/philiprehberger/py-webhook-relay-client",
        sendLang: "python",
        send: `from philiprehberger_webhook_relay_client import WebhookRelayClient

relay = WebhookRelayClient(api_key=os.environ["WEBHOOK_RELAY_KEY"])

event = relay.ingest(
    event_type="order.created",
    payload={"order_id": 42},
    idempotency_key="order-42-created",
)

print(event["id"], event["deliveries_summary"])`,
        verifyLang: "python",
        verify: `from philiprehberger_webhook_relay_client import verify_signature

def webhook_handler(request):
    raw = request.body                  # bytes — DO NOT json.loads + re-dumps
    if not verify_signature(
        secret=os.environ["WEBHOOK_SECRET"],
        body=raw,
        header=request.headers.get("X-Webhook-Signature"),
    ):
        return Response("Bad signature", status=400)
    event = json.loads(raw)
    # ... handle event`,
      }}
    />
  );
}
