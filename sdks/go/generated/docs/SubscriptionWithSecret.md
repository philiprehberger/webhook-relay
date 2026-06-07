# SubscriptionWithSecret

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**Id** | **string** |  | 
**Name** | Pointer to **string** |  | [optional] 
**Url** | **string** |  | 
**EventFilter** | **string** |  | 
**State** | **string** |  | 
**ConsecutiveFailures** | **int32** |  | 
**PausedAt** | Pointer to **time.Time** |  | [optional] 
**SecretRotatedAt** | Pointer to **time.Time** |  | [optional] 
**CreatedAt** | **time.Time** |  | 
**UpdatedAt** | Pointer to **time.Time** |  | [optional] 
**SigningSecret** | **string** | Plaintext signing secret. Shown only on creation and rotation. | 

## Methods

### NewSubscriptionWithSecret

`func NewSubscriptionWithSecret(id string, url string, eventFilter string, state string, consecutiveFailures int32, createdAt time.Time, signingSecret string, ) *SubscriptionWithSecret`

NewSubscriptionWithSecret instantiates a new SubscriptionWithSecret object
This constructor will assign default values to properties that have it defined,
and makes sure properties required by API are set, but the set of arguments
will change when the set of required properties is changed

### NewSubscriptionWithSecretWithDefaults

`func NewSubscriptionWithSecretWithDefaults() *SubscriptionWithSecret`

NewSubscriptionWithSecretWithDefaults instantiates a new SubscriptionWithSecret object
This constructor will only assign default values to properties that have it defined,
but it doesn't guarantee that properties required by API are set

### GetId

`func (o *SubscriptionWithSecret) GetId() string`

GetId returns the Id field if non-nil, zero value otherwise.

### GetIdOk

`func (o *SubscriptionWithSecret) GetIdOk() (*string, bool)`

GetIdOk returns a tuple with the Id field if it's non-nil, zero value otherwise
and a boolean to check if the value has been set.

### SetId

`func (o *SubscriptionWithSecret) SetId(v string)`

SetId sets Id field to given value.


### GetName

`func (o *SubscriptionWithSecret) GetName() string`

GetName returns the Name field if non-nil, zero value otherwise.

### GetNameOk

`func (o *SubscriptionWithSecret) GetNameOk() (*string, bool)`

GetNameOk returns a tuple with the Name field if it's non-nil, zero value otherwise
and a boolean to check if the value has been set.

### SetName

`func (o *SubscriptionWithSecret) SetName(v string)`

SetName sets Name field to given value.

### HasName

`func (o *SubscriptionWithSecret) HasName() bool`

HasName returns a boolean if a field has been set.

### GetUrl

`func (o *SubscriptionWithSecret) GetUrl() string`

GetUrl returns the Url field if non-nil, zero value otherwise.

### GetUrlOk

`func (o *SubscriptionWithSecret) GetUrlOk() (*string, bool)`

GetUrlOk returns a tuple with the Url field if it's non-nil, zero value otherwise
and a boolean to check if the value has been set.

### SetUrl

`func (o *SubscriptionWithSecret) SetUrl(v string)`

SetUrl sets Url field to given value.


### GetEventFilter

`func (o *SubscriptionWithSecret) GetEventFilter() string`

GetEventFilter returns the EventFilter field if non-nil, zero value otherwise.

### GetEventFilterOk

`func (o *SubscriptionWithSecret) GetEventFilterOk() (*string, bool)`

GetEventFilterOk returns a tuple with the EventFilter field if it's non-nil, zero value otherwise
and a boolean to check if the value has been set.

### SetEventFilter

`func (o *SubscriptionWithSecret) SetEventFilter(v string)`

SetEventFilter sets EventFilter field to given value.


### GetState

`func (o *SubscriptionWithSecret) GetState() string`

GetState returns the State field if non-nil, zero value otherwise.

### GetStateOk

`func (o *SubscriptionWithSecret) GetStateOk() (*string, bool)`

GetStateOk returns a tuple with the State field if it's non-nil, zero value otherwise
and a boolean to check if the value has been set.

### SetState

`func (o *SubscriptionWithSecret) SetState(v string)`

SetState sets State field to given value.


### GetConsecutiveFailures

`func (o *SubscriptionWithSecret) GetConsecutiveFailures() int32`

GetConsecutiveFailures returns the ConsecutiveFailures field if non-nil, zero value otherwise.

### GetConsecutiveFailuresOk

`func (o *SubscriptionWithSecret) GetConsecutiveFailuresOk() (*int32, bool)`

GetConsecutiveFailuresOk returns a tuple with the ConsecutiveFailures field if it's non-nil, zero value otherwise
and a boolean to check if the value has been set.

### SetConsecutiveFailures

`func (o *SubscriptionWithSecret) SetConsecutiveFailures(v int32)`

SetConsecutiveFailures sets ConsecutiveFailures field to given value.


### GetPausedAt

`func (o *SubscriptionWithSecret) GetPausedAt() time.Time`

GetPausedAt returns the PausedAt field if non-nil, zero value otherwise.

### GetPausedAtOk

`func (o *SubscriptionWithSecret) GetPausedAtOk() (*time.Time, bool)`

GetPausedAtOk returns a tuple with the PausedAt field if it's non-nil, zero value otherwise
and a boolean to check if the value has been set.

### SetPausedAt

`func (o *SubscriptionWithSecret) SetPausedAt(v time.Time)`

SetPausedAt sets PausedAt field to given value.

### HasPausedAt

`func (o *SubscriptionWithSecret) HasPausedAt() bool`

HasPausedAt returns a boolean if a field has been set.

### GetSecretRotatedAt

`func (o *SubscriptionWithSecret) GetSecretRotatedAt() time.Time`

GetSecretRotatedAt returns the SecretRotatedAt field if non-nil, zero value otherwise.

### GetSecretRotatedAtOk

`func (o *SubscriptionWithSecret) GetSecretRotatedAtOk() (*time.Time, bool)`

GetSecretRotatedAtOk returns a tuple with the SecretRotatedAt field if it's non-nil, zero value otherwise
and a boolean to check if the value has been set.

### SetSecretRotatedAt

`func (o *SubscriptionWithSecret) SetSecretRotatedAt(v time.Time)`

SetSecretRotatedAt sets SecretRotatedAt field to given value.

### HasSecretRotatedAt

`func (o *SubscriptionWithSecret) HasSecretRotatedAt() bool`

HasSecretRotatedAt returns a boolean if a field has been set.

### GetCreatedAt

`func (o *SubscriptionWithSecret) GetCreatedAt() time.Time`

GetCreatedAt returns the CreatedAt field if non-nil, zero value otherwise.

### GetCreatedAtOk

`func (o *SubscriptionWithSecret) GetCreatedAtOk() (*time.Time, bool)`

GetCreatedAtOk returns a tuple with the CreatedAt field if it's non-nil, zero value otherwise
and a boolean to check if the value has been set.

### SetCreatedAt

`func (o *SubscriptionWithSecret) SetCreatedAt(v time.Time)`

SetCreatedAt sets CreatedAt field to given value.


### GetUpdatedAt

`func (o *SubscriptionWithSecret) GetUpdatedAt() time.Time`

GetUpdatedAt returns the UpdatedAt field if non-nil, zero value otherwise.

### GetUpdatedAtOk

`func (o *SubscriptionWithSecret) GetUpdatedAtOk() (*time.Time, bool)`

GetUpdatedAtOk returns a tuple with the UpdatedAt field if it's non-nil, zero value otherwise
and a boolean to check if the value has been set.

### SetUpdatedAt

`func (o *SubscriptionWithSecret) SetUpdatedAt(v time.Time)`

SetUpdatedAt sets UpdatedAt field to given value.

### HasUpdatedAt

`func (o *SubscriptionWithSecret) HasUpdatedAt() bool`

HasUpdatedAt returns a boolean if a field has been set.

### GetSigningSecret

`func (o *SubscriptionWithSecret) GetSigningSecret() string`

GetSigningSecret returns the SigningSecret field if non-nil, zero value otherwise.

### GetSigningSecretOk

`func (o *SubscriptionWithSecret) GetSigningSecretOk() (*string, bool)`

GetSigningSecretOk returns a tuple with the SigningSecret field if it's non-nil, zero value otherwise
and a boolean to check if the value has been set.

### SetSigningSecret

`func (o *SubscriptionWithSecret) SetSigningSecret(v string)`

SetSigningSecret sets SigningSecret field to given value.



[[Back to Model list]](../README.md#documentation-for-models) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to README]](../README.md)


