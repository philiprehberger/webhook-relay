
# DeliveryAttempt


## Properties

Name | Type
------------ | -------------
`attemptNumber` | number
`requestSignature` | string
`responseStatus` | number
`responseHeaders` | { [key: string]: string; }
`responseBodySnippet` | string
`latencyMs` | number
`errorCode` | string
`attemptedAt` | Date

## Example

```typescript
import type { DeliveryAttempt } from '@philiprehberger/webhook-relay-client'

// TODO: Update the object below with actual values
const example = {
  "attemptNumber": null,
  "requestSignature": null,
  "responseStatus": null,
  "responseHeaders": null,
  "responseBodySnippet": null,
  "latencyMs": null,
  "errorCode": null,
  "attemptedAt": null,
} satisfies DeliveryAttempt

console.log(example)

// Convert the instance to a JSON string
const exampleJSON: string = JSON.stringify(example)
console.log(exampleJSON)

// Parse the JSON string back to an object
const exampleParsed = JSON.parse(exampleJSON) as DeliveryAttempt
console.log(exampleParsed)
```

[[Back to top]](#) [[Back to API list]](../README.md#api-endpoints) [[Back to Model list]](../README.md#models) [[Back to README]](../README.md)


