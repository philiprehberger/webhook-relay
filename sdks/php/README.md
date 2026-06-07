# philiprehberger/webhook-relay-client

PHP SDK for the [Webhook Relay API](https://webhook-relay.dcsuniverse.com).
Includes a hand-tuned `WebhookSignature::verify()` helper for receiver-side
HMAC validation.

## Install

```bash
composer require philiprehberger/webhook-relay-client
```

## Verify an incoming webhook (receiver side)

```php
use WebhookRelay\Client\WebhookSignature;

$body = file_get_contents('php://input');           // raw bytes — DO NOT json_decode + re-encode
$header = $_SERVER['HTTP_X_WEBHOOK_SIGNATURE'] ?? '';

if (! WebhookSignature::verify($_ENV['WEBHOOK_SECRET'], $body, $header)) {
    http_response_code(400);
    exit('Bad signature');
}

$event = json_decode($body, true);
// ... handle $event
```

## Send an event (sender side)

```php
use WebhookRelay\Client\Configuration;
use WebhookRelay\Client\Api\EventsApi;
use WebhookRelay\Client\Model\EventCreate;
use GuzzleHttp\Client;

$config = Configuration::getDefaultConfiguration()
    ->setHost('https://api.webhook-relay.dcsuniverse.com')
    ->setAccessToken(getenv('WEBHOOK_RELAY_KEY'));   // whk_live_...

$events = new EventsApi(new Client(), $config);

$events->createEvent(
    eventCreate: new EventCreate(['type' => 'order.created', 'payload' => ['order_id' => 42]]),
    idempotencyKey: 'order-42-created',
);
```

## Links

- API docs: https://webhook-relay.dcsuniverse.com
- OpenAPI spec: https://webhook-relay.dcsuniverse.com/openapi.yaml
- Source: https://github.com/philiprehberger/webhook-relay/tree/main/sdks/php
