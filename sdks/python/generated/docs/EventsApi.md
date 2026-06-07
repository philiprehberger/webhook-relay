# webhook_relay_client.EventsApi

All URIs are relative to *https://api.webhook-relay.dcsuniverse.com*

Method | HTTP request | Description
------------- | ------------- | -------------
[**create_event**](EventsApi.md#create_event) | **POST** /v1/events | Ingest an event
[**get_event**](EventsApi.md#get_event) | **GET** /v1/events/{id} | Retrieve an event
[**healthz**](EventsApi.md#healthz) | **GET** /v1/healthz | Liveness check
[**list_events**](EventsApi.md#list_events) | **GET** /v1/events | List events


# **create_event**
> Event create_event(event_create, idempotency_key=idempotency_key)

Ingest an event

Accepts an event and enqueues it for fan-out to all matching
subscriptions. Provide an `Idempotency-Key` header to safely retry;
dedup window is 24 hours per workspace.


### Example

* Bearer (API Key) Authentication (ApiKeyAuth):

```python
import webhook_relay_client
from webhook_relay_client.models.event import Event
from webhook_relay_client.models.event_create import EventCreate
from webhook_relay_client.rest import ApiException
from pprint import pprint

# Defining the host is optional and defaults to https://api.webhook-relay.dcsuniverse.com
# See configuration.py for a list of all supported configuration parameters.
configuration = webhook_relay_client.Configuration(
    host = "https://api.webhook-relay.dcsuniverse.com"
)

# The client must configure the authentication and authorization parameters
# in accordance with the API server security policy.
# Examples for each auth method are provided below, use the example that
# satisfies your auth use case.

# Configure Bearer authorization (API Key): ApiKeyAuth
configuration = webhook_relay_client.Configuration(
    access_token = os.environ["BEARER_TOKEN"]
)

# Enter a context with an instance of the API client
with webhook_relay_client.ApiClient(configuration) as api_client:
    # Create an instance of the API class
    api_instance = webhook_relay_client.EventsApi(api_client)
    event_create = webhook_relay_client.EventCreate() # EventCreate | 
    idempotency_key = 'idempotency_key_example' # str | Optional dedup key; requests with the same key within 24h return the original response. (optional)

    try:
        # Ingest an event
        api_response = api_instance.create_event(event_create, idempotency_key=idempotency_key)
        print("The response of EventsApi->create_event:\n")
        pprint(api_response)
    except Exception as e:
        print("Exception when calling EventsApi->create_event: %s\n" % e)
```



### Parameters


Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **event_create** | [**EventCreate**](EventCreate.md)|  | 
 **idempotency_key** | **str**| Optional dedup key; requests with the same key within 24h return the original response. | [optional] 

### Return type

[**Event**](Event.md)

### Authorization

[ApiKeyAuth](../README.md#ApiKeyAuth)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json, application/problem+json

### HTTP response details

| Status code | Description | Response headers |
|-------------|-------------|------------------|
**202** | Event accepted for fan-out. |  -  |
**400** | Validation error. |  -  |
**401** | Missing or invalid API key. |  -  |
**409** | Idempotency key already used with a different payload. |  -  |
**429** | Workspace rate limit exceeded. |  * X-RateLimit-Limit -  <br>  * X-RateLimit-Remaining -  <br>  * X-RateLimit-Reset -  <br>  |

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **get_event**
> Event get_event(id)

Retrieve an event

Returns the event and a summary of its deliveries across all matching subscriptions.

### Example

* Bearer (API Key) Authentication (ApiKeyAuth):

```python
import webhook_relay_client
from webhook_relay_client.models.event import Event
from webhook_relay_client.rest import ApiException
from pprint import pprint

# Defining the host is optional and defaults to https://api.webhook-relay.dcsuniverse.com
# See configuration.py for a list of all supported configuration parameters.
configuration = webhook_relay_client.Configuration(
    host = "https://api.webhook-relay.dcsuniverse.com"
)

# The client must configure the authentication and authorization parameters
# in accordance with the API server security policy.
# Examples for each auth method are provided below, use the example that
# satisfies your auth use case.

# Configure Bearer authorization (API Key): ApiKeyAuth
configuration = webhook_relay_client.Configuration(
    access_token = os.environ["BEARER_TOKEN"]
)

# Enter a context with an instance of the API client
with webhook_relay_client.ApiClient(configuration) as api_client:
    # Create an instance of the API class
    api_instance = webhook_relay_client.EventsApi(api_client)
    id = 'id_example' # str | ULID of the event.

    try:
        # Retrieve an event
        api_response = api_instance.get_event(id)
        print("The response of EventsApi->get_event:\n")
        pprint(api_response)
    except Exception as e:
        print("Exception when calling EventsApi->get_event: %s\n" % e)
```



### Parameters


Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **id** | **str**| ULID of the event. | 

### Return type

[**Event**](Event.md)

### Authorization

[ApiKeyAuth](../README.md#ApiKeyAuth)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json, application/problem+json

### HTTP response details

| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | The event with a delivery summary. |  -  |
**401** | Missing or invalid API key. |  -  |
**404** | Resource not found in this workspace. |  -  |

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **healthz**
> Healthz200Response healthz()

Liveness check

Returns 200 if the API is up. Does not require auth.

### Example


```python
import webhook_relay_client
from webhook_relay_client.models.healthz200_response import Healthz200Response
from webhook_relay_client.rest import ApiException
from pprint import pprint

# Defining the host is optional and defaults to https://api.webhook-relay.dcsuniverse.com
# See configuration.py for a list of all supported configuration parameters.
configuration = webhook_relay_client.Configuration(
    host = "https://api.webhook-relay.dcsuniverse.com"
)


# Enter a context with an instance of the API client
with webhook_relay_client.ApiClient(configuration) as api_client:
    # Create an instance of the API class
    api_instance = webhook_relay_client.EventsApi(api_client)

    try:
        # Liveness check
        api_response = api_instance.healthz()
        print("The response of EventsApi->healthz:\n")
        pprint(api_response)
    except Exception as e:
        print("Exception when calling EventsApi->healthz: %s\n" % e)
```



### Parameters

This endpoint does not need any parameter.

### Return type

[**Healthz200Response**](Healthz200Response.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

### HTTP response details

| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | Service is up. |  -  |

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **list_events**
> EventPage list_events(type=type, created_after=created_after, cursor=cursor, limit=limit)

List events

Returns a cursor-paginated page of events, newest first, scoped to the caller's workspace.

### Example

* Bearer (API Key) Authentication (ApiKeyAuth):

```python
import webhook_relay_client
from webhook_relay_client.models.event_page import EventPage
from webhook_relay_client.rest import ApiException
from pprint import pprint

# Defining the host is optional and defaults to https://api.webhook-relay.dcsuniverse.com
# See configuration.py for a list of all supported configuration parameters.
configuration = webhook_relay_client.Configuration(
    host = "https://api.webhook-relay.dcsuniverse.com"
)

# The client must configure the authentication and authorization parameters
# in accordance with the API server security policy.
# Examples for each auth method are provided below, use the example that
# satisfies your auth use case.

# Configure Bearer authorization (API Key): ApiKeyAuth
configuration = webhook_relay_client.Configuration(
    access_token = os.environ["BEARER_TOKEN"]
)

# Enter a context with an instance of the API client
with webhook_relay_client.ApiClient(configuration) as api_client:
    # Create an instance of the API class
    api_instance = webhook_relay_client.EventsApi(api_client)
    type = 'type_example' # str | Filter by event type (exact match). (optional)
    created_after = '2013-10-20T19:20:30+01:00' # datetime |  (optional)
    cursor = 'cursor_example' # str | Opaque cursor from a prior page's `next_cursor`. (optional)
    limit = 25 # int |  (optional) (default to 25)

    try:
        # List events
        api_response = api_instance.list_events(type=type, created_after=created_after, cursor=cursor, limit=limit)
        print("The response of EventsApi->list_events:\n")
        pprint(api_response)
    except Exception as e:
        print("Exception when calling EventsApi->list_events: %s\n" % e)
```



### Parameters


Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **type** | **str**| Filter by event type (exact match). | [optional] 
 **created_after** | **datetime**|  | [optional] 
 **cursor** | **str**| Opaque cursor from a prior page&#39;s &#x60;next_cursor&#x60;. | [optional] 
 **limit** | **int**|  | [optional] [default to 25]

### Return type

[**EventPage**](EventPage.md)

### Authorization

[ApiKeyAuth](../README.md#ApiKeyAuth)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json, application/problem+json

### HTTP response details

| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | Page of events, newest first. |  -  |
**401** | Missing or invalid API key. |  -  |

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

