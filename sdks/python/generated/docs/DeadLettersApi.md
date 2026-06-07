# webhook_relay_client.DeadLettersApi

All URIs are relative to *https://api.webhook-relay.dcsuniverse.com*

Method | HTTP request | Description
------------- | ------------- | -------------
[**list_dead_letters**](DeadLettersApi.md#list_dead_letters) | **GET** /v1/dead-letters | List dead-lettered deliveries
[**replay_dead_letter**](DeadLettersApi.md#replay_dead_letter) | **POST** /v1/dead-letters/{id}/replay | Replay a dead-lettered delivery


# **list_dead_letters**
> DeliveryPage list_dead_letters(subscription_id=subscription_id, since=since, cursor=cursor, limit=limit)

List dead-lettered deliveries

Returns deliveries with `status=dead` — the human-attention queue.
A delivery lands here when:
  - The subscriber returned 4xx (not retried).
  - Retries exhausted on 5xx / timeout / connection errors.
  - The subscription was paused / disabled before the attempt.
  - The SSRF guard blocked the URL.


### Example

* Bearer (API Key) Authentication (ApiKeyAuth):

```python
import webhook_relay_client
from webhook_relay_client.models.delivery_page import DeliveryPage
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
    api_instance = webhook_relay_client.DeadLettersApi(api_client)
    subscription_id = 'subscription_id_example' # str |  (optional)
    since = '2013-10-20T19:20:30+01:00' # datetime |  (optional)
    cursor = 'cursor_example' # str |  (optional)
    limit = 25 # int |  (optional) (default to 25)

    try:
        # List dead-lettered deliveries
        api_response = api_instance.list_dead_letters(subscription_id=subscription_id, since=since, cursor=cursor, limit=limit)
        print("The response of DeadLettersApi->list_dead_letters:\n")
        pprint(api_response)
    except Exception as e:
        print("Exception when calling DeadLettersApi->list_dead_letters: %s\n" % e)
```



### Parameters


Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **subscription_id** | **str**|  | [optional] 
 **since** | **datetime**|  | [optional] 
 **cursor** | **str**|  | [optional] 
 **limit** | **int**|  | [optional] [default to 25]

### Return type

[**DeliveryPage**](DeliveryPage.md)

### Authorization

[ApiKeyAuth](../README.md#ApiKeyAuth)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json, application/problem+json

### HTTP response details

| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | Page of dead-lettered deliveries. |  -  |
**401** | Missing or invalid API key. |  -  |

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **replay_dead_letter**
> Delivery replay_dead_letter(id)

Replay a dead-lettered delivery

Resurrects a `status=dead` delivery — sets it back to `pending` and
enqueues a fresh attempt. Returns 409 if the delivery is not in the
dead-letter state. To retry deliveries in other states use POST
/v1/deliveries/{id}/retry instead.


### Example

* Bearer (API Key) Authentication (ApiKeyAuth):

```python
import webhook_relay_client
from webhook_relay_client.models.delivery import Delivery
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
    api_instance = webhook_relay_client.DeadLettersApi(api_client)
    id = 'id_example' # str | 

    try:
        # Replay a dead-lettered delivery
        api_response = api_instance.replay_dead_letter(id)
        print("The response of DeadLettersApi->replay_dead_letter:\n")
        pprint(api_response)
    except Exception as e:
        print("Exception when calling DeadLettersApi->replay_dead_letter: %s\n" % e)
```



### Parameters


Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **id** | **str**|  | 

### Return type

[**Delivery**](Delivery.md)

### Authorization

[ApiKeyAuth](../README.md#ApiKeyAuth)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json, application/problem+json

### HTTP response details

| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | Replayed delivery (now pending) with attempts. |  -  |
**401** | Missing or invalid API key. |  -  |
**404** | Resource not found in this workspace. |  -  |
**409** | Delivery is not dead-lettered. |  -  |

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

