
# Event


## Properties

Name | Type
------------ | -------------
`id` | string
`type` | string
`payload` | { [key: string]: any; }
`idempotencyKey` | string
`sourceIp` | string
`createdAt` | Date
`deliveriesSummary` | [EventDeliveriesSummary](EventDeliveriesSummary.md)

## Example

```typescript
import type { Event } from '@philiprehberger/webhook-relay-client'

// TODO: Update the object below with actual values
const example = {
  "id": 01JAB3K5XYZQRSTUVWXYZABCDE,
  "type": order.created,
  "payload": null,
  "idempotencyKey": null,
  "sourceIp": null,
  "createdAt": null,
  "deliveriesSummary": null,
} satisfies Event

console.log(example)

// Convert the instance to a JSON string
const exampleJSON: string = JSON.stringify(example)
console.log(exampleJSON)

// Parse the JSON string back to an object
const exampleParsed = JSON.parse(exampleJSON) as Event
console.log(exampleParsed)
```

[[Back to top]](#) [[Back to API list]](../README.md#api-endpoints) [[Back to Model list]](../README.md#models) [[Back to README]](../README.md)


