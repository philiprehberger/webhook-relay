# webhook_relay_client.DeliveriesApi

All URIs are relative to *https://api.webhook-relay.dcsuniverse.com*

Method | HTTP request | Description
------------- | ------------- | -------------
[**get_delivery**](DeliveriesApi.md#get_delivery) | **GET** /v1/deliveries/{id} | Retrieve a delivery
[**list_deliveries**](DeliveriesApi.md#list_deliveries) | **GET** /v1/deliveries | List deliveries
[**retry_delivery**](DeliveriesApi.md#retry_delivery) | **POST** /v1/deliveries/{id}/retry | Manually retry a delivery


# **get_delivery**
> Delivery get_delivery(id)

Retrieve a delivery

Returns the delivery with its full attempt timeline.

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
    api_instance = webhook_relay_client.DeliveriesApi(api_client)
    id = 'id_example' # str | 

    try:
        # Retrieve a delivery
        api_response = api_instance.get_delivery(id)
        print("The response of DeliveriesApi->get_delivery:\n")
        pprint(api_response)
    except Exception as e:
        print("Exception when calling DeliveriesApi->get_delivery: %s\n" % e)
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
**200** | The delivery with attempts. |  -  |
**401** | Missing or invalid API key. |  -  |
**404** | Resource not found in this workspace. |  -  |

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **list_deliveries**
> DeliveryPage list_deliveries(event_id=event_id, subscription_id=subscription_id, status=status, cursor=cursor, limit=limit)

List deliveries

Cursor-paginated, newest first, scoped to the caller's workspace.

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
    api_instance = webhook_relay_client.DeliveriesApi(api_client)
    event_id = 'event_id_example' # str |  (optional)
    subscription_id = 'subscription_id_example' # str |  (optional)
    status = 'status_example' # str |  (optional)
    cursor = 'cursor_example' # str |  (optional)
    limit = 25 # int |  (optional) (default to 25)

    try:
        # List deliveries
        api_response = api_instance.list_deliveries(event_id=event_id, subscription_id=subscription_id, status=status, cursor=cursor, limit=limit)
        print("The response of DeliveriesApi->list_deliveries:\n")
        pprint(api_response)
    except Exception as e:
        print("Exception when calling DeliveriesApi->list_deliveries: %s\n" % e)
```



### Parameters


Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **event_id** | **str**|  | [optional] 
 **subscription_id** | **str**|  | [optional] 
 **status** | **str**|  | [optional] 
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
**200** | Page of deliveries. |  -  |
**401** | Missing or invalid API key. |  -  |

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **retry_delivery**
> Delivery retry_delivery(id)

Manually retry a delivery

Sets the delivery to `pending` and enqueues a fresh delivery attempt
immediately. Works regardless of current status. Does not reset
`attempts_made` — the new attempt is recorded as the next one in
the timeline.


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
    api_instance = webhook_relay_client.DeliveriesApi(api_client)
    id = 'id_example' # str | 

    try:
        # Manually retry a delivery
        api_response = api_instance.retry_delivery(id)
        print("The response of DeliveriesApi->retry_delivery:\n")
        pprint(api_response)
    except Exception as e:
        print("Exception when calling DeliveriesApi->retry_delivery: %s\n" % e)
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
**200** | Updated delivery with attempts. |  -  |
**401** | Missing or invalid API key. |  -  |
**404** | Resource not found in this workspace. |  -  |

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

