"""Receiver-side HMAC verifier for X-Webhook-Signature.

Header format: ``t={unix_ts},v1={hex_hmac_sha256}``
Signed payload: ``"{t}.{raw_body}"``

The raw body must be the exact bytes received. ``json.dumps(json.loads(...))``
is NOT safe — Python's serializer may reorder keys or change whitespace,
breaking the signature.
"""

from __future__ import annotations

import hashlib
import hmac
import time
from typing import Optional, Union


def verify_signature(
    secret: str,
    body: Union[str, bytes],
    header: Optional[str],
    tolerance_seconds: int = 300,
) -> bool:
    """Return True if the header signature matches body+secret within tolerance."""
    if not header:
        return False

    parts: dict[str, str] = {}
    for segment in header.split(","):
        kv = segment.strip().split("=", 1)
        if len(kv) == 2:
            parts[kv[0]] = kv[1]

    if "t" not in parts or "v1" not in parts:
        return False

    try:
        ts = int(parts["t"])
    except ValueError:
        return False

    if abs(int(time.time()) - ts) > tolerance_seconds:
        return False

    body_bytes = body.encode("utf-8") if isinstance(body, str) else body
    signed = f"{ts}.".encode("utf-8") + body_bytes
    expected = hmac.new(secret.encode("utf-8"), signed, hashlib.sha256).hexdigest()

    return hmac.compare_digest(expected, parts["v1"])
