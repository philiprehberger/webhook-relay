"""Webhook Relay Python SDK — hand-written ergonomics layer.

The auto-generated OpenAPI client lives in ``webhook_relay_client`` (under
``./generated/``). This package adds the things receivers actually use:
the HMAC signature verifier.
"""

from .signature import verify_signature

__all__ = ["verify_signature"]
__version__ = "0.3.0"
