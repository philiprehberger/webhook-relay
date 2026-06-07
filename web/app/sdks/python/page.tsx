import { SdkPage } from "../../../components/SdkPage";

export const metadata = { title: "Python SDK" };

export default function PythonSdk() {
  return (
    <SdkPage
      snippets={{
        lang: "Python",
        pkg: "webhook-relay-client",
        install: "pip install 'webhook-relay-client[generated]'",
        sourceUrl:
          "https://github.com/philiprehberger/webhook-relay/tree/main/sdks/python",
        sendLang: "python",
        send: `from webhook_relay_client import Configuration, ApiClient
from webhook_relay_client.api import EventsApi
from webhook_relay_client.models import EventCreate

config = Configuration(
    host="https://api.webhook-relay.dcsuniverse.com",
    access_token=os.environ["WEBHOOK_RELAY_KEY"],
)
events = EventsApi(ApiClient(config))

events.create_event(
    event_create=EventCreate(type="order.created", payload={"order_id": 42}),
    idempotency_key="order-42-created",
)`,
        verifyLang: "python",
        verify: `from webhook_relay import verify_signature

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
