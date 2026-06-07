# Delivery

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**Id** | **string** |  | 
**EventId** | **string** |  | 
**SubscriptionId** | **string** |  | 
**Status** | **string** |  | 
**AttemptsMade** | **int32** |  | 
**NextAttemptAt** | Pointer to **time.Time** |  | [optional] 
**FinalStatusCode** | Pointer to **int32** |  | [optional] 
**CompletedAt** | Pointer to **time.Time** |  | [optional] 
**CreatedAt** | **time.Time** |  | 
**Attempts** | Pointer to [**[]DeliveryAttempt**](DeliveryAttempt.md) | Present on retrieve, omitted on list. | [optional] 

## Methods

### NewDelivery

`func NewDelivery(id string, eventId string, subscriptionId string, status string, attemptsMade int32, createdAt time.Time, ) *Delivery`

NewDelivery instantiates a new Delivery object
This constructor will assign default values to properties that have it defined,
and makes sure properties required by API are set, but the set of arguments
will change when the set of required properties is changed

### NewDeliveryWithDefaults

`func NewDeliveryWithDefaults() *Delivery`

NewDeliveryWithDefaults instantiates a new Delivery object
This constructor will only assign default values to properties that have it defined,
but it doesn't guarantee that properties required by API are set

### GetId

`func (o *Delivery) GetId() string`

GetId returns the Id field if non-nil, zero value otherwise.

### GetIdOk

`func (o *Delivery) GetIdOk() (*string, bool)`

GetIdOk returns a tuple with the Id field if it's non-nil, zero value otherwise
and a boolean to check if the value has been set.

### SetId

`func (o *Delivery) SetId(v string)`

SetId sets Id field to given value.


### GetEventId

`func (o *Delivery) GetEventId() string`

GetEventId returns the EventId field if non-nil, zero value otherwise.

### GetEventIdOk

`func (o *Delivery) GetEventIdOk() (*string, bool)`

GetEventIdOk returns a tuple with the EventId field if it's non-nil, zero value otherwise
and a boolean to check if the value has been set.

### SetEventId

`func (o *Delivery) SetEventId(v string)`

SetEventId sets EventId field to given value.


### GetSubscriptionId

`func (o *Delivery) GetSubscriptionId() string`

GetSubscriptionId returns the SubscriptionId field if non-nil, zero value otherwise.

### GetSubscriptionIdOk

`func (o *Delivery) GetSubscriptionIdOk() (*string, bool)`

GetSubscriptionIdOk returns a tuple with the SubscriptionId field if it's non-nil, zero value otherwise
and a boolean to check if the value has been set.

### SetSubscriptionId

`func (o *Delivery) SetSubscriptionId(v string)`

SetSubscriptionId sets SubscriptionId field to given value.


### GetStatus

`func (o *Delivery) GetStatus() string`

GetStatus returns the Status field if non-nil, zero value otherwise.

### GetStatusOk

`func (o *Delivery) GetStatusOk() (*string, bool)`

GetStatusOk returns a tuple with the Status field if it's non-nil, zero value otherwise
and a boolean to check if the value has been set.

### SetStatus

`func (o *Delivery) SetStatus(v string)`

SetStatus sets Status field to given value.


### GetAttemptsMade

`func (o *Delivery) GetAttemptsMade() int32`

GetAttemptsMade returns the AttemptsMade field if non-nil, zero value otherwise.

### GetAttemptsMadeOk

`func (o *Delivery) GetAttemptsMadeOk() (*int32, bool)`

GetAttemptsMadeOk returns a tuple with the AttemptsMade field if it's non-nil, zero value otherwise
and a boolean to check if the value has been set.

### SetAttemptsMade

`func (o *Delivery) SetAttemptsMade(v int32)`

SetAttemptsMade sets AttemptsMade field to given value.


### GetNextAttemptAt

`func (o *Delivery) GetNextAttemptAt() time.Time`

GetNextAttemptAt returns the NextAttemptAt field if non-nil, zero value otherwise.

### GetNextAttemptAtOk

`func (o *Delivery) GetNextAttemptAtOk() (*time.Time, bool)`

GetNextAttemptAtOk returns a tuple with the NextAttemptAt field if it's non-nil, zero value otherwise
and a boolean to check if the value has been set.

### SetNextAttemptAt

`func (o *Delivery) SetNextAttemptAt(v time.Time)`

SetNextAttemptAt sets NextAttemptAt field to given value.

### HasNextAttemptAt

`func (o *Delivery) HasNextAttemptAt() bool`

HasNextAttemptAt returns a boolean if a field has been set.

### GetFinalStatusCode

`func (o *Delivery) GetFinalStatusCode() int32`

GetFinalStatusCode returns the FinalStatusCode field if non-nil, zero value otherwise.

### GetFinalStatusCodeOk

`func (o *Delivery) GetFinalStatusCodeOk() (*int32, bool)`

GetFinalStatusCodeOk returns a tuple with the FinalStatusCode field if it's non-nil, zero value otherwise
and a boolean to check if the value has been set.

### SetFinalStatusCode

`func (o *Delivery) SetFinalStatusCode(v int32)`

SetFinalStatusCode sets FinalStatusCode field to given value.

### HasFinalStatusCode

`func (o *Delivery) HasFinalStatusCode() bool`

HasFinalStatusCode returns a boolean if a field has been set.

### GetCompletedAt

`func (o *Delivery) GetCompletedAt() time.Time`

GetCompletedAt returns the CompletedAt field if non-nil, zero value otherwise.

### GetCompletedAtOk

`func (o *Delivery) GetCompletedAtOk() (*time.Time, bool)`

GetCompletedAtOk returns a tuple with the CompletedAt field if it's non-nil, zero value otherwise
and a boolean to check if the value has been set.

### SetCompletedAt

`func (o *Delivery) SetCompletedAt(v time.Time)`

SetCompletedAt sets CompletedAt field to given value.

### HasCompletedAt

`func (o *Delivery) HasCompletedAt() bool`

HasCompletedAt returns a boolean if a field has been set.

### GetCreatedAt

`func (o *Delivery) GetCreatedAt() time.Time`

GetCreatedAt returns the CreatedAt field if non-nil, zero value otherwise.

### GetCreatedAtOk

`func (o *Delivery) GetCreatedAtOk() (*time.Time, bool)`

GetCreatedAtOk returns a tuple with the CreatedAt field if it's non-nil, zero value otherwise
and a boolean to check if the value has been set.

### SetCreatedAt

`func (o *Delivery) SetCreatedAt(v time.Time)`

SetCreatedAt sets CreatedAt field to given value.


### GetAttempts

`func (o *Delivery) GetAttempts() []DeliveryAttempt`

GetAttempts returns the Attempts field if non-nil, zero value otherwise.

### GetAttemptsOk

`func (o *Delivery) GetAttemptsOk() (*[]DeliveryAttempt, bool)`

GetAttemptsOk returns a tuple with the Attempts field if it's non-nil, zero value otherwise
and a boolean to check if the value has been set.

### SetAttempts

`func (o *Delivery) SetAttempts(v []DeliveryAttempt)`

SetAttempts sets Attempts field to given value.

### HasAttempts

`func (o *Delivery) HasAttempts() bool`

HasAttempts returns a boolean if a field has been set.


[[Back to Model list]](../README.md#documentation-for-models) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to README]](../README.md)


