import { createHmac } from "crypto";
import { strict as assert } from "node:assert";
import { test } from "node:test";
import { verifySignature } from "./verifySignature.js";

const SECRET = "whsec_test_round_trip";

function sign(secret: string, body: string, ts: number = Math.floor(Date.now() / 1000)): string {
  const hex = createHmac("sha256", secret).update(`${ts}.${body}`).digest("hex");
  return `t=${ts},v1=${hex}`;
}

test("verifySignature round-trips against the canonical PHP signer", () => {
  const body = '{"event":"order.created","amount":100}';
  const header = sign(SECRET, body);
  assert.equal(verifySignature(SECRET, body, header), true);
});

test("rejects a tampered body", () => {
  const header = sign(SECRET, "original body");
  assert.equal(verifySignature(SECRET, "tampered body", header), false);
});

test("rejects a wrong secret", () => {
  const header = sign(SECRET, "body");
  assert.equal(verifySignature("whsec_wrong", "body", header), false);
});

test("rejects a timestamp outside tolerance", () => {
  const oldTs = Math.floor(Date.now() / 1000) - 1000;
  const header = sign(SECRET, "body", oldTs);
  assert.equal(verifySignature(SECRET, "body", header, 300), false);
});

test("rejects malformed headers", () => {
  assert.equal(verifySignature(SECRET, "body", "garbage"), false);
  assert.equal(verifySignature(SECRET, "body", "t=abc,v1=xyz"), false);
  assert.equal(verifySignature(SECRET, "body", "v1=xyz"), false);
  assert.equal(verifySignature(SECRET, "body", null), false);
});
