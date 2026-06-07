# \DeadLettersAPI

All URIs are relative to *https://api.webhook-relay.dcsuniverse.com*

Method | HTTP request | Description
------------- | ------------- | -------------
[**ListDeadLetters**](DeadLettersAPI.md#ListDeadLetters) | **Get** /v1/dead-letters | List dead-lettered deliveries
[**ReplayDeadLetter**](DeadLettersAPI.md#ReplayDeadLetter) | **Post** /v1/dead-letters/{id}/replay | Replay a dead-lettered delivery



## ListDeadLetters

> DeliveryPage ListDeadLetters(ctx).SubscriptionId(subscriptionId).Since(since).Cursor(cursor).Limit(limit).Execute()

List dead-lettered deliveries



### Example

```go
package main

import (
	"context"
	"fmt"
	"os"
    "time"
	openapiclient "github.com/GIT_USER_ID/GIT_REPO_ID/webhookrelay"
)

func main() {
	subscriptionId := "subscriptionId_example" // string |  (optional)
	since := time.Now() // time.Time |  (optional)
	cursor := "cursor_example" // string |  (optional)
	limit := int32(56) // int32 |  (optional) (default to 25)

	configuration := openapiclient.NewConfiguration()
	apiClient := openapiclient.NewAPIClient(configuration)
	resp, r, err := apiClient.DeadLettersAPI.ListDeadLetters(context.Background()).SubscriptionId(subscriptionId).Since(since).Cursor(cursor).Limit(limit).Execute()
	if err != nil {
		fmt.Fprintf(os.Stderr, "Error when calling `DeadLettersAPI.ListDeadLetters``: %v\n", err)
		fmt.Fprintf(os.Stderr, "Full HTTP response: %v\n", r)
	}
	// response from `ListDeadLetters`: DeliveryPage
	fmt.Fprintf(os.Stdout, "Response from `DeadLettersAPI.ListDeadLetters`: %v\n", resp)
}
```

### Path Parameters



### Other Parameters

Other parameters are passed through a pointer to a apiListDeadLettersRequest struct via the builder pattern


Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **subscriptionId** | **string** |  | 
 **since** | **time.Time** |  | 
 **cursor** | **string** |  | 
 **limit** | **int32** |  | [default to 25]

### Return type

[**DeliveryPage**](DeliveryPage.md)

### Authorization

[ApiKeyAuth](../README.md#ApiKeyAuth)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: application/json, application/problem+json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints)
[[Back to Model list]](../README.md#documentation-for-models)
[[Back to README]](../README.md)


## ReplayDeadLetter

> Delivery ReplayDeadLetter(ctx, id).Execute()

Replay a dead-lettered delivery



### Example

```go
package main

import (
	"context"
	"fmt"
	"os"
	openapiclient "github.com/GIT_USER_ID/GIT_REPO_ID/webhookrelay"
)

func main() {
	id := "id_example" // string | 

	configuration := openapiclient.NewConfiguration()
	apiClient := openapiclient.NewAPIClient(configuration)
	resp, r, err := apiClient.DeadLettersAPI.ReplayDeadLetter(context.Background(), id).Execute()
	if err != nil {
		fmt.Fprintf(os.Stderr, "Error when calling `DeadLettersAPI.ReplayDeadLetter``: %v\n", err)
		fmt.Fprintf(os.Stderr, "Full HTTP response: %v\n", r)
	}
	// response from `ReplayDeadLetter`: Delivery
	fmt.Fprintf(os.Stdout, "Response from `DeadLettersAPI.ReplayDeadLetter`: %v\n", resp)
}
```

### Path Parameters


Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
**ctx** | **context.Context** | context for authentication, logging, cancellation, deadlines, tracing, etc.
**id** | **string** |  | 

### Other Parameters

Other parameters are passed through a pointer to a apiReplayDeadLetterRequest struct via the builder pattern


Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------


### Return type

[**Delivery**](Delivery.md)

### Authorization

[ApiKeyAuth](../README.md#ApiKeyAuth)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: application/json, application/problem+json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints)
[[Back to Model list]](../README.md#documentation-for-models)
[[Back to README]](../README.md)

