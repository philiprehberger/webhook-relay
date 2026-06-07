# Delivery


## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**id** | **str** |  | 
**event_id** | **str** |  | 
**subscription_id** | **str** |  | 
**status** | **str** |  | 
**attempts_made** | **int** |  | 
**next_attempt_at** | **datetime** |  | [optional] 
**final_status_code** | **int** |  | [optional] 
**completed_at** | **datetime** |  | [optional] 
**created_at** | **datetime** |  | 
**attempts** | [**List[DeliveryAttempt]**](DeliveryAttempt.md) | Present on retrieve, omitted on list. | [optional] 

## Example

```python
from webhook_relay_client.models.delivery import Delivery

# TODO update the JSON string below
json = "{}"
# create an instance of Delivery from a JSON string
delivery_instance = Delivery.from_json(json)
# print the JSON string representation of the object
print(Delivery.to_json())

# convert the object into a dict
delivery_dict = delivery_instance.to_dict()
# create an instance of Delivery from a dict
delivery_from_dict = Delivery.from_dict(delivery_dict)
```
[[Back to Model list]](../README.md#documentation-for-models) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to README]](../README.md)


