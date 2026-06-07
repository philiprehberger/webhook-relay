import { CodeBlock } from "../../../../components/CodeBlock";
import { DocsLayout } from "../../../../components/DocsLayout";

export const metadata = { title: "Event filters" };

export default function FilteringGuide() {
  return (
    <DocsLayout>
      <h1>Event filters</h1>
      <p>
        Subscriptions don&apos;t receive every event by default — they
        receive events whose <code>type</code> matches their{" "}
        <code>event_filter</code>. The filter language is small on purpose.
      </p>

      <h2>Three forms</h2>
      <table className="border-collapse text-sm">
        <thead>
          <tr>
            <th className="border border-zinc-800 px-3 py-2 text-left">Filter</th>
            <th className="border border-zinc-800 px-3 py-2 text-left">Matches</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td className="border border-zinc-800 px-3 py-2"><code>*</code></td>
            <td className="border border-zinc-800 px-3 py-2">every event in the workspace</td>
          </tr>
          <tr>
            <td className="border border-zinc-800 px-3 py-2"><code>order.created</code></td>
            <td className="border border-zinc-800 px-3 py-2">exact match only</td>
          </tr>
          <tr>
            <td className="border border-zinc-800 px-3 py-2"><code>order.*</code></td>
            <td className="border border-zinc-800 px-3 py-2">
              prefix glob — <code>order.created</code>, <code>order.shipped</code>,{" "}
              <code>order.deep.nested.type</code>
            </td>
          </tr>
        </tbody>
      </table>

      <h2>Naming events</h2>
      <p>
        Event types use a dot-separated, lower-case grammar:{" "}
        <code>noun.verb</code> or <code>noun.verb.detail</code>. Examples
        from existing patterns: <code>order.created</code>,{" "}
        <code>payment.succeeded</code>, <code>user.signed_up</code>,{" "}
        <code>invoice.payment_failed</code>. The validation regex is{" "}
        <code>^[a-z0-9._-]&#123;1,128&#125;$</code>.
      </p>

      <h2>Two failure modes</h2>
      <h3>The wildcard isn&apos;t a substring</h3>
      <p>
        <code>order.*</code> matches <code>order.created</code> but{" "}
        <strong>not</strong> <code>order</code>. The glob always extends
        past at least one dot.
      </p>

      <h3>Filter changes don&apos;t affect past events</h3>
      <p>
        Editing a subscription&apos;s filter changes which future events
        match. It does not retroactively dispatch deliveries for events
        that were ingested before the change. To replay against a new
        filter, use the dead-letter / retry endpoints.
      </p>

      <h2>Coming later: JSONPath body filters</h2>
      <p>
        A second filter (e.g. <code>$.amount &gt; 100</code>) is in the
        plan but not shipped yet. Today, filtering happens at the{" "}
        <code>type</code> level only.
      </p>

      <CodeBlock language="curl">
{`# Subscribe to every order event:
curl -X POST https://api.webhook-relay.dcsuniverse.com/v1/subscriptions \\
  -H "Authorization: Bearer whk_live_..." \\
  -d '{"url":"https://my-app/hooks","event_filter":"order.*"}'`}
      </CodeBlock>
    </DocsLayout>
  );
}
