# DeliveryAttempt

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**attempt_number** | **int** |  |
**request_signature** | **string** | Format: t&#x3D;{unix_ts},v1&#x3D;{hex_hmac_sha256} |
**response_status** | **int** |  | [optional]
**response_headers** | **array<string,string>** |  | [optional]
**response_body_snippet** | **string** | First 4 KB of the response body, UTF-8 best-effort. | [optional]
**latency_ms** | **int** |  | [optional]
**error_code** | **string** |  | [optional]
**attempted_at** | **\DateTime** |  |

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
