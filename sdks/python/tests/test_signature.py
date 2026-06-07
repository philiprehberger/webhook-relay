import hashlib
import hmac
import time

from webhook_relay import verify_signature


SECRET = "whsec_test_round_trip"


def _sign(secret: str, body: str, ts: int | None = None) -> str:
    if ts is None:
        ts = int(time.time())
    hex_sig = hmac.new(secret.encode(), f"{ts}.{body}".encode(), hashlib.sha256).hexdigest()
    return f"t={ts},v1={hex_sig}"


def test_round_trips_against_canonical_signer():
    body = '{"event":"order.created","amount":100}'
    header = _sign(SECRET, body)
    assert verify_signature(SECRET, body, header) is True


def test_rejects_tampered_body():
    header = _sign(SECRET, "original body")
    assert verify_signature(SECRET, "tampered body", header) is False


def test_rejects_wrong_secret():
    header = _sign(SECRET, "body")
    assert verify_signature("whsec_wrong", "body", header) is False


def test_rejects_old_timestamp():
    header = _sign(SECRET, "body", ts=int(time.time()) - 1000)
    assert verify_signature(SECRET, "body", header, tolerance_seconds=300) is False


def test_rejects_malformed_headers():
    assert verify_signature(SECRET, "body", "garbage") is False
    assert verify_signature(SECRET, "body", "t=abc,v1=xyz") is False
    assert verify_signature(SECRET, "body", "v1=xyz") is False
    assert verify_signature(SECRET, "body", None) is False
