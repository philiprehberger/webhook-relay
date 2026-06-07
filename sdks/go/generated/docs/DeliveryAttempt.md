# DeliveryAttempt

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**AttemptNumber** | **int32** |  | 
**RequestSignature** | **string** | Format: t&#x3D;{unix_ts},v1&#x3D;{hex_hmac_sha256} | 
**ResponseStatus** | Pointer to **int32** |  | [optional] 
**ResponseHeaders** | Pointer to **map[string]string** |  | [optional] 
**ResponseBodySnippet** | Pointer to **string** | First 4 KB of the response body, UTF-8 best-effort. | [optional] 
**LatencyMs** | Pointer to **int32** |  | [optional] 
**ErrorCode** | Pointer to **string** |  | [optional] 
**AttemptedAt** | **time.Time** |  | 

## Methods

### NewDeliveryAttempt

`func NewDeliveryAttempt(attemptNumber int32, requestSignature string, attemptedAt time.Time, ) *DeliveryAttempt`

NewDeliveryAttempt instantiates a new DeliveryAttempt object
This constructor will assign default values to properties that have it defined,
and makes sure properties required by API are set, but the set of arguments
will change when the set of required properties is changed

### NewDeliveryAttemptWithDefaults

`func NewDeliveryAttemptWithDefaults() *DeliveryAttempt`

NewDeliveryAttemptWithDefaults instantiates a new DeliveryAttempt object
This constructor will only assign default values to properties that have it defined,
but it doesn't guarantee that properties required by API are set

### GetAttemptNumber

`func (o *DeliveryAttempt) GetAttemptNumber() int32`

GetAttemptNumber returns the AttemptNumber field if non-nil, zero value otherwise.

### GetAttemptNumberOk

`func (o *DeliveryAttempt) GetAttemptNumberOk() (*int32, bool)`

GetAttemptNumberOk returns a tuple with the AttemptNumber field if it's non-nil, zero value otherwise
and a boolean to check if the value has been set.

### SetAttemptNumber

`func (o *DeliveryAttempt) SetAttemptNumber(v int32)`

SetAttemptNumber sets AttemptNumber field to given value.


### GetRequestSignature

`func (o *DeliveryAttempt) GetRequestSignature() string`

GetRequestSignature returns the RequestSignature field if non-nil, zero value otherwise.

### GetRequestSignatureOk

`func (o *DeliveryAttempt) GetRequestSignatureOk() (*string, bool)`

GetRequestSignatureOk returns a tuple with the RequestSignature field if it's non-nil, zero value otherwise
and a boolean to check if the value has been set.

### SetRequestSignature

`func (o *DeliveryAttempt) SetRequestSignature(v string)`

SetRequestSignature sets RequestSignature field to given value.


### GetResponseStatus

`func (o *DeliveryAttempt) GetResponseStatus() int32`

GetResponseStatus returns the ResponseStatus field if non-nil, zero value otherwise.

### GetResponseStatusOk

`func (o *DeliveryAttempt) GetResponseStatusOk() (*int32, bool)`

GetResponseStatusOk returns a tuple with the ResponseStatus field if it's non-nil, zero value otherwise
and a boolean to check if the value has been set.

### SetResponseStatus

`func (o *DeliveryAttempt) SetResponseStatus(v int32)`

SetResponseStatus sets ResponseStatus field to given value.

### HasResponseStatus

`func (o *DeliveryAttempt) HasResponseStatus() bool`

HasResponseStatus returns a boolean if a field has been set.

### GetResponseHeaders

`func (o *DeliveryAttempt) GetResponseHeaders() map[string]string`

GetResponseHeaders returns the ResponseHeaders field if non-nil, zero value otherwise.

### GetResponseHeadersOk

`func (o *DeliveryAttempt) GetResponseHeadersOk() (*map[string]string, bool)`

GetResponseHeadersOk returns a tuple with the ResponseHeaders field if it's non-nil, zero value otherwise
and a boolean to check if the value has been set.

### SetResponseHeaders

`func (o *DeliveryAttempt) SetResponseHeaders(v map[string]string)`

SetResponseHeaders sets ResponseHeaders field to given value.

### HasResponseHeaders

`func (o *DeliveryAttempt) HasResponseHeaders() bool`

HasResponseHeaders returns a boolean if a field has been set.

### GetResponseBodySnippet

`func (o *DeliveryAttempt) GetResponseBodySnippet() string`

GetResponseBodySnippet returns the ResponseBodySnippet field if non-nil, zero value otherwise.

### GetResponseBodySnippetOk

`func (o *DeliveryAttempt) GetResponseBodySnippetOk() (*string, bool)`

GetResponseBodySnippetOk returns a tuple with the ResponseBodySnippet field if it's non-nil, zero value otherwise
and a boolean to check if the value has been set.

### SetResponseBodySnippet

`func (o *DeliveryAttempt) SetResponseBodySnippet(v string)`

SetResponseBodySnippet sets ResponseBodySnippet field to given value.

### HasResponseBodySnippet

`func (o *DeliveryAttempt) HasResponseBodySnippet() bool`

HasResponseBodySnippet returns a boolean if a field has been set.

### GetLatencyMs

`func (o *DeliveryAttempt) GetLatencyMs() int32`

GetLatencyMs returns the LatencyMs field if non-nil, zero value otherwise.

### GetLatencyMsOk

`func (o *DeliveryAttempt) GetLatencyMsOk() (*int32, bool)`

GetLatencyMsOk returns a tuple with the LatencyMs field if it's non-nil, zero value otherwise
and a boolean to check if the value has been set.

### SetLatencyMs

`func (o *DeliveryAttempt) SetLatencyMs(v int32)`

SetLatencyMs sets LatencyMs field to given value.

### HasLatencyMs

`func (o *DeliveryAttempt) HasLatencyMs() bool`

HasLatencyMs returns a boolean if a field has been set.

### GetErrorCode

`func (o *DeliveryAttempt) GetErrorCode() string`

GetErrorCode returns the ErrorCode field if non-nil, zero value otherwise.

### GetErrorCodeOk

`func (o *DeliveryAttempt) GetErrorCodeOk() (*string, bool)`

GetErrorCodeOk returns a tuple with the ErrorCode field if it's non-nil, zero value otherwise
and a boolean to check if the value has been set.

### SetErrorCode

`func (o *DeliveryAttempt) SetErrorCode(v string)`

SetErrorCode sets ErrorCode field to given value.

### HasErrorCode

`func (o *DeliveryAttempt) HasErrorCode() bool`

HasErrorCode returns a boolean if a field has been set.

### GetAttemptedAt

`func (o *DeliveryAttempt) GetAttemptedAt() time.Time`

GetAttemptedAt returns the AttemptedAt field if non-nil, zero value otherwise.

### GetAttemptedAtOk

`func (o *DeliveryAttempt) GetAttemptedAtOk() (*time.Time, bool)`

GetAttemptedAtOk returns a tuple with the AttemptedAt field if it's non-nil, zero value otherwise
and a boolean to check if the value has been set.

### SetAttemptedAt

`func (o *DeliveryAttempt) SetAttemptedAt(v time.Time)`

SetAttemptedAt sets AttemptedAt field to given value.



[[Back to Model list]](../README.md#documentation-for-models) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to README]](../README.md)


