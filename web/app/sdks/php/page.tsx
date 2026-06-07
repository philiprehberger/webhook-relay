import { SdkPage } from "../../../components/SdkPage";

export const metadata = { title: "PHP SDK" };

export default function PhpSdk() {
  return (
    <SdkPage
      snippets={{
        lang: "PHP",
        pkg: "philiprehberger/webhook-relay-client",
        install: "composer require philiprehberger/webhook-relay-client",
        sourceUrl:
          "https://github.com/philiprehberger/webhook-relay/tree/main/sdks/php",
        sendLang: "php",
        send: `use WebhookRelay\\Client\\Api\\EventsApi;
use WebhookRelay\\Client\\Configuration;
use WebhookRelay\\Client\\Model\\EventCreate;
use GuzzleHttp\\Client;

$config = Configuration::getDefaultConfiguration()
    ->setHost('https://api.webhook-relay.dcsuniverse.com')
    ->setAccessToken(getenv('WEBHOOK_RELAY_KEY'));

$events = new EventsApi(new Client(), $config);

$events->createEvent(
    eventCreate: new EventCreate(['type' => 'order.created', 'payload' => ['order_id' => 42]]),
    idempotencyKey: 'order-42-created',
);`,
        verifyLang: "php",
        verify: `use WebhookRelay\\Client\\WebhookSignature;

$body = file_get_contents('php://input');     // raw bytes — DO NOT json_decode + re-encode
$header = $_SERVER['HTTP_X_WEBHOOK_SIGNATURE'] ?? '';

if (! WebhookSignature::verify(getenv('WEBHOOK_SECRET'), $body, $header)) {
    http_response_code(400);
    exit('Bad signature');
}`,
      }}
    />
  );
}
