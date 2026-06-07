package webhookrelay

import (
	"crypto/hmac"
	"crypto/sha256"
	"encoding/hex"
	"fmt"
	"testing"
	"time"
)

const testSecret = "whsec_test_round_trip"

func sign(secret, body string, ts int64) string {
	mac := hmac.New(sha256.New, []byte(secret))
	fmt.Fprintf(mac, "%d.%s", ts, body)
	return fmt.Sprintf("t=%d,v1=%s", ts, hex.EncodeToString(mac.Sum(nil)))
}

func TestVerifySignature_RoundTrip(t *testing.T) {
	body := `{"event":"order.created","amount":100}`
	header := sign(testSecret, body, time.Now().Unix())
	if !VerifySignature(testSecret, body, header, 0) {
		t.Fatal("expected valid signature")
	}
}

func TestVerifySignature_TamperedBody(t *testing.T) {
	header := sign(testSecret, "original body", time.Now().Unix())
	if VerifySignature(testSecret, "tampered body", header, 0) {
		t.Fatal("expected tampered body to fail")
	}
}

func TestVerifySignature_WrongSecret(t *testing.T) {
	header := sign(testSecret, "body", time.Now().Unix())
	if VerifySignature("whsec_wrong", "body", header, 0) {
		t.Fatal("expected wrong secret to fail")
	}
}

func TestVerifySignature_OldTimestamp(t *testing.T) {
	header := sign(testSecret, "body", time.Now().Unix()-1000)
	if VerifySignature(testSecret, "body", header, 5*time.Minute) {
		t.Fatal("expected old timestamp to fail")
	}
}

func TestVerifySignature_MalformedHeader(t *testing.T) {
	cases := []string{
		"",
		"garbage",
		"t=abc,v1=xyz",
		"v1=xyz",
	}
	for _, c := range cases {
		if VerifySignature(testSecret, "body", c, 0) {
			t.Fatalf("expected malformed header %q to fail", c)
		}
	}
}
