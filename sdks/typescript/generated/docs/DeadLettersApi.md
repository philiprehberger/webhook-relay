# DeadLettersApi

All URIs are relative to *https://api.webhook-relay.dcsuniverse.com*

| Method | HTTP request | Description |
|------------- | ------------- | -------------|
| [**listDeadLetters**](DeadLettersApi.md#listdeadletters) | **GET** /v1/dead-letters | List dead-lettered deliveries |
| [**replayDeadLetter**](DeadLettersApi.md#replaydeadletter) | **POST** /v1/dead-letters/{id}/replay | Replay a dead-lettered delivery |



## listDeadLetters

> DeliveryPage listDeadLetters(subscriptionId, since, cursor, limit)

List dead-lettered deliveries

Returns deliveries with &#x60;status&#x3D;dead&#x60; — the human-attention queue. A delivery lands here when:   - The subscriber returned 4xx (not retried).   - Retries exhausted on 5xx / timeout / connection errors.   - The subscription was paused / disabled before the attempt.   - The SSRF guard blocked the URL. 

### Example

```ts
import {
  Configuration,
  DeadLettersApi,
} from '@philiprehberger/webhook-relay-client';
import type { ListDeadLettersRequest } from '@philiprehberger/webhook-relay-client';

async function example() {
  console.log("🚀 Testing @philiprehberger/webhook-relay-client SDK...");
  const config = new Configuration({ 
    // Configure HTTP bearer authorization: ApiKeyAuth
    accessToken: "YOUR BEARER TOKEN",
  });
  const api = new DeadLettersApi(config);

  const body = {
    // string (optional)
    subscriptionId: subscriptionId_example,
    // Date (optional)
    since: 2013-10-20T19:20:30+01:00,
    // string (optional)
    cursor: cursor_example,
    // number (optional)
    limit: 56,
  } satisfies ListDeadLettersRequest;

  try {
    const data = await api.listDeadLetters(body);
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
| **subscriptionId** | `string` |  | [Optional] [Defaults to `undefined`] |
| **since** | `Date` |  | [Optional] [Defaults to `undefined`] |
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
| **200** | Page of dead-lettered deliveries. |  -  |
| **401** | Missing or invalid API key. |  -  |

[[Back to top]](#) [[Back to API list]](../README.md#api-endpoints) [[Back to Model list]](../README.md#models) [[Back to README]](../README.md)


## replayDeadLetter

> Delivery replayDeadLetter(id)

Replay a dead-lettered delivery

Resurrects a &#x60;status&#x3D;dead&#x60; delivery — sets it back to &#x60;pending&#x60; and enqueues a fresh attempt. Returns 409 if the delivery is not in the dead-letter state. To retry deliveries in other states use POST /v1/deliveries/{id}/retry instead. 

### Example

```ts
import {
  Configuration,
  DeadLettersApi,
} from '@philiprehberger/webhook-relay-client';
import type { ReplayDeadLetterRequest } from '@philiprehberger/webhook-relay-client';

async function example() {
  console.log("🚀 Testing @philiprehberger/webhook-relay-client SDK...");
  const config = new Configuration({ 
    // Configure HTTP bearer authorization: ApiKeyAuth
    accessToken: "YOUR BEARER TOKEN",
  });
  const api = new DeadLettersApi(config);

  const body = {
    // string
    id: id_example,
  } satisfies ReplayDeadLetterRequest;

  try {
    const data = await api.replayDeadLetter(body);
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
| **200** | Replayed delivery (now pending) with attempts. |  -  |
| **401** | Missing or invalid API key. |  -  |
| **404** | Resource not found in this workspace. |  -  |
| **409** | Delivery is not dead-lettered. |  -  |

[[Back to top]](#) [[Back to API list]](../README.md#api-endpoints) [[Back to Model list]](../README.md#models) [[Back to README]](../README.md)

