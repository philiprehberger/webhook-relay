import { createHmac, timingSafeEqual } from "crypto";

/**
 * Verify an X-Webhook-Signature header from Webhook Relay.
 *
 * Header format: `t=<unix_ts>,v1=<hex_hmac_sha256>` where the HMAC is
 * computed over `"{t}.{raw_body}"` using the subscription's signing secret.
 *
 * The raw body must be the exact bytes received — JSON.stringify(JSON.parse(...))
 * is NOT safe (Node's stringifier may reorder keys / change whitespace).
 *
 * @param secret              Subscription signing secret (whsec_...).
 * @param body                Raw request body, exactly as received.
 * @param header              Value of the X-Webhook-Signature header.
 * @param toleranceSeconds    Reject signatures older than this. Default 300s.
 */
export function verifySignature(
  secret: string,
  body: string,
  header: string | undefined | null,
  toleranceSeconds: number = 300,
): boolean {
  if (!header) return false;

  const parts: Record<string, string> = {};
  for (const seg of header.split(",")) {
    const idx = seg.indexOf("=");
    if (idx === -1) continue;
    parts[seg.slice(0, idx).trim()] = seg.slice(idx + 1).trim();
  }

  if (!parts.t || !parts.v1) return false;

  const ts = Number.parseInt(parts.t, 10);
  if (!Number.isFinite(ts)) return false;

  if (Math.abs(Math.floor(Date.now() / 1000) - ts) > toleranceSeconds) {
    return false;
  }

  const expectedHex = createHmac("sha256", secret)
    .update(`${ts}.${body}`)
    .digest("hex");

  if (expectedHex.length !== parts.v1.length) return false;

  return timingSafeEqual(
    Buffer.from(expectedHex, "utf8"),
    Buffer.from(parts.v1, "utf8"),
  );
}
