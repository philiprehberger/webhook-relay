# Delivery

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**id** | **string** |  |
**event_id** | **string** |  |
**subscription_id** | **string** |  |
**status** | **string** |  |
**attempts_made** | **int** |  |
**next_attempt_at** | **\DateTime** |  | [optional]
**final_status_code** | **int** |  | [optional]
**completed_at** | **\DateTime** |  | [optional]
**created_at** | **\DateTime** |  |
**attempts** | [**\WebhookRelayClient\Model\DeliveryAttempt[]**](DeliveryAttempt.md) | Present on retrieve, omitted on list. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
