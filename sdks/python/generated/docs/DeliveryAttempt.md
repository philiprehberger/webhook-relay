# DeliveryAttempt


## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**attempt_number** | **int** |  | 
**request_signature** | **str** | Format: t&#x3D;{unix_ts},v1&#x3D;{hex_hmac_sha256} | 
**response_status** | **int** |  | [optional] 
**response_headers** | **Dict[str, str]** |  | [optional] 
**response_body_snippet** | **str** | First 4 KB of the response body, UTF-8 best-effort. | [optional] 
**latency_ms** | **int** |  | [optional] 
**error_code** | **str** |  | [optional] 
**attempted_at** | **datetime** |  | 

## Example

```python
from webhook_relay_client.models.delivery_attempt import DeliveryAttempt

# TODO update the JSON string below
json = "{}"
# create an instance of DeliveryAttempt from a JSON string
delivery_attempt_instance = DeliveryAttempt.from_json(json)
# print the JSON string representation of the object
print(DeliveryAttempt.to_json())

# convert the object into a dict
delivery_attempt_dict = delivery_attempt_instance.to_dict()
# create an instance of DeliveryAttempt from a dict
delivery_attempt_from_dict = DeliveryAttempt.from_dict(delivery_attempt_dict)
```
[[Back to Model list]](../README.md#documentation-for-models) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to README]](../README.md)


