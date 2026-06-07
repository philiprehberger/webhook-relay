# EventDeliveriesSummary


## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**total** | **int** |  | 
**succeeded** | **int** |  | 
**failed** | **int** |  | 
**pending** | **int** |  | 

## Example

```python
from webhook_relay_client.models.event_deliveries_summary import EventDeliveriesSummary

# TODO update the JSON string below
json = "{}"
# create an instance of EventDeliveriesSummary from a JSON string
event_deliveries_summary_instance = EventDeliveriesSummary.from_json(json)
# print the JSON string representation of the object
print(EventDeliveriesSummary.to_json())

# convert the object into a dict
event_deliveries_summary_dict = event_deliveries_summary_instance.to_dict()
# create an instance of EventDeliveriesSummary from a dict
event_deliveries_summary_from_dict = EventDeliveriesSummary.from_dict(event_deliveries_summary_dict)
```
[[Back to Model list]](../README.md#documentation-for-models) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to README]](../README.md)


