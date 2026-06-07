
# Delivery


## Properties

Name | Type
------------ | -------------
`id` | string
`eventId` | string
`subscriptionId` | string
`status` | string
`attemptsMade` | number
`nextAttemptAt` | Date
`finalStatusCode` | number
`completedAt` | Date
`createdAt` | Date
`attempts` | [Array&lt;DeliveryAttempt&gt;](DeliveryAttempt.md)

## Example

```typescript
import type { Delivery } from '@philiprehberger/webhook-relay-client'

// TODO: Update the object below with actual values
const example = {
  "id": null,
  "eventId": null,
  "subscriptionId": null,
  "status": null,
  "attemptsMade": null,
  "nextAttemptAt": null,
  "finalStatusCode": null,
  "completedAt": null,
  "createdAt": null,
  "attempts": null,
} satisfies Delivery

console.log(example)

// Convert the instance to a JSON string
const exampleJSON: string = JSON.stringify(example)
console.log(exampleJSON)

// Parse the JSON string back to an object
const exampleParsed = JSON.parse(exampleJSON) as Delivery
console.log(exampleParsed)
```

[[Back to top]](#) [[Back to API list]](../README.md#api-endpoints) [[Back to Model list]](../README.md#models) [[Back to README]](../README.md)


