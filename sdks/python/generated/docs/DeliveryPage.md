# DeliveryPage


## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**data** | [**List[Delivery]**](Delivery.md) |  | 
**next_cursor** | **str** |  | 

## Example

```python
from webhook_relay_client.models.delivery_page import DeliveryPage

# TODO update the JSON string below
json = "{}"
# create an instance of DeliveryPage from a JSON string
delivery_page_instance = DeliveryPage.from_json(json)
# print the JSON string representation of the object
print(DeliveryPage.to_json())

# convert the object into a dict
delivery_page_dict = delivery_page_instance.to_dict()
# create an instance of DeliveryPage from a dict
delivery_page_from_dict = DeliveryPage.from_dict(delivery_page_dict)
```
[[Back to Model list]](../README.md#documentation-for-models) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to README]](../README.md)


