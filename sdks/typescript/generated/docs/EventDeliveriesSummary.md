
# EventDeliveriesSummary


## Properties

Name | Type
------------ | -------------
`total` | number
`succeeded` | number
`failed` | number
`pending` | number

## Example

```typescript
import type { EventDeliveriesSummary } from '@philiprehberger/webhook-relay-client'

// TODO: Update the object below with actual values
const example = {
  "total": null,
  "succeeded": null,
  "failed": null,
  "pending": null,
} satisfies EventDeliveriesSummary

console.log(example)

// Convert the instance to a JSON string
const exampleJSON: string = JSON.stringify(example)
console.log(exampleJSON)

// Parse the JSON string back to an object
const exampleParsed = JSON.parse(exampleJSON) as EventDeliveriesSummary
console.log(exampleParsed)
```

[[Back to top]](#) [[Back to API list]](../README.md#api-endpoints) [[Back to Model list]](../README.md#models) [[Back to README]](../README.md)


