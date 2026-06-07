# EventsApi

All URIs are relative to *https://api.webhook-relay.dcsuniverse.com*

| Method | HTTP request | Description |
|------------- | ------------- | -------------|
| [**createEvent**](EventsApi.md#createevent) | **POST** /v1/events | Ingest an event |
| [**getEvent**](EventsApi.md#getevent) | **GET** /v1/events/{id} | Retrieve an event |
| [**healthz**](EventsApi.md#healthz) | **GET** /v1/healthz | Liveness check |
| [**listEvents**](EventsApi.md#listevents) | **GET** /v1/events | List events |



## createEvent

> Event createEvent(eventCreate, idempotencyKey)

Ingest an event

Accepts an event and enqueues it for fan-out to all matching subscriptions. Provide an &#x60;Idempotency-Key&#x60; header to safely retry; dedup window is 24 hours per workspace. 

### Example

```ts
import {
  Configuration,
  EventsApi,
} from '@philiprehberger/webhook-relay-client';
import type { CreateEventRequest } from '@philiprehberger/webhook-relay-client';

async function example() {
  console.log("🚀 Testing @philiprehberger/webhook-relay-client SDK...");
  const config = new Configuration({ 
    // Configure HTTP bearer authorization: ApiKeyAuth
    accessToken: "YOUR BEARER TOKEN",
  });
  const api = new EventsApi(config);

  const body = {
    // EventCreate
    eventCreate: ...,
    // string | Optional dedup key; requests with the same key within 24h return the original response. (optional)
    idempotencyKey: idempotencyKey_example,
  } satisfies CreateEventRequest;

  try {
    const data = await api.createEvent(body);
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
| **eventCreate** | [EventCreate](EventCreate.md) |  | |
| **idempotencyKey** | `string` | Optional dedup key; requests with the same key within 24h return the original response. | [Optional] [Defaults to `undefined`] |

### Return type

[**Event**](Event.md)

### Authorization

[ApiKeyAuth](../README.md#ApiKeyAuth)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`, `application/problem+json`


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
| **202** | Event accepted for fan-out. |  -  |
| **400** | Validation error. |  -  |
| **401** | Missing or invalid API key. |  -  |
| **409** | Idempotency key already used with a different payload. |  -  |
| **429** | Workspace rate limit exceeded. |  * X-RateLimit-Limit -  <br>  * X-RateLimit-Remaining -  <br>  * X-RateLimit-Reset -  <br>  |

[[Back to top]](#) [[Back to API list]](../README.md#api-endpoints) [[Back to Model list]](../README.md#models) [[Back to README]](../README.md)


## getEvent

> Event getEvent(id)

Retrieve an event

Returns the event and a summary of its deliveries across all matching subscriptions.

### Example

```ts
import {
  Configuration,
  EventsApi,
} from '@philiprehberger/webhook-relay-client';
import type { GetEventRequest } from '@philiprehberger/webhook-relay-client';

async function example() {
  console.log("🚀 Testing @philiprehberger/webhook-relay-client SDK...");
  const config = new Configuration({ 
    // Configure HTTP bearer authorization: ApiKeyAuth
    accessToken: "YOUR BEARER TOKEN",
  });
  const api = new EventsApi(config);

  const body = {
    // string | ULID of the event.
    id: id_example,
  } satisfies GetEventRequest;

  try {
    const data = await api.getEvent(body);
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
| **id** | `string` | ULID of the event. | [Defaults to `undefined`] |

### Return type

[**Event**](Event.md)

### Authorization

[ApiKeyAuth](../README.md#ApiKeyAuth)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`, `application/problem+json`


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
| **200** | The event with a delivery summary. |  -  |
| **401** | Missing or invalid API key. |  -  |
| **404** | Resource not found in this workspace. |  -  |

[[Back to top]](#) [[Back to API list]](../README.md#api-endpoints) [[Back to Model list]](../README.md#models) [[Back to README]](../README.md)


## healthz

> Healthz200Response healthz()

Liveness check

Returns 200 if the API is up. Does not require auth.

### Example

```ts
import {
  Configuration,
  EventsApi,
} from '@philiprehberger/webhook-relay-client';
import type { HealthzRequest } from '@philiprehberger/webhook-relay-client';

async function example() {
  console.log("🚀 Testing @philiprehberger/webhook-relay-client SDK...");
  const api = new EventsApi();

  try {
    const data = await api.healthz();
    console.log(data);
  } catch (error) {
    console.error(error);
  }
}

// Run the test
example().catch(console.error);
```

### Parameters

This endpoint does not need any parameter.

### Return type

[**Healthz200Response**](Healthz200Response.md)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
| **200** | Service is up. |  -  |

[[Back to top]](#) [[Back to API list]](../README.md#api-endpoints) [[Back to Model list]](../README.md#models) [[Back to README]](../README.md)


## listEvents

> EventPage listEvents(type, createdAfter, cursor, limit)

List events

Returns a cursor-paginated page of events, newest first, scoped to the caller\&#39;s workspace.

### Example

```ts
import {
  Configuration,
  EventsApi,
} from '@philiprehberger/webhook-relay-client';
import type { ListEventsRequest } from '@philiprehberger/webhook-relay-client';

async function example() {
  console.log("🚀 Testing @philiprehberger/webhook-relay-client SDK...");
  const config = new Configuration({ 
    // Configure HTTP bearer authorization: ApiKeyAuth
    accessToken: "YOUR BEARER TOKEN",
  });
  const api = new EventsApi(config);

  const body = {
    // string | Filter by event type (exact match). (optional)
    type: type_example,
    // Date (optional)
    createdAfter: 2013-10-20T19:20:30+01:00,
    // string | Opaque cursor from a prior page\'s `next_cursor`. (optional)
    cursor: cursor_example,
    // number (optional)
    limit: 56,
  } satisfies ListEventsRequest;

  try {
    const data = await api.listEvents(body);
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
| **type** | `string` | Filter by event type (exact match). | [Optional] [Defaults to `undefined`] |
| **createdAfter** | `Date` |  | [Optional] [Defaults to `undefined`] |
| **cursor** | `string` | Opaque cursor from a prior page\&#39;s &#x60;next_cursor&#x60;. | [Optional] [Defaults to `undefined`] |
| **limit** | `number` |  | [Optional] [Defaults to `25`] |

### Return type

[**EventPage**](EventPage.md)

### Authorization

[ApiKeyAuth](../README.md#ApiKeyAuth)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`, `application/problem+json`


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
| **200** | Page of events, newest first. |  -  |
| **401** | Missing or invalid API key. |  -  |

[[Back to top]](#) [[Back to API list]](../README.md#api-endpoints) [[Back to Model list]](../README.md#models) [[Back to README]](../README.md)

