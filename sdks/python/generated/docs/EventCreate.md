# EventCreate


## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**type** | **str** | Dot-separated event type. | 
**payload** | **Dict[str, object]** | Arbitrary JSON payload, max 256 KB serialized. | 

## Example

```python
from webhook_relay_client.models.event_create import EventCreate

# TODO update the JSON string below
json = "{}"
# create an instance of EventCreate from a JSON string
event_create_instance = EventCreate.from_json(json)
# print the JSON string representation of the object
print(EventCreate.to_json())

# convert the object into a dict
event_create_dict = event_create_instance.to_dict()
# create an instance of EventCreate from a dict
event_create_from_dict = EventCreate.from_dict(event_create_dict)
```
[[Back to Model list]](../README.md#documentation-for-models) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to README]](../README.md)


