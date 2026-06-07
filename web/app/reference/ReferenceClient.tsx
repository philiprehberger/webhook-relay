"use client";

import { ApiReferenceReact } from "@scalar/api-reference-react";
import "@scalar/api-reference-react/style.css";
import { useState } from "react";

type MintResponse = {
  key: string;
  prefix: string;
  expires_in_hours: number;
  allowed_receiver_hosts: string[];
};

type MintError = {
  status: number;
  title: string;
  detail?: string;
};

const API_BASE = "https://api.webhook-relay.dcsuniverse.com";
const SECURITY_SCHEME_NAME = "ApiKeyAuth"; // matches openapi/spec.yaml

export function ReferenceClient() {
  const [token, setToken] = useState<string | null>(null);
  const [allowedHosts, setAllowedHosts] = useState<string[]>([]);
  const [state, setState] = useState<
    | { kind: "idle" }
    | { kind: "loading" }
    | { kind: "error"; message: string }
    | { kind: "ready" }
  >({ kind: "idle" });

  async function mint() {
    setState({ kind: "loading" });
    try {
      const response = await fetch(`${API_BASE}/v1/sandbox/keys`, { method: "POST" });
      if (!response.ok) {
        const problem = (await response.json().catch(() => null)) as MintError | null;
        setState({
          kind: "error",
          message: problem?.detail ?? `Mint failed with status ${response.status}.`,
        });
        return;
      }
      const data = (await response.json()) as MintResponse;
      setToken(data.key);
      setAllowedHosts(data.allowed_receiver_hosts);
      setState({ kind: "ready" });
    } catch (err) {
      setState({
        kind: "error",
        message: err instanceof Error ? err.message : "Network error.",
      });
    }
  }

  return (
    <div>
      <SandboxBanner state={state} token={token} allowedHosts={allowedHosts} onMint={mint} />
      <ApiReferenceReact
        // The `key` forces Scalar to remount whenever the token changes so
        // its internal auth state picks up the new value.
        key={token ?? "no-token"}
        configuration={{
          url: "/openapi.yaml",
          theme: "deepSpace",
          layout: "modern",
          darkMode: true,
          hideClientButton: false,
          hideDownloadButton: false,
          persistAuth: true,
          metaData: {
            title: "Webhook Relay — API reference",
          },
          servers: [
            {
              url: API_BASE,
              description: "Production",
            },
          ],
          authentication: token
            ? {
                preferredSecurityScheme: SECURITY_SCHEME_NAME,
                securitySchemes: {
                  [SECURITY_SCHEME_NAME]: {
                    token,
                  },
                },
              }
            : undefined,
        }}
      />
    </div>
  );
}

function SandboxBanner({
  state,
  token,
  allowedHosts,
  onMint,
}: {
  state: { kind: "idle" | "loading" | "error" | "ready"; message?: string };
  token: string | null;
  allowedHosts: string[];
  onMint: () => void;
}) {
  return (
    <div className="border-b border-zinc-800 bg-zinc-900/60">
      <div className="mx-auto max-w-7xl px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div className="text-sm text-zinc-300 max-w-3xl">
          <span className="text-zinc-100 font-medium">Try the API.</span>{" "}
          Get a 24-hour sandbox key and every try-it request below sends it as{" "}
          <span className="font-mono text-sky-300">Authorization: Bearer …</span>{" "}
          automatically.{" "}
          {state.kind === "ready" && allowedHosts.length > 0 ? (
            <span className="text-zinc-500">
              Sandbox subscriptions can only deliver to {allowedHosts.join(", ")}.
            </span>
          ) : (
            <span className="text-zinc-500">
              Sandbox subscriptions can deliver to webhook.site, requestbin.com,
              and httpbin.org.
            </span>
          )}
        </div>

        <div className="flex flex-col sm:items-end gap-2 min-w-0">
          {state.kind === "idle" && (
            <button
              onClick={onMint}
              className="rounded-md bg-sky-400 text-sky-950 hover:bg-sky-300 transition-colors px-3 py-1.5 text-sm font-medium"
            >
              Get sandbox key →
            </button>
          )}
          {state.kind === "loading" && (
            <span className="text-sm text-zinc-400">Minting…</span>
          )}
          {state.kind === "ready" && token && (
            <div className="flex items-center gap-2 max-w-full">
              <button
                onClick={onMint}
                className="rounded-md border border-zinc-700 hover:border-zinc-500 px-2.5 py-1 text-xs text-zinc-300 transition-colors shrink-0"
                title="Mint another key"
              >
                ↺
              </button>
              <code
                onClick={() => navigator.clipboard?.writeText(token)}
                className="text-xs font-mono text-sky-200 bg-sky-300/10 rounded px-2 py-1 break-all cursor-pointer hover:bg-sky-300/15 truncate"
                title="Click to copy"
              >
                {token}
              </code>
            </div>
          )}
          {state.kind === "error" && (
            <p className="text-sm text-amber-300">{state.message}</p>
          )}
        </div>
      </div>
    </div>
  );
}
