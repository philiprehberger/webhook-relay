
# SubscriptionPage


## Properties

Name | Type
------------ | -------------
`data` | [Array&lt;Subscription&gt;](Subscription.md)
`nextCursor` | string

## Example

```typescript
import type { SubscriptionPage } from '@philiprehberger/webhook-relay-client'

// TODO: Update the object below with actual values
const example = {
  "data": null,
  "nextCursor": null,
} satisfies SubscriptionPage

console.log(example)

// Convert the instance to a JSON string
const exampleJSON: string = JSON.stringify(example)
console.log(exampleJSON)

// Parse the JSON string back to an object
const exampleParsed = JSON.parse(exampleJSON) as SubscriptionPage
console.log(exampleParsed)
```

[[Back to top]](#) [[Back to API list]](../README.md#api-endpoints) [[Back to Model list]](../README.md#models) [[Back to README]](../README.md)


