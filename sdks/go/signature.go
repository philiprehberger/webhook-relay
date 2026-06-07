// Package webhookrelay provides a receiver-side HMAC verifier for the
// Webhook Relay API's X-Webhook-Signature header.
//
// Header format: t={unix_ts},v1={hex_hmac_sha256}
// Signed payload: "{t}.{raw_body}"
//
// The raw body must be the exact bytes received. json.Marshal(json.Unmarshal(...))
// is NOT safe — Go's encoding/json will reorder map keys and break the signature.
package webhookrelay

import (
	"crypto/hmac"
	"crypto/sha256"
	"encoding/hex"
	"fmt"
	"strconv"
	"strings"
	"time"
)

// VerifySignature returns true if the header signature matches body+secret
// within tolerance. Default tolerance is 5 minutes; pass 0 to use it.
func VerifySignature(secret, body, header string, tolerance time.Duration) bool {
	if header == "" {
		return false
	}
	if tolerance == 0 {
		tolerance = 5 * time.Minute
	}

	var ts int64
	var sig string
	for _, segment := range strings.Split(header, ",") {
		kv := strings.SplitN(strings.TrimSpace(segment), "=", 2)
		if len(kv) != 2 {
			continue
		}
		switch kv[0] {
		case "t":
			parsed, err := strconv.ParseInt(kv[1], 10, 64)
			if err != nil {
				return false
			}
			ts = parsed
		case "v1":
			sig = kv[1]
		}
	}
	if ts == 0 || sig == "" {
		return false
	}

	delta := time.Since(time.Unix(ts, 0))
	if delta < 0 {
		delta = -delta
	}
	if delta > tolerance {
		return false
	}

	mac := hmac.New(sha256.New, []byte(secret))
	fmt.Fprintf(mac, "%d.%s", ts, body)
	expected := hex.EncodeToString(mac.Sum(nil))

	return hmac.Equal([]byte(expected), []byte(sig))
}
