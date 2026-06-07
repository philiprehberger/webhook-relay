
# EventCreate


## Properties

Name | Type
------------ | -------------
`type` | string
`payload` | { [key: string]: any; }

## Example

```typescript
import type { EventCreate } from '@philiprehberger/webhook-relay-client'

// TODO: Update the object below with actual values
const example = {
  "type": order.created,
  "payload": null,
} satisfies EventCreate

console.log(example)

// Convert the instance to a JSON string
const exampleJSON: string = JSON.stringify(example)
console.log(exampleJSON)

// Parse the JSON string back to an object
const exampleParsed = JSON.parse(exampleJSON) as EventCreate
console.log(exampleParsed)
```

[[Back to top]](#) [[Back to API list]](../README.md#api-endpoints) [[Back to Model list]](../README.md#models) [[Back to README]](../README.md)


