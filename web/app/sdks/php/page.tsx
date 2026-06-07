import { SdkPage } from "../../../components/SdkPage";

export const metadata = { title: "PHP SDK" };

export default function PhpSdk() {
  return (
    <SdkPage
      snippets={{
        lang: "PHP",
        pkg: "philiprehberger/php-webhook-relay-client",
        install: "composer require philiprehberger/php-webhook-relay-client",
        sourceUrl: "https://github.com/philiprehberger/php-webhook-relay-client",
        sendLang: "php",
        send: `use PhilipRehberger\\WebhookRelayClient\\WebhookRelayClient;

$relay = new WebhookRelayClient(getenv('WEBHOOK_RELAY_KEY'));

$event = $relay->ingest(
    type: 'order.created',
    payload: ['order_id' => 42],
    idempotencyKey: 'order-42-created',
);

print $event['id'].PHP_EOL;`,
        verifyLang: "php",
        verify: `use PhilipRehberger\\WebhookRelayClient\\Signer;

$body = file_get_contents('php://input');     // raw bytes — DO NOT json_decode + re-encode
$header = $_SERVER['HTTP_X_WEBHOOK_SIGNATURE'] ?? '';

if (! Signer::verify(getenv('WEBHOOK_SECRET'), $body, $header)) {
    http_response_code(400);
    exit('Bad signature');
}`,
      }}
    />
  );
}
