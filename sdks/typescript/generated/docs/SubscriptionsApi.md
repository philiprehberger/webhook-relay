# SubscriptionsApi

All URIs are relative to *https://api.webhook-relay.dcsuniverse.com*

| Method | HTTP request | Description |
|------------- | ------------- | -------------|
| [**createSubscription**](SubscriptionsApi.md#createsubscription) | **POST** /v1/subscriptions | Create a subscription |
| [**deleteSubscription**](SubscriptionsApi.md#deletesubscription) | **DELETE** /v1/subscriptions/{id} | Delete a subscription |
| [**getSubscription**](SubscriptionsApi.md#getsubscription) | **GET** /v1/subscriptions/{id} | Retrieve a subscription |
| [**listSubscriptions**](SubscriptionsApi.md#listsubscriptions) | **GET** /v1/subscriptions | List subscriptions |
| [**pauseSubscription**](SubscriptionsApi.md#pausesubscription) | **POST** /v1/subscriptions/{id}/pause | Pause a subscription |
| [**resumeSubscription**](SubscriptionsApi.md#resumesubscription) | **POST** /v1/subscriptions/{id}/resume | Resume a paused subscription |
| [**rotateSubscriptionSecret**](SubscriptionsApi.md#rotatesubscriptionsecret) | **POST** /v1/subscriptions/{id}/rotate-secret | Rotate the signing secret |
| [**updateSubscription**](SubscriptionsApi.md#updatesubscription) | **PATCH** /v1/subscriptions/{id} | Update a subscription |



## createSubscription

> SubscriptionWithSecret createSubscription(subscriptionCreate)

Create a subscription

Registers a new endpoint to receive matching events. The signing secret is generated server-side and returned ONCE in the response — store it now; you cannot retrieve it later (only rotate it). 

### Example

```ts
import {
  Configuration,
  SubscriptionsApi,
} from '@philiprehberger/webhook-relay-client';
import type { CreateSubscriptionRequest } from '@philiprehberger/webhook-relay-client';

async function example() {
  console.log("🚀 Testing @philiprehberger/webhook-relay-client SDK...");
  const config = new Configuration({ 
    // Configure HTTP bearer authorization: ApiKeyAuth
    accessToken: "YOUR BEARER TOKEN",
  });
  const api = new SubscriptionsApi(config);

  const body = {
    // SubscriptionCreate
    subscriptionCreate: ...,
  } satisfies CreateSubscriptionRequest;

  try {
    const data = await api.createSubscription(body);
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
| **subscriptionCreate** | [SubscriptionCreate](SubscriptionCreate.md) |  | |

### Return type

[**SubscriptionWithSecret**](SubscriptionWithSecret.md)

### Authorization

[ApiKeyAuth](../README.md#ApiKeyAuth)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`, `application/problem+json`


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
| **201** | Subscription created. |  -  |
| **400** | Validation error. |  -  |
| **401** | Missing or invalid API key. |  -  |

[[Back to top]](#) [[Back to API list]](../README.md#api-endpoints) [[Back to Model list]](../README.md#models) [[Back to README]](../README.md)


## deleteSubscription

> deleteSubscription(id)

Delete a subscription

Permanently deletes the subscription. Deliveries are retained for audit.

### Example

```ts
import {
  Configuration,
  SubscriptionsApi,
} from '@philiprehberger/webhook-relay-client';
import type { DeleteSubscriptionRequest } from '@philiprehberger/webhook-relay-client';

async function example() {
  console.log("🚀 Testing @philiprehberger/webhook-relay-client SDK...");
  const config = new Configuration({ 
    // Configure HTTP bearer authorization: ApiKeyAuth
    accessToken: "YOUR BEARER TOKEN",
  });
  const api = new SubscriptionsApi(config);

  const body = {
    // string | ULID of the subscription.
    id: id_example,
  } satisfies DeleteSubscriptionRequest;

  try {
    const data = await api.deleteSubscription(body);
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
| **id** | `string` | ULID of the subscription. | [Defaults to `undefined`] |

### Return type

`void` (Empty response body)

### Authorization

[ApiKeyAuth](../README.md#ApiKeyAuth)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/problem+json`


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
| **204** | Deleted. |  -  |
| **401** | Missing or invalid API key. |  -  |
| **404** | Resource not found in this workspace. |  -  |

[[Back to top]](#) [[Back to API list]](../README.md#api-endpoints) [[Back to Model list]](../README.md#models) [[Back to README]](../README.md)


## getSubscription

> Subscription getSubscription(id)

Retrieve a subscription

Returns a single subscription without its signing secret.

### Example

```ts
import {
  Configuration,
  SubscriptionsApi,
} from '@philiprehberger/webhook-relay-client';
import type { GetSubscriptionRequest } from '@philiprehberger/webhook-relay-client';

async function example() {
  console.log("🚀 Testing @philiprehberger/webhook-relay-client SDK...");
  const config = new Configuration({ 
    // Configure HTTP bearer authorization: ApiKeyAuth
    accessToken: "YOUR BEARER TOKEN",
  });
  const api = new SubscriptionsApi(config);

  const body = {
    // string | ULID of the subscription.
    id: id_example,
  } satisfies GetSubscriptionRequest;

  try {
    const data = await api.getSubscription(body);
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
| **id** | `string` | ULID of the subscription. | [Defaults to `undefined`] |

### Return type

[**Subscription**](Subscription.md)

### Authorization

[ApiKeyAuth](../README.md#ApiKeyAuth)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`, `application/problem+json`


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
| **200** | The subscription. |  -  |
| **401** | Missing or invalid API key. |  -  |
| **404** | Resource not found in this workspace. |  -  |

[[Back to top]](#) [[Back to API list]](../README.md#api-endpoints) [[Back to Model list]](../README.md#models) [[Back to README]](../README.md)


## listSubscriptions

> SubscriptionPage listSubscriptions(cursor, limit, state)

List subscriptions

Cursor-paginated, scoped to the caller\&#39;s workspace.

### Example

```ts
import {
  Configuration,
  SubscriptionsApi,
} from '@philiprehberger/webhook-relay-client';
import type { ListSubscriptionsRequest } from '@philiprehberger/webhook-relay-client';

async function example() {
  console.log("🚀 Testing @philiprehberger/webhook-relay-client SDK...");
  const config = new Configuration({ 
    // Configure HTTP bearer authorization: ApiKeyAuth
    accessToken: "YOUR BEARER TOKEN",
  });
  const api = new SubscriptionsApi(config);

  const body = {
    // string (optional)
    cursor: cursor_example,
    // number (optional)
    limit: 56,
    // 'active' | 'paused' | 'disabled' (optional)
    state: state_example,
  } satisfies ListSubscriptionsRequest;

  try {
    const data = await api.listSubscriptions(body);
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
| **cursor** | `string` |  | [Optional] [Defaults to `undefined`] |
| **limit** | `number` |  | [Optional] [Defaults to `25`] |
| **state** | `active`, `paused`, `disabled` |  | [Optional] [Defaults to `undefined`] [Enum: active, paused, disabled] |

### Return type

[**SubscriptionPage**](SubscriptionPage.md)

### Authorization

[ApiKeyAuth](../README.md#ApiKeyAuth)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`, `application/problem+json`


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
| **200** | Page of subscriptions. |  -  |
| **401** | Missing or invalid API key. |  -  |

[[Back to top]](#) [[Back to API list]](../README.md#api-endpoints) [[Back to Model list]](../README.md#models) [[Back to README]](../README.md)


## pauseSubscription

> Subscription pauseSubscription(id)

Pause a subscription

Stops new deliveries until resumed. Existing in-flight deliveries are not cancelled.

### Example

```ts
import {
  Configuration,
  SubscriptionsApi,
} from '@philiprehberger/webhook-relay-client';
import type { PauseSubscriptionRequest } from '@philiprehberger/webhook-relay-client';

async function example() {
  console.log("🚀 Testing @philiprehberger/webhook-relay-client SDK...");
  const config = new Configuration({ 
    // Configure HTTP bearer authorization: ApiKeyAuth
    accessToken: "YOUR BEARER TOKEN",
  });
  const api = new SubscriptionsApi(config);

  const body = {
    // string | ULID of the subscription.
    id: id_example,
  } satisfies PauseSubscriptionRequest;

  try {
    const data = await api.pauseSubscription(body);
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
| **id** | `string` | ULID of the subscription. | [Defaults to `undefined`] |

### Return type

[**Subscription**](Subscription.md)

### Authorization

[ApiKeyAuth](../README.md#ApiKeyAuth)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`, `application/problem+json`


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
| **200** | Paused. |  -  |
| **401** | Missing or invalid API key. |  -  |
| **404** | Resource not found in this workspace. |  -  |

[[Back to top]](#) [[Back to API list]](../README.md#api-endpoints) [[Back to Model list]](../README.md#models) [[Back to README]](../README.md)


## resumeSubscription

> Subscription resumeSubscription(id)

Resume a paused subscription

Resets consecutive_failures to 0 and returns the subscription to active state.

### Example

```ts
import {
  Configuration,
  SubscriptionsApi,
} from '@philiprehberger/webhook-relay-client';
import type { ResumeSubscriptionRequest } from '@philiprehberger/webhook-relay-client';

async function example() {
  console.log("🚀 Testing @philiprehberger/webhook-relay-client SDK...");
  const config = new Configuration({ 
    // Configure HTTP bearer authorization: ApiKeyAuth
    accessToken: "YOUR BEARER TOKEN",
  });
  const api = new SubscriptionsApi(config);

  const body = {
    // string | ULID of the subscription.
    id: id_example,
  } satisfies ResumeSubscriptionRequest;

  try {
    const data = await api.resumeSubscription(body);
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
| **id** | `string` | ULID of the subscription. | [Defaults to `undefined`] |

### Return type

[**Subscription**](Subscription.md)

### Authorization

[ApiKeyAuth](../README.md#ApiKeyAuth)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`, `application/problem+json`


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
| **200** | Resumed. |  -  |
| **401** | Missing or invalid API key. |  -  |
| **404** | Resource not found in this workspace. |  -  |

[[Back to top]](#) [[Back to API list]](../README.md#api-endpoints) [[Back to Model list]](../README.md#models) [[Back to README]](../README.md)


## rotateSubscriptionSecret

> SubscriptionWithSecret rotateSubscriptionSecret(id)

Rotate the signing secret

Generates a new secret and returns it once. The previous secret remains valid for a 48-hour grace window so receivers can update their verifier without losing requests. 

### Example

```ts
import {
  Configuration,
  SubscriptionsApi,
} from '@philiprehberger/webhook-relay-client';
import type { RotateSubscriptionSecretRequest } from '@philiprehberger/webhook-relay-client';

async function example() {
  console.log("🚀 Testing @philiprehberger/webhook-relay-client SDK...");
  const config = new Configuration({ 
    // Configure HTTP bearer authorization: ApiKeyAuth
    accessToken: "YOUR BEARER TOKEN",
  });
  const api = new SubscriptionsApi(config);

  const body = {
    // string | ULID of the subscription.
    id: id_example,
  } satisfies RotateSubscriptionSecretRequest;

  try {
    const data = await api.rotateSubscriptionSecret(body);
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
| **id** | `string` | ULID of the subscription. | [Defaults to `undefined`] |

### Return type

[**SubscriptionWithSecret**](SubscriptionWithSecret.md)

### Authorization

[ApiKeyAuth](../README.md#ApiKeyAuth)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`, `application/problem+json`


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
| **200** | Rotated. |  -  |
| **401** | Missing or invalid API key. |  -  |
| **404** | Resource not found in this workspace. |  -  |

[[Back to top]](#) [[Back to API list]](../README.md#api-endpoints) [[Back to Model list]](../README.md#models) [[Back to README]](../README.md)


## updateSubscription

> Subscription updateSubscription(id, subscriptionUpdate)

Update a subscription

Partial update. Mutable fields are name, url, event_filter.

### Example

```ts
import {
  Configuration,
  SubscriptionsApi,
} from '@philiprehberger/webhook-relay-client';
import type { UpdateSubscriptionRequest } from '@philiprehberger/webhook-relay-client';

async function example() {
  console.log("🚀 Testing @philiprehberger/webhook-relay-client SDK...");
  const config = new Configuration({ 
    // Configure HTTP bearer authorization: ApiKeyAuth
    accessToken: "YOUR BEARER TOKEN",
  });
  const api = new SubscriptionsApi(config);

  const body = {
    // string | ULID of the subscription.
    id: id_example,
    // SubscriptionUpdate
    subscriptionUpdate: ...,
  } satisfies UpdateSubscriptionRequest;

  try {
    const data = await api.updateSubscription(body);
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
| **id** | `string` | ULID of the subscription. | [Defaults to `undefined`] |
| **subscriptionUpdate** | [SubscriptionUpdate](SubscriptionUpdate.md) |  | |

### Return type

[**Subscription**](Subscription.md)

### Authorization

[ApiKeyAuth](../README.md#ApiKeyAuth)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`, `application/problem+json`


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
| **200** | Updated subscription. |  -  |
| **400** | Validation error. |  -  |
| **401** | Missing or invalid API key. |  -  |
| **404** | Resource not found in this workspace. |  -  |

[[Back to top]](#) [[Back to API list]](../README.md#api-endpoints) [[Back to Model list]](../README.md#models) [[Back to README]](../README.md)

