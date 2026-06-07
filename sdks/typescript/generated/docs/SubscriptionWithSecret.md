
# SubscriptionWithSecret


## Properties

Name | Type
------------ | -------------
`id` | string
`name` | string
`url` | string
`eventFilter` | string
`state` | string
`consecutiveFailures` | number
`pausedAt` | Date
`secretRotatedAt` | Date
`createdAt` | Date
`updatedAt` | Date
`signingSecret` | string

## Example

```typescript
import type { SubscriptionWithSecret } from '@philiprehberger/webhook-relay-client'

// TODO: Update the object below with actual values
const example = {
  "id": null,
  "name": null,
  "url": null,
  "eventFilter": null,
  "state": null,
  "consecutiveFailures": null,
  "pausedAt": null,
  "secretRotatedAt": null,
  "createdAt": null,
  "updatedAt": null,
  "signingSecret": whsec_abcdef0123456789...,
} satisfies SubscriptionWithSecret

console.log(example)

// Convert the instance to a JSON string
const exampleJSON: string = JSON.stringify(example)
console.log(exampleJSON)

// Parse the JSON string back to an object
const exampleParsed = JSON.parse(exampleJSON) as SubscriptionWithSecret
console.log(exampleParsed)
```

[[Back to top]](#) [[Back to API list]](../README.md#api-endpoints) [[Back to Model list]](../README.md#models) [[Back to README]](../README.md)


