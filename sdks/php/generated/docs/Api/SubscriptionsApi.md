# WebhookRelayClient\SubscriptionsApi

Manage delivery destinations and signing secrets.

All URIs are relative to https://api.webhook-relay.dcsuniverse.com, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**createSubscription()**](SubscriptionsApi.md#createSubscription) | **POST** /v1/subscriptions | Create a subscription |
| [**deleteSubscription()**](SubscriptionsApi.md#deleteSubscription) | **DELETE** /v1/subscriptions/{id} | Delete a subscription |
| [**getSubscription()**](SubscriptionsApi.md#getSubscription) | **GET** /v1/subscriptions/{id} | Retrieve a subscription |
| [**listSubscriptions()**](SubscriptionsApi.md#listSubscriptions) | **GET** /v1/subscriptions | List subscriptions |
| [**pauseSubscription()**](SubscriptionsApi.md#pauseSubscription) | **POST** /v1/subscriptions/{id}/pause | Pause a subscription |
| [**resumeSubscription()**](SubscriptionsApi.md#resumeSubscription) | **POST** /v1/subscriptions/{id}/resume | Resume a paused subscription |
| [**rotateSubscriptionSecret()**](SubscriptionsApi.md#rotateSubscriptionSecret) | **POST** /v1/subscriptions/{id}/rotate-secret | Rotate the signing secret |
| [**updateSubscription()**](SubscriptionsApi.md#updateSubscription) | **PATCH** /v1/subscriptions/{id} | Update a subscription |


## `createSubscription()`

```php
createSubscription($subscription_create): \WebhookRelayClient\Model\SubscriptionWithSecret
```

Create a subscription

Registers a new endpoint to receive matching events. The signing secret is generated server-side and returned ONCE in the response — store it now; you cannot retrieve it later (only rotate it).

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure Bearer (API Key) authorization: ApiKeyAuth
$config = WebhookRelayClient\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new WebhookRelayClient\Api\SubscriptionsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$subscription_create = new \WebhookRelayClient\Model\SubscriptionCreate(); // \WebhookRelayClient\Model\SubscriptionCreate

try {
    $result = $apiInstance->createSubscription($subscription_create);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling SubscriptionsApi->createSubscription: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **subscription_create** | [**\WebhookRelayClient\Model\SubscriptionCreate**](../Model/SubscriptionCreate.md)|  | |

### Return type

[**\WebhookRelayClient\Model\SubscriptionWithSecret**](../Model/SubscriptionWithSecret.md)

### Authorization

[ApiKeyAuth](../../README.md#ApiKeyAuth)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `deleteSubscription()`

```php
deleteSubscription($id)
```

Delete a subscription

Permanently deletes the subscription. Deliveries are retained for audit.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure Bearer (API Key) authorization: ApiKeyAuth
$config = WebhookRelayClient\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new WebhookRelayClient\Api\SubscriptionsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$id = 'id_example'; // string | ULID of the subscription.

try {
    $apiInstance->deleteSubscription($id);
} catch (Exception $e) {
    echo 'Exception when calling SubscriptionsApi->deleteSubscription: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **id** | **string**| ULID of the subscription. | |

### Return type

void (empty response body)

### Authorization

[ApiKeyAuth](../../README.md#ApiKeyAuth)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `getSubscription()`

```php
getSubscription($id): \WebhookRelayClient\Model\Subscription
```

Retrieve a subscription

Returns a single subscription without its signing secret.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure Bearer (API Key) authorization: ApiKeyAuth
$config = WebhookRelayClient\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new WebhookRelayClient\Api\SubscriptionsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$id = 'id_example'; // string | ULID of the subscription.

try {
    $result = $apiInstance->getSubscription($id);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling SubscriptionsApi->getSubscription: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **id** | **string**| ULID of the subscription. | |

### Return type

[**\WebhookRelayClient\Model\Subscription**](../Model/Subscription.md)

### Authorization

[ApiKeyAuth](../../README.md#ApiKeyAuth)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `listSubscriptions()`

```php
listSubscriptions($cursor, $limit, $state): \WebhookRelayClient\Model\SubscriptionPage
```

List subscriptions

Cursor-paginated, scoped to the caller's workspace.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure Bearer (API Key) authorization: ApiKeyAuth
$config = WebhookRelayClient\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new WebhookRelayClient\Api\SubscriptionsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$cursor = 'cursor_example'; // string
$limit = 25; // int
$state = 'state_example'; // string

try {
    $result = $apiInstance->listSubscriptions($cursor, $limit, $state);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling SubscriptionsApi->listSubscriptions: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **cursor** | **string**|  | [optional] |
| **limit** | **int**|  | [optional] [default to 25] |
| **state** | **string**|  | [optional] |

### Return type

[**\WebhookRelayClient\Model\SubscriptionPage**](../Model/SubscriptionPage.md)

### Authorization

[ApiKeyAuth](../../README.md#ApiKeyAuth)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `pauseSubscription()`

```php
pauseSubscription($id): \WebhookRelayClient\Model\Subscription
```

Pause a subscription

Stops new deliveries until resumed. Existing in-flight deliveries are not cancelled.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure Bearer (API Key) authorization: ApiKeyAuth
$config = WebhookRelayClient\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new WebhookRelayClient\Api\SubscriptionsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$id = 'id_example'; // string | ULID of the subscription.

try {
    $result = $apiInstance->pauseSubscription($id);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling SubscriptionsApi->pauseSubscription: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **id** | **string**| ULID of the subscription. | |

### Return type

[**\WebhookRelayClient\Model\Subscription**](../Model/Subscription.md)

### Authorization

[ApiKeyAuth](../../README.md#ApiKeyAuth)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `resumeSubscription()`

```php
resumeSubscription($id): \WebhookRelayClient\Model\Subscription
```

Resume a paused subscription

Resets consecutive_failures to 0 and returns the subscription to active state.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure Bearer (API Key) authorization: ApiKeyAuth
$config = WebhookRelayClient\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new WebhookRelayClient\Api\SubscriptionsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$id = 'id_example'; // string | ULID of the subscription.

try {
    $result = $apiInstance->resumeSubscription($id);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling SubscriptionsApi->resumeSubscription: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **id** | **string**| ULID of the subscription. | |

### Return type

[**\WebhookRelayClient\Model\Subscription**](../Model/Subscription.md)

### Authorization

[ApiKeyAuth](../../README.md#ApiKeyAuth)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `rotateSubscriptionSecret()`

```php
rotateSubscriptionSecret($id): \WebhookRelayClient\Model\SubscriptionWithSecret
```

Rotate the signing secret

Generates a new secret and returns it once. The previous secret remains valid for a 48-hour grace window so receivers can update their verifier without losing requests.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure Bearer (API Key) authorization: ApiKeyAuth
$config = WebhookRelayClient\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new WebhookRelayClient\Api\SubscriptionsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$id = 'id_example'; // string | ULID of the subscription.

try {
    $result = $apiInstance->rotateSubscriptionSecret($id);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling SubscriptionsApi->rotateSubscriptionSecret: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **id** | **string**| ULID of the subscription. | |

### Return type

[**\WebhookRelayClient\Model\SubscriptionWithSecret**](../Model/SubscriptionWithSecret.md)

### Authorization

[ApiKeyAuth](../../README.md#ApiKeyAuth)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `updateSubscription()`

```php
updateSubscription($id, $subscription_update): \WebhookRelayClient\Model\Subscription
```

Update a subscription

Partial update. Mutable fields are name, url, event_filter.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure Bearer (API Key) authorization: ApiKeyAuth
$config = WebhookRelayClient\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new WebhookRelayClient\Api\SubscriptionsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$id = 'id_example'; // string | ULID of the subscription.
$subscription_update = new \WebhookRelayClient\Model\SubscriptionUpdate(); // \WebhookRelayClient\Model\SubscriptionUpdate

try {
    $result = $apiInstance->updateSubscription($id, $subscription_update);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling SubscriptionsApi->updateSubscription: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **id** | **string**| ULID of the subscription. | |
| **subscription_update** | [**\WebhookRelayClient\Model\SubscriptionUpdate**](../Model/SubscriptionUpdate.md)|  | |

### Return type

[**\WebhookRelayClient\Model\Subscription**](../Model/Subscription.md)

### Authorization

[ApiKeyAuth](../../README.md#ApiKeyAuth)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
