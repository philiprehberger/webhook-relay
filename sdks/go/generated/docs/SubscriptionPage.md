# SubscriptionPage

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**Data** | [**[]Subscription**](Subscription.md) |  | 
**NextCursor** | **string** |  | 

## Methods

### NewSubscriptionPage

`func NewSubscriptionPage(data []Subscription, nextCursor string, ) *SubscriptionPage`

NewSubscriptionPage instantiates a new SubscriptionPage object
This constructor will assign default values to properties that have it defined,
and makes sure properties required by API are set, but the set of arguments
will change when the set of required properties is changed

### NewSubscriptionPageWithDefaults

`func NewSubscriptionPageWithDefaults() *SubscriptionPage`

NewSubscriptionPageWithDefaults instantiates a new SubscriptionPage object
This constructor will only assign default values to properties that have it defined,
but it doesn't guarantee that properties required by API are set

### GetData

`func (o *SubscriptionPage) GetData() []Subscription`

GetData returns the Data field if non-nil, zero value otherwise.

### GetDataOk

`func (o *SubscriptionPage) GetDataOk() (*[]Subscription, bool)`

GetDataOk returns a tuple with the Data field if it's non-nil, zero value otherwise
and a boolean to check if the value has been set.

### SetData

`func (o *SubscriptionPage) SetData(v []Subscription)`

SetData sets Data field to given value.


### GetNextCursor

`func (o *SubscriptionPage) GetNextCursor() string`

GetNextCursor returns the NextCursor field if non-nil, zero value otherwise.

### GetNextCursorOk

`func (o *SubscriptionPage) GetNextCursorOk() (*string, bool)`

GetNextCursorOk returns a tuple with the NextCursor field if it's non-nil, zero value otherwise
and a boolean to check if the value has been set.

### SetNextCursor

`func (o *SubscriptionPage) SetNextCursor(v string)`

SetNextCursor sets NextCursor field to given value.



[[Back to Model list]](../README.md#documentation-for-models) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to README]](../README.md)


