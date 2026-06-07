# SubscriptionUpdate


## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**name** | **str** |  | [optional] 
**url** | **str** |  | [optional] 
**event_filter** | **str** |  | [optional] 

## Example

```python
from webhook_relay_client.models.subscription_update import SubscriptionUpdate

# TODO update the JSON string below
json = "{}"
# create an instance of SubscriptionUpdate from a JSON string
subscription_update_instance = SubscriptionUpdate.from_json(json)
# print the JSON string representation of the object
print(SubscriptionUpdate.to_json())

# convert the object into a dict
subscription_update_dict = subscription_update_instance.to_dict()
# create an instance of SubscriptionUpdate from a dict
subscription_update_from_dict = SubscriptionUpdate.from_dict(subscription_update_dict)
```
[[Back to Model list]](../README.md#documentation-for-models) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to README]](../README.md)


