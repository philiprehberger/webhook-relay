# WebhookRelayClient\DeadLettersApi



All URIs are relative to https://api.webhook-relay.dcsuniverse.com, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**listDeadLetters()**](DeadLettersApi.md#listDeadLetters) | **GET** /v1/dead-letters | List dead-lettered deliveries |
| [**replayDeadLetter()**](DeadLettersApi.md#replayDeadLetter) | **POST** /v1/dead-letters/{id}/replay | Replay a dead-lettered delivery |


## `listDeadLetters()`

```php
listDeadLetters($subscription_id, $since, $cursor, $limit): \WebhookRelayClient\Model\DeliveryPage
```

List dead-lettered deliveries

Returns deliveries with `status=dead` — the human-attention queue. A delivery lands here when:   - The subscriber returned 4xx (not retried).   - Retries exhausted on 5xx / timeout / connection errors.   - The subscription was paused / disabled before the attempt.   - The SSRF guard blocked the URL.

### Example

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

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **subscription_id** | **string**|  | [optional] |
| **since** | **\DateTime**|  | [optional] |
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

## `replayDeadLetter()`

```php
replayDeadLetter($id): \WebhookRelayClient\Model\Delivery
```

Replay a dead-lettered delivery

Resurrects a `status=dead` delivery — sets it back to `pending` and enqueues a fresh attempt. Returns 409 if the delivery is not in the dead-letter state. To retry deliveries in other states use POST /v1/deliveries/{id}/retry instead.

### Example

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
$id = 'id_example'; // string

try {
    $result = $apiInstance->replayDeadLetter($id);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DeadLettersApi->replayDeadLetter: ', $e->getMessage(), PHP_EOL;
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
