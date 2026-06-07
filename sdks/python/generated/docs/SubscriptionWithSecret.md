# SubscriptionWithSecret


## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**id** | **str** |  | 
**name** | **str** |  | [optional] 
**url** | **str** |  | 
**event_filter** | **str** |  | 
**state** | **str** |  | 
**consecutive_failures** | **int** |  | 
**paused_at** | **datetime** |  | [optional] 
**secret_rotated_at** | **datetime** |  | [optional] 
**created_at** | **datetime** |  | 
**updated_at** | **datetime** |  | [optional] 
**signing_secret** | **str** | Plaintext signing secret. Shown only on creation and rotation. | 

## Example

```python
from webhook_relay_client.models.subscription_with_secret import SubscriptionWithSecret

# TODO update the JSON string below
json = "{}"
# create an instance of SubscriptionWithSecret from a JSON string
subscription_with_secret_instance = SubscriptionWithSecret.from_json(json)
# print the JSON string representation of the object
print(SubscriptionWithSecret.to_json())

# convert the object into a dict
subscription_with_secret_dict = subscription_with_secret_instance.to_dict()
# create an instance of SubscriptionWithSecret from a dict
subscription_with_secret_from_dict = SubscriptionWithSecret.from_dict(subscription_with_secret_dict)
```
[[Back to Model list]](../README.md#documentation-for-models) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to README]](../README.md)


