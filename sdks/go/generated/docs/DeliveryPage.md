# DeliveryPage

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**Data** | [**[]Delivery**](Delivery.md) |  | 
**NextCursor** | **string** |  | 

## Methods

### NewDeliveryPage

`func NewDeliveryPage(data []Delivery, nextCursor string, ) *DeliveryPage`

NewDeliveryPage instantiates a new DeliveryPage object
This constructor will assign default values to properties that have it defined,
and makes sure properties required by API are set, but the set of arguments
will change when the set of required properties is changed

### NewDeliveryPageWithDefaults

`func NewDeliveryPageWithDefaults() *DeliveryPage`

NewDeliveryPageWithDefaults instantiates a new DeliveryPage object
This constructor will only assign default values to properties that have it defined,
but it doesn't guarantee that properties required by API are set

### GetData

`func (o *DeliveryPage) GetData() []Delivery`

GetData returns the Data field if non-nil, zero value otherwise.

### GetDataOk

`func (o *DeliveryPage) GetDataOk() (*[]Delivery, bool)`

GetDataOk returns a tuple with the Data field if it's non-nil, zero value otherwise
and a boolean to check if the value has been set.

### SetData

`func (o *DeliveryPage) SetData(v []Delivery)`

SetData sets Data field to given value.


### GetNextCursor

`func (o *DeliveryPage) GetNextCursor() string`

GetNextCursor returns the NextCursor field if non-nil, zero value otherwise.

### GetNextCursorOk

`func (o *DeliveryPage) GetNextCursorOk() (*string, bool)`

GetNextCursorOk returns a tuple with the NextCursor field if it's non-nil, zero value otherwise
and a boolean to check if the value has been set.

### SetNextCursor

`func (o *DeliveryPage) SetNextCursor(v string)`

SetNextCursor sets NextCursor field to given value.



[[Back to Model list]](../README.md#documentation-for-models) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to README]](../README.md)


