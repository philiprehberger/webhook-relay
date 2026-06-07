# SubscriptionCreate

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**Name** | Pointer to **string** |  | [optional] 
**Url** | **string** | HTTPS endpoint to receive deliveries. | 
**EventFilter** | Pointer to **string** | \&quot;*\&quot; matches all events, otherwise an exact type or a glob like \&quot;order.*\&quot;. Glob uses \&quot;*\&quot; as a wildcard segment.  | [optional] [default to "*"]

## Methods

### NewSubscriptionCreate

`func NewSubscriptionCreate(url string, ) *SubscriptionCreate`

NewSubscriptionCreate instantiates a new SubscriptionCreate object
This constructor will assign default values to properties that have it defined,
and makes sure properties required by API are set, but the set of arguments
will change when the set of required properties is changed

### NewSubscriptionCreateWithDefaults

`func NewSubscriptionCreateWithDefaults() *SubscriptionCreate`

NewSubscriptionCreateWithDefaults instantiates a new SubscriptionCreate object
This constructor will only assign default values to properties that have it defined,
but it doesn't guarantee that properties required by API are set

### GetName

`func (o *SubscriptionCreate) GetName() string`

GetName returns the Name field if non-nil, zero value otherwise.

### GetNameOk

`func (o *SubscriptionCreate) GetNameOk() (*string, bool)`

GetNameOk returns a tuple with the Name field if it's non-nil, zero value otherwise
and a boolean to check if the value has been set.

### SetName

`func (o *SubscriptionCreate) SetName(v string)`

SetName sets Name field to given value.

### HasName

`func (o *SubscriptionCreate) HasName() bool`

HasName returns a boolean if a field has been set.

### GetUrl

`func (o *SubscriptionCreate) GetUrl() string`

GetUrl returns the Url field if non-nil, zero value otherwise.

### GetUrlOk

`func (o *SubscriptionCreate) GetUrlOk() (*string, bool)`

GetUrlOk returns a tuple with the Url field if it's non-nil, zero value otherwise
and a boolean to check if the value has been set.

### SetUrl

`func (o *SubscriptionCreate) SetUrl(v string)`

SetUrl sets Url field to given value.


### GetEventFilter

`func (o *SubscriptionCreate) GetEventFilter() string`

GetEventFilter returns the EventFilter field if non-nil, zero value otherwise.

### GetEventFilterOk

`func (o *SubscriptionCreate) GetEventFilterOk() (*string, bool)`

GetEventFilterOk returns a tuple with the EventFilter field if it's non-nil, zero value otherwise
and a boolean to check if the value has been set.

### SetEventFilter

`func (o *SubscriptionCreate) SetEventFilter(v string)`

SetEventFilter sets EventFilter field to given value.

### HasEventFilter

`func (o *SubscriptionCreate) HasEventFilter() bool`

HasEventFilter returns a boolean if a field has been set.


[[Back to Model list]](../README.md#documentation-for-models) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to README]](../README.md)


