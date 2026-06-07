# WebhookRelayClient

Webhook Relay accepts events from your application and fans them out to
subscriber endpoints with HMAC signing, idempotency-key dedup,
exponential-backoff retries, dead-letter handling, and per-attempt
observability.

This spec is the source of truth for the API. Controllers conform to it;
SDKs are generated from it. Run `npx @stoplight/spectral-cli lint
openapi/spec.yaml` to validate locally.


For more information, please visit [https://webhook-relay.dcsuniverse.com](https://webhook-relay.dcsuniverse.com).

## Installation & Usage

### Requirements

PHP 8.1 and later.

### Composer

To install the bindings via [Composer](https://getcomposer.org/), add the following to `composer.json`:

```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/GIT_USER_ID/GIT_REPO_ID.git"
    }
  ],
  "require": {
    "GIT_USER_ID/GIT_REPO_ID": "*@dev"
  }
}
```

Then run `composer install`

### Manual Installation

Download the files and include `autoload.php`:

```php
<?php
require_once('/path/to/WebhookRelayClient/vendor/autoload.php');
```

## Getting Started

Please follow the [installation procedure](#installation--usage) and then run the following:

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



// Configure Bearer (API Key) authorization: ApiKeyAuth
$config = WebhookRelayClient\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new WebhookRelayClient\Api\DeadLettersApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$subscription_id = 'subscription_id_example'; // string
$since = new \DateTime('2013-10-20T19:20:30+01:00'); // \DateTime
$cursor = 'cursor_example'; // string
$limit = 25; // int

try {
    $result = $apiInstance->listDeadLetters($subscription_id, $since, $cursor, $limit);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DeadLettersApi->listDeadLetters: ', $e->getMessage(), PHP_EOL;
}

```

## API Endpoints

All URIs are relative to *https://api.webhook-relay.dcsuniverse.com*

Class | Method | HTTP request | Description
------------ | ------------- | ------------- | -------------
*DeadLettersApi* | [**listDeadLetters**](docs/Api/DeadLettersApi.md#listdeadletters) | **GET** /v1/dead-letters | List dead-lettered deliveries
*DeadLettersApi* | [**replayDeadLetter**](docs/Api/DeadLettersApi.md#replaydeadletter) | **POST** /v1/dead-letters/{id}/replay | Replay a dead-lettered delivery
*DeliveriesApi* | [**getDelivery**](docs/Api/DeliveriesApi.md#getdelivery) | **GET** /v1/deliveries/{id} | Retrieve a delivery
*DeliveriesApi* | [**listDeliveries**](docs/Api/DeliveriesApi.md#listdeliveries) | **GET** /v1/deliveries | List deliveries
*DeliveriesApi* | [**retryDelivery**](docs/Api/DeliveriesApi.md#retrydelivery) | **POST** /v1/deliveries/{id}/retry | Manually retry a delivery
*EventsApi* | [**createEvent**](docs/Api/EventsApi.md#createevent) | **POST** /v1/events | Ingest an event
*EventsApi* | [**getEvent**](docs/Api/EventsApi.md#getevent) | **GET** /v1/events/{id} | Retrieve an event
*EventsApi* | [**healthz**](docs/Api/EventsApi.md#healthz) | **GET** /v1/healthz | Liveness check
*EventsApi* | [**listEvents**](docs/Api/EventsApi.md#listevents) | **GET** /v1/events | List events
*SubscriptionsApi* | [**createSubscription**](docs/Api/SubscriptionsApi.md#createsubscription) | **POST** /v1/subscriptions | Create a subscription
*SubscriptionsApi* | [**deleteSubscription**](docs/Api/SubscriptionsApi.md#deletesubscription) | **DELETE** /v1/subscriptions/{id} | Delete a subscription
*SubscriptionsApi* | [**getSubscription**](docs/Api/SubscriptionsApi.md#getsubscription) | **GET** /v1/subscriptions/{id} | Retrieve a subscription
*SubscriptionsApi* | [**listSubscriptions**](docs/Api/SubscriptionsApi.md#listsubscriptions) | **GET** /v1/subscriptions | List subscriptions
*SubscriptionsApi* | [**pauseSubscription**](docs/Api/SubscriptionsApi.md#pausesubscription) | **POST** /v1/subscriptions/{id}/pause | Pause a subscription
*SubscriptionsApi* | [**resumeSubscription**](docs/Api/SubscriptionsApi.md#resumesubscription) | **POST** /v1/subscriptions/{id}/resume | Resume a paused subscription
*SubscriptionsApi* | [**rotateSubscriptionSecret**](docs/Api/SubscriptionsApi.md#rotatesubscriptionsecret) | **POST** /v1/subscriptions/{id}/rotate-secret | Rotate the signing secret
*SubscriptionsApi* | [**updateSubscription**](docs/Api/SubscriptionsApi.md#updatesubscription) | **PATCH** /v1/subscriptions/{id} | Update a subscription

## Models

- [Delivery](docs/Model/Delivery.md)
- [DeliveryAttempt](docs/Model/DeliveryAttempt.md)
- [DeliveryPage](docs/Model/DeliveryPage.md)
- [Event](docs/Model/Event.md)
- [EventCreate](docs/Model/EventCreate.md)
- [EventDeliveriesSummary](docs/Model/EventDeliveriesSummary.md)
- [EventPage](docs/Model/EventPage.md)
- [Healthz200Response](docs/Model/Healthz200Response.md)
- [Problem](docs/Model/Problem.md)
- [Subscription](docs/Model/Subscription.md)
- [SubscriptionCreate](docs/Model/SubscriptionCreate.md)
- [SubscriptionPage](docs/Model/SubscriptionPage.md)
- [SubscriptionUpdate](docs/Model/SubscriptionUpdate.md)
- [SubscriptionWithSecret](docs/Model/SubscriptionWithSecret.md)

## Authorization

Authentication schemes defined for the API:
### ApiKeyAuth

- **Type**: Bearer authentication (API Key)

## Tests

To run the tests, use:

```bash
composer install
vendor/bin/phpunit
```

## Author



## About this package

This PHP package is automatically generated by the [OpenAPI Generator](https://openapi-generator.tech) project:

- API version: `0.3.0`
    - Package version: `0.3.0`
    - Generator version: `7.22.0`
- Build package: `org.openapitools.codegen.languages.PhpClientCodegen`
