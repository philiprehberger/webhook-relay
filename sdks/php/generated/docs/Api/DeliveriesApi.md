# WebhookRelayClient\DeliveriesApi

Per-attempt delivery log for observability and retry.

All URIs are relative to https://api.webhook-relay.dcsuniverse.com, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**getDelivery()**](DeliveriesApi.md#getDelivery) | **GET** /v1/deliveries/{id} | Retrieve a delivery |
| [**listDeliveries()**](DeliveriesApi.md#listDeliveries) | **GET** /v1/deliveries | List deliveries |
| [**retryDelivery()**](DeliveriesApi.md#retryDelivery) | **POST** /v1/deliveries/{id}/retry | Manually retry a delivery |


## `getDelivery()`

```php
getDelivery($id): \WebhookRelayClient\Model\Delivery
```

Retrieve a delivery

Returns the delivery with its full attempt timeline.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure Bearer (API Key) authorization: ApiKeyAuth
$config = WebhookRelayClient\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new WebhookRelayClient\Api\DeliveriesApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$id = 'id_example'; // string

try {
    $result = $apiInstance->getDelivery($id);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DeliveriesApi->getDelivery: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **id** | **string**|  | |

### Return type

[**\WebhookRelayClient\Model\Delivery**](../Model/Delivery.md)

### Authorization

[ApiKeyAuth](../../README.md#ApiKeyAuth)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `listDeliveries()`

```php
listDeliveries($event_id, $subscription_id, $status, $cursor, $limit): \WebhookRelayClient\Model\DeliveryPage
```

List deliveries

Cursor-paginated, newest first, scoped to the caller's workspace.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure Bearer (API Key) authorization: ApiKeyAuth
$config = WebhookRelayClient\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new WebhookRelayClient\Api\DeliveriesApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$event_id = 'event_id_example'; // string
$subscription_id = 'subscription_id_example'; // string
$status = 'status_example'; // string
$cursor = 'cursor_example'; // string
$limit = 25; // int

try {
    $result = $apiInstance->listDeliveries($event_id, $subscription_id, $status, $cursor, $limit);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DeliveriesApi->listDeliveries: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **event_id** | **string**|  | [optional] |
| **subscription_id** | **string**|  | [optional] |
| **status** | **string**|  | [optional] |
| **cursor** | **string**|  | [optional] |
| **limit** | **int**|  | [optional] [default to 25] |

### Return type

[**\WebhookRelayClient\Model\DeliveryPage**](../Model/DeliveryPage.md)

### Authorization

[ApiKeyAuth](../../README.md#ApiKeyAuth)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `retryDelivery()`

```php
retryDelivery($id): \WebhookRelayClient\Model\Delivery
```

Manually retry a delivery

Sets the delivery to `pending` and enqueues a fresh delivery attempt immediately. Works regardless of current status. Does not reset `attempts_made` — the new attempt is recorded as the next one in the timeline.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure Bearer (API Key) authorization: ApiKeyAuth
$config = WebhookRelayClient\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new WebhookRelayClient\Api\DeliveriesApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$id = 'id_example'; // string

try {
    $result = $apiInstance->retryDelivery($id);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DeliveriesApi->retryDelivery: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **id** | **string**|  | |

### Return type

[**\WebhookRelayClient\Model\Delivery**](../Model/Delivery.md)

### Authorization

[ApiKeyAuth](../../README.md#ApiKeyAuth)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
