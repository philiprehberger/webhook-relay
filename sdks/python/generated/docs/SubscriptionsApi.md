# webhook_relay_client.SubscriptionsApi

All URIs are relative to *https://api.webhook-relay.dcsuniverse.com*

Method | HTTP request | Description
------------- | ------------- | -------------
[**create_subscription**](SubscriptionsApi.md#create_subscription) | **POST** /v1/subscriptions | Create a subscription
[**delete_subscription**](SubscriptionsApi.md#delete_subscription) | **DELETE** /v1/subscriptions/{id} | Delete a subscription
[**get_subscription**](SubscriptionsApi.md#get_subscription) | **GET** /v1/subscriptions/{id} | Retrieve a subscription
[**list_subscriptions**](SubscriptionsApi.md#list_subscriptions) | **GET** /v1/subscriptions | List subscriptions
[**pause_subscription**](SubscriptionsApi.md#pause_subscription) | **POST** /v1/subscriptions/{id}/pause | Pause a subscription
[**resume_subscription**](SubscriptionsApi.md#resume_subscription) | **POST** /v1/subscriptions/{id}/resume | Resume a paused subscription
[**rotate_subscription_secret**](SubscriptionsApi.md#rotate_subscription_secret) | **POST** /v1/subscriptions/{id}/rotate-secret | Rotate the signing secret
[**update_subscription**](SubscriptionsApi.md#update_subscription) | **PATCH** /v1/subscriptions/{id} | Update a subscription


# **create_subscription**
> SubscriptionWithSecret create_subscription(subscription_create)

Create a subscription

Registers a new endpoint to receive matching events. The signing
secret is generated server-side and returned ONCE in the response —
store it now; you cannot retrieve it later (only rotate it).


### Example

* Bearer (API Key) Authentication (ApiKeyAuth):

```python
import webhook_relay_client
from webhook_relay_client.models.subscription_create import SubscriptionCreate
from webhook_relay_client.models.subscription_with_secret import SubscriptionWithSecret
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
    api_instance = webhook_relay_client.SubscriptionsApi(api_client)
    subscription_create = webhook_relay_client.SubscriptionCreate() # SubscriptionCreate | 

    try:
        # Create a subscription
        api_response = api_instance.create_subscription(subscription_create)
        print("The response of SubscriptionsApi->create_subscription:\n")
        pprint(api_response)
    except Exception as e:
        print("Exception when calling SubscriptionsApi->create_subscription: %s\n" % e)
```



### Parameters


Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **subscription_create** | [**SubscriptionCreate**](SubscriptionCreate.md)|  | 

### Return type

[**SubscriptionWithSecret**](SubscriptionWithSecret.md)

### Authorization

[ApiKeyAuth](../README.md#ApiKeyAuth)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json, application/problem+json

### HTTP response details

| Status code | Description | Response headers |
|-------------|-------------|------------------|
**201** | Subscription created. |  -  |
**400** | Validation error. |  -  |
**401** | Missing or invalid API key. |  -  |

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **delete_subscription**
> delete_subscription(id)

Delete a subscription

Permanently deletes the subscription. Deliveries are retained for audit.

### Example

* Bearer (API Key) Authentication (ApiKeyAuth):

```python
import webhook_relay_client
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
    api_instance = webhook_relay_client.SubscriptionsApi(api_client)
    id = 'id_example' # str | ULID of the subscription.

    try:
        # Delete a subscription
        api_instance.delete_subscription(id)
    except Exception as e:
        print("Exception when calling SubscriptionsApi->delete_subscription: %s\n" % e)
```



### Parameters


Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **id** | **str**| ULID of the subscription. | 

### Return type

void (empty response body)

### Authorization

[ApiKeyAuth](../README.md#ApiKeyAuth)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/problem+json

### HTTP response details

| Status code | Description | Response headers |
|-------------|-------------|------------------|
**204** | Deleted. |  -  |
**401** | Missing or invalid API key. |  -  |
**404** | Resource not found in this workspace. |  -  |

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **get_subscription**
> Subscription get_subscription(id)

Retrieve a subscription

Returns a single subscription without its signing secret.

### Example

* Bearer (API Key) Authentication (ApiKeyAuth):

```python
import webhook_relay_client
from webhook_relay_client.models.subscription import Subscription
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
    api_instance = webhook_relay_client.SubscriptionsApi(api_client)
    id = 'id_example' # str | ULID of the subscription.

    try:
        # Retrieve a subscription
        api_response = api_instance.get_subscription(id)
        print("The response of SubscriptionsApi->get_subscription:\n")
        pprint(api_response)
    except Exception as e:
        print("Exception when calling SubscriptionsApi->get_subscription: %s\n" % e)
```



### Parameters


Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **id** | **str**| ULID of the subscription. | 

### Return type

[**Subscription**](Subscription.md)

### Authorization

[ApiKeyAuth](../README.md#ApiKeyAuth)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json, application/problem+json

### HTTP response details

| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | The subscription. |  -  |
**401** | Missing or invalid API key. |  -  |
**404** | Resource not found in this workspace. |  -  |

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **list_subscriptions**
> SubscriptionPage list_subscriptions(cursor=cursor, limit=limit, state=state)

List subscriptions

Cursor-paginated, scoped to the caller's workspace.

### Example

* Bearer (API Key) Authentication (ApiKeyAuth):

```python
import webhook_relay_client
from webhook_relay_client.models.subscription_page import SubscriptionPage
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
    api_instance = webhook_relay_client.SubscriptionsApi(api_client)
    cursor = 'cursor_example' # str |  (optional)
    limit = 25 # int |  (optional) (default to 25)
    state = 'state_example' # str |  (optional)

    try:
        # List subscriptions
        api_response = api_instance.list_subscriptions(cursor=cursor, limit=limit, state=state)
        print("The response of SubscriptionsApi->list_subscriptions:\n")
        pprint(api_response)
    except Exception as e:
        print("Exception when calling SubscriptionsApi->list_subscriptions: %s\n" % e)
```



### Parameters


Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **cursor** | **str**|  | [optional] 
 **limit** | **int**|  | [optional] [default to 25]
 **state** | **str**|  | [optional] 

### Return type

[**SubscriptionPage**](SubscriptionPage.md)

### Authorization

[ApiKeyAuth](../README.md#ApiKeyAuth)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json, application/problem+json

### HTTP response details

| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | Page of subscriptions. |  -  |
**401** | Missing or invalid API key. |  -  |

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **pause_subscription**
> Subscription pause_subscription(id)

Pause a subscription

Stops new deliveries until resumed. Existing in-flight deliveries are not cancelled.

### Example

* Bearer (API Key) Authentication (ApiKeyAuth):

```python
import webhook_relay_client
from webhook_relay_client.models.subscription import Subscription
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
    api_instance = webhook_relay_client.SubscriptionsApi(api_client)
    id = 'id_example' # str | ULID of the subscription.

    try:
        # Pause a subscription
        api_response = api_instance.pause_subscription(id)
        print("The response of SubscriptionsApi->pause_subscription:\n")
        pprint(api_response)
    except Exception as e:
        print("Exception when calling SubscriptionsApi->pause_subscription: %s\n" % e)
```



### Parameters


Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **id** | **str**| ULID of the subscription. | 

### Return type

[**Subscription**](Subscription.md)

### Authorization

[ApiKeyAuth](../README.md#ApiKeyAuth)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json, application/problem+json

### HTTP response details

| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | Paused. |  -  |
**401** | Missing or invalid API key. |  -  |
**404** | Resource not found in this workspace. |  -  |

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **resume_subscription**
> Subscription resume_subscription(id)

Resume a paused subscription

Resets consecutive_failures to 0 and returns the subscription to active state.

### Example

* Bearer (API Key) Authentication (ApiKeyAuth):

```python
import webhook_relay_client
from webhook_relay_client.models.subscription import Subscription
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
    api_instance = webhook_relay_client.SubscriptionsApi(api_client)
    id = 'id_example' # str | ULID of the subscription.

    try:
        # Resume a paused subscription
        api_response = api_instance.resume_subscription(id)
        print("The response of SubscriptionsApi->resume_subscription:\n")
        pprint(api_response)
    except Exception as e:
        print("Exception when calling SubscriptionsApi->resume_subscription: %s\n" % e)
```



### Parameters


Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **id** | **str**| ULID of the subscription. | 

### Return type

[**Subscription**](Subscription.md)

### Authorization

[ApiKeyAuth](../README.md#ApiKeyAuth)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json, application/problem+json

### HTTP response details

| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | Resumed. |  -  |
**401** | Missing or invalid API key. |  -  |
**404** | Resource not found in this workspace. |  -  |

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **rotate_subscription_secret**
> SubscriptionWithSecret rotate_subscription_secret(id)

Rotate the signing secret

Generates a new secret and returns it once. The previous secret
remains valid for a 48-hour grace window so receivers can update
their verifier without losing requests.


### Example

* Bearer (API Key) Authentication (ApiKeyAuth):

```python
import webhook_relay_client
from webhook_relay_client.models.subscription_with_secret import SubscriptionWithSecret
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
    api_instance = webhook_relay_client.SubscriptionsApi(api_client)
    id = 'id_example' # str | ULID of the subscription.

    try:
        # Rotate the signing secret
        api_response = api_instance.rotate_subscription_secret(id)
        print("The response of SubscriptionsApi->rotate_subscription_secret:\n")
        pprint(api_response)
    except Exception as e:
        print("Exception when calling SubscriptionsApi->rotate_subscription_secret: %s\n" % e)
```



### Parameters


Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **id** | **str**| ULID of the subscription. | 

### Return type

[**SubscriptionWithSecret**](SubscriptionWithSecret.md)

### Authorization

[ApiKeyAuth](../README.md#ApiKeyAuth)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json, application/problem+json

### HTTP response details

| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | Rotated. |  -  |
**401** | Missing or invalid API key. |  -  |
**404** | Resource not found in this workspace. |  -  |

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **update_subscription**
> Subscription update_subscription(id, subscription_update)

Update a subscription

Partial update. Mutable fields are name, url, event_filter.

### Example

* Bearer (API Key) Authentication (ApiKeyAuth):

```python
import webhook_relay_client
from webhook_relay_client.models.subscription import Subscription
from webhook_relay_client.models.subscription_update import SubscriptionUpdate
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
    api_instance = webhook_relay_client.SubscriptionsApi(api_client)
    id = 'id_example' # str | ULID of the subscription.
    subscription_update = webhook_relay_client.SubscriptionUpdate() # SubscriptionUpdate | 

    try:
        # Update a subscription
        api_response = api_instance.update_subscription(id, subscription_update)
        print("The response of SubscriptionsApi->update_subscription:\n")
        pprint(api_response)
    except Exception as e:
        print("Exception when calling SubscriptionsApi->update_subscription: %s\n" % e)
```



### Parameters


Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **id** | **str**| ULID of the subscription. | 
 **subscription_update** | [**SubscriptionUpdate**](SubscriptionUpdate.md)|  | 

### Return type

[**Subscription**](Subscription.md)

### Authorization

[ApiKeyAuth](../README.md#ApiKeyAuth)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json, application/problem+json

### HTTP response details

| Status code | Description | Response headers |
|-------------|-------------|------------------|
**200** | Updated subscription. |  -  |
**400** | Validation error. |  -  |
**401** | Missing or invalid API key. |  -  |
**404** | Resource not found in this workspace. |  -  |

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

