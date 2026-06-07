# SubscriptionPage


## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**data** | [**List[Subscription]**](Subscription.md) |  | 
**next_cursor** | **str** |  | 

## Example

```python
from webhook_relay_client.models.subscription_page import SubscriptionPage

# TODO update the JSON string below
json = "{}"
# create an instance of SubscriptionPage from a JSON string
subscription_page_instance = SubscriptionPage.from_json(json)
# print the JSON string representation of the object
print(SubscriptionPage.to_json())

# convert the object into a dict
subscription_page_dict = subscription_page_instance.to_dict()
# create an instance of SubscriptionPage from a dict
subscription_page_from_dict = SubscriptionPage.from_dict(subscription_page_dict)
```
[[Back to Model list]](../README.md#documentation-for-models) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to README]](../README.md)


