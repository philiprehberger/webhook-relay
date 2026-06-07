# WebhookRelayClient\EventsApi

Ingest, list, and retrieve events.

All URIs are relative to https://api.webhook-relay.dcsuniverse.com, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**createEvent()**](EventsApi.md#createEvent) | **POST** /v1/events | Ingest an event |
| [**getEvent()**](EventsApi.md#getEvent) | **GET** /v1/events/{id} | Retrieve an event |
| [**healthz()**](EventsApi.md#healthz) | **GET** /v1/healthz | Liveness check |
| [**listEvents()**](EventsApi.md#listEvents) | **GET** /v1/events | List events |


## `createEvent()`

```php
createEvent($event_create, $idempotency_key): \WebhookRelayClient\Model\Event
```

Ingest an event

Accepts an event and enqueues it for fan-out to all matching subscriptions. Provide an `Idempotency-Key` header to safely retry; dedup window is 24 hours per workspace.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure Bearer (API Key) authorization: ApiKeyAuth
$config = WebhookRelayClient\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new WebhookRelayClient\Api\EventsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$event_create = new \WebhookRelayClient\Model\EventCreate(); // \WebhookRelayClient\Model\EventCreate
$idempotency_key = 'idempotency_key_example'; // string | Optional dedup key; requests with the same key within 24h return the original response.

try {
    $result = $apiInstance->createEvent($event_create, $idempotency_key);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling EventsApi->createEvent: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **event_create** | [**\WebhookRelayClient\Model\EventCreate**](../Model/EventCreate.md)|  | |
| **idempotency_key** | **string**| Optional dedup key; requests with the same key within 24h return the original response. | [optional] |

### Return type

[**\WebhookRelayClient\Model\Event**](../Model/Event.md)

### Authorization

[ApiKeyAuth](../../README.md#ApiKeyAuth)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `getEvent()`

```php
getEvent($id): \WebhookRelayClient\Model\Event
```

Retrieve an event

Returns the event and a summary of its deliveries across all matching subscriptions.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure Bearer (API Key) authorization: ApiKeyAuth
$config = WebhookRelayClient\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new WebhookRelayClient\Api\EventsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$id = 'id_example'; // string | ULID of the event.

try {
    $result = $apiInstance->getEvent($id);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling EventsApi->getEvent: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **id** | **string**| ULID of the event. | |

### Return type

[**\WebhookRelayClient\Model\Event**](../Model/Event.md)

### Authorization

[ApiKeyAuth](../../README.md#ApiKeyAuth)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `healthz()`

```php
healthz(): \WebhookRelayClient\Model\Healthz200Response
```

Liveness check

Returns 200 if the API is up. Does not require auth.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new WebhookRelayClient\Api\EventsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);

try {
    $result = $apiInstance->healthz();
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling EventsApi->healthz: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

This endpoint does not need any parameter.

### Return type

[**\WebhookRelayClient\Model\Healthz200Response**](../Model/Healthz200Response.md)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `listEvents()`

```php
listEvents($type, $created_after, $cursor, $limit): \WebhookRelayClient\Model\EventPage
```

List events

Returns a cursor-paginated page of events, newest first, scoped to the caller's workspace.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure Bearer (API Key) authorization: ApiKeyAuth
$config = WebhookRelayClient\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new WebhookRelayClient\Api\EventsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$type = 'type_example'; // string | Filter by event type (exact match).
$created_after = new \DateTime('2013-10-20T19:20:30+01:00'); // \DateTime
$cursor = 'cursor_example'; // string | Opaque cursor from a prior page's `next_cursor`.
$limit = 25; // int

try {
    $result = $apiInstance->listEvents($type, $created_after, $cursor, $limit);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling EventsApi->listEvents: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **type** | **string**| Filter by event type (exact match). | [optional] |
| **created_after** | **\DateTime**|  | [optional] |
| **cursor** | **string**| Opaque cursor from a prior page&#39;s &#x60;next_cursor&#x60;. | [optional] |
| **limit** | **int**|  | [optional] [default to 25] |

### Return type

[**\WebhookRelayClient\Model\EventPage**](../Model/EventPage.md)

### Authorization

[ApiKeyAuth](../../README.md#ApiKeyAuth)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
