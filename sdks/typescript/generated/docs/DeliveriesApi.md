# DeliveriesApi

All URIs are relative to *https://api.webhook-relay.dcsuniverse.com*

| Method | HTTP request | Description |
|------------- | ------------- | -------------|
| [**getDelivery**](DeliveriesApi.md#getdelivery) | **GET** /v1/deliveries/{id} | Retrieve a delivery |
| [**listDeliveries**](DeliveriesApi.md#listdeliveries) | **GET** /v1/deliveries | List deliveries |
| [**retryDelivery**](DeliveriesApi.md#retrydelivery) | **POST** /v1/deliveries/{id}/retry | Manually retry a delivery |



## getDelivery

> Delivery getDelivery(id)

Retrieve a delivery

Returns the delivery with its full attempt timeline.

### Example

```ts
import {
  Configuration,
  DeliveriesApi,
} from '@philiprehberger/webhook-relay-client';
import type { GetDeliveryRequest } from '@philiprehberger/webhook-relay-client';

async function example() {
  console.log("🚀 Testing @philiprehberger/webhook-relay-client SDK...");
  const config = new Configuration({ 
    // Configure HTTP bearer authorization: ApiKeyAuth
    accessToken: "YOUR BEARER TOKEN",
  });
  const api = new DeliveriesApi(config);

  const body = {
    // string
    id: id_example,
  } satisfies GetDeliveryRequest;

  try {
    const data = await api.getDelivery(body);
    console.log(data);
  } catch (error) {
    console.error(error);
  }
}

// Run the test
example().catch(console.error);
```

### Parameters


| Name | Type | Description  | Notes |
|------------- | ------------- | ------------- | -------------|
| **id** | `string` |  | [Defaults to `undefined`] |

### Return type

[**Delivery**](Delivery.md)

### Authorization

[ApiKeyAuth](../README.md#ApiKeyAuth)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`, `application/problem+json`


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
| **200** | The delivery with attempts. |  -  |
| **401** | Missing or invalid API key. |  -  |
| **404** | Resource not found in this workspace. |  -  |

[[Back to top]](#) [[Back to API list]](../README.md#api-endpoints) [[Back to Model list]](../README.md#models) [[Back to README]](../README.md)


## listDeliveries

> DeliveryPage listDeliveries(eventId, subscriptionId, status, cursor, limit)

List deliveries

Cursor-paginated, newest first, scoped to the caller\&#39;s workspace.

### Example

```ts
import {
  Configuration,
  DeliveriesApi,
} from '@philiprehberger/webhook-relay-client';
import type { ListDeliveriesRequest } from '@philiprehberger/webhook-relay-client';

async function example() {
  console.log("🚀 Testing @philiprehberger/webhook-relay-client SDK...");
  const config = new Configuration({ 
    // Configure HTTP bearer authorization: ApiKeyAuth
    accessToken: "YOUR BEARER TOKEN",
  });
  const api = new DeliveriesApi(config);

  const body = {
    // string (optional)
    eventId: eventId_example,
    // string (optional)
    subscriptionId: subscriptionId_example,
    // 'pending' | 'success' | 'failed' | 'dead' (optional)
    status: status_example,
    // string (optional)
    cursor: cursor_example,
    // number (optional)
    limit: 56,
  } satisfies ListDeliveriesRequest;

  try {
    const data = await api.listDeliveries(body);
    console.log(data);
  } catch (error) {
    console.error(error);
  }
}

// Run the test
example().catch(console.error);
```

### Parameters


| Name | Type | Description  | Notes |
|------------- | ------------- | ------------- | -------------|
| **eventId** | `string` |  | [Optional] [Defaults to `undefined`] |
| **subscriptionId** | `string` |  | [Optional] [Defaults to `undefined`] |
| **status** | `pending`, `success`, `failed`, `dead` |  | [Optional] [Defaults to `undefined`] [Enum: pending, success, failed, dead] |
| **cursor** | `string` |  | [Optional] [Defaults to `undefined`] |
| **limit** | `number` |  | [Optional] [Defaults to `25`] |

### Return type

[**DeliveryPage**](DeliveryPage.md)

### Authorization

[ApiKeyAuth](../README.md#ApiKeyAuth)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`, `application/problem+json`


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
| **200** | Page of deliveries. |  -  |
| **401** | Missing or invalid API key. |  -  |

[[Back to top]](#) [[Back to API list]](../README.md#api-endpoints) [[Back to Model list]](../README.md#models) [[Back to README]](../README.md)


## retryDelivery

> Delivery retryDelivery(id)

Manually retry a delivery

Sets the delivery to &#x60;pending&#x60; and enqueues a fresh delivery attempt immediately. Works regardless of current status. Does not reset &#x60;attempts_made&#x60; — the new attempt is recorded as the next one in the timeline. 

### Example

```ts
import {
  Configuration,
  DeliveriesApi,
} from '@philiprehberger/webhook-relay-client';
import type { RetryDeliveryRequest } from '@philiprehberger/webhook-relay-client';

async function example() {
  console.log("🚀 Testing @philiprehberger/webhook-relay-client SDK...");
  const config = new Configuration({ 
    // Configure HTTP bearer authorization: ApiKeyAuth
    accessToken: "YOUR BEARER TOKEN",
  });
  const api = new DeliveriesApi(config);

  const body = {
    // string
    id: id_example,
  } satisfies RetryDeliveryRequest;

  try {
    const data = await api.retryDelivery(body);
    console.log(data);
  } catch (error) {
    console.error(error);
  }
}

// Run the test
example().catch(console.error);
```

### Parameters


| Name | Type | Description  | Notes |
|------------- | ------------- | ------------- | -------------|
| **id** | `string` |  | [Defaults to `undefined`] |

### Return type

[**Delivery**](Delivery.md)

### Authorization

[ApiKeyAuth](../README.md#ApiKeyAuth)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`, `application/problem+json`


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
| **200** | Updated delivery with attempts. |  -  |
| **401** | Missing or invalid API key. |  -  |
| **404** | Resource not found in this workspace. |  -  |

[[Back to top]](#) [[Back to API list]](../README.md#api-endpoints) [[Back to Model list]](../README.md#models) [[Back to README]](../README.md)

