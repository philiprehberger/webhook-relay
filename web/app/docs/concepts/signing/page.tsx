import { CodeBlock } from "../../../../components/CodeBlock";
import { DocsLayout } from "../../../../components/DocsLayout";

export const metadata = { title: "HMAC signing" };

export default function SigningGuide() {
  return (
    <DocsLayout>
      <h1>HMAC signing</h1>
      <p>
        Every outbound webhook carries an <code>X-Webhook-Signature</code>{" "}
        header. The receiver recomputes the HMAC over the timestamp + body
        with the shared secret and compares in constant time. Stripe and
        Svix use the same shape; if you&apos;ve seen one you&apos;ve seen
        this one.
      </p>

      <h2>Header format</h2>
      <CodeBlock language="http">
{`X-Webhook-Signature: t=1717000000,v1=cafebabe...`}
      </CodeBlock>
      <ul>
        <li>
          <code>t</code> — Unix timestamp the signature was generated at.
        </li>
        <li>
          <code>v1</code> — hex-encoded HMAC-SHA256 over the string
          <code> &quot;{`{t}.{raw_body}`}&quot; </code>.
        </li>
      </ul>

      <h2>Verifying</h2>
      <p>
        The signature verifier is a 30-line helper. Every SDK ships one — use
        the helper rather than rolling your own; constant-time comparison and
        timestamp-window checks are easy to get wrong.
      </p>
      <CodeBlock language="typescript">
{`import { verifySignature } from "@philiprehberger/webhook-relay-client";

verifySignature(
  secret,                                  // whsec_...
  rawBody,                                 // exact bytes received
  request.headers.get("x-webhook-signature"),
  300,                                     // tolerance seconds (default)
);`}
      </CodeBlock>
      <CodeBlock language="php">
{`use PhilipRehberger\\WebhookRelayClient\\Signer;

Signer::verify(
    $secret,
    file_get_contents('php://input'),
    $_SERVER['HTTP_X_WEBHOOK_SIGNATURE'] ?? '',
);`}
      </CodeBlock>
      <CodeBlock language="python">
{`from philiprehberger_webhook_relay_client import verify_signature

verify_signature(secret, request.body, request.headers.get("X-Webhook-Signature"))`}
      </CodeBlock>
      <CodeBlock language="go">
{`webhookrelay.VerifySignature(
    secret,
    string(body),
    r.Header.Get("X-Webhook-Signature"),
    0,  // default tolerance: 5 minutes
)`}
      </CodeBlock>

      <h2>Two failure modes that bite</h2>
      <h3>Re-encoding the body</h3>
      <p>
        The signature is computed over the bytes that hit the wire. If your
        framework JSON-parses the body and you re-serialize it before
        verifying, key order or whitespace can change and the signature will
        fail. Always verify against the <em>raw</em> request body.
      </p>

      <h3>Skipping the timestamp window</h3>
      <p>
        A signature on a body is replayable forever without a timestamp
        check. The SDK helpers reject signatures older than 5 minutes by
        default — keep that on. If you need a longer window, raise it
        explicitly; don&apos;t turn it off.
      </p>

      <h2>Rotating secrets</h2>
      <p>
        Calling <code>POST /v1/subscriptions/&#123;id&#125;/rotate-secret</code>
        generates a new secret and keeps the old one valid for 48 hours.
        During the window, every outbound delivery is signed with the new
        secret. Update your receiver to accept both during the grace, then
        drop the old one.
      </p>
    </DocsLayout>
  );
}
