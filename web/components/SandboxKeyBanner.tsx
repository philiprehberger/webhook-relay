"use client";

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

export function SandboxKeyBanner() {
  const [state, setState] = useState<
    | { kind: "idle" }
    | { kind: "loading" }
    | { kind: "ready"; key: string; allowedHosts: string[] }
    | { kind: "error"; message: string }
  >({ kind: "idle" });

  async function mint() {
    setState({ kind: "loading" });
    try {
      const response = await fetch(
        "https://api.webhook-relay.dcsuniverse.com/v1/sandbox/keys",
        { method: "POST" },
      );
      if (!response.ok) {
        const problem = (await response.json().catch(() => null)) as MintError | null;
        setState({
          kind: "error",
          message: problem?.detail ?? `Mint failed with status ${response.status}.`,
        });
        return;
      }
      const data = (await response.json()) as MintResponse;
      setState({ kind: "ready", key: data.key, allowedHosts: data.allowed_receiver_hosts });
    } catch (err) {
      setState({
        kind: "error",
        message: err instanceof Error ? err.message : "Network error.",
      });
    }
  }

  return (
    <div className="border-b border-zinc-800 bg-zinc-900/60">
      <div className="mx-auto max-w-7xl px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div className="text-sm text-zinc-300">
          <span className="text-zinc-100 font-medium">Try the API.</span>{" "}
          Get a 24-hour sandbox key, paste it into the{" "}
          <span className="font-mono text-sky-300">Authorize</span> panel below,
          and fire real cross-origin calls.{" "}
          <span className="text-zinc-500">
            Sandbox subscriptions can deliver to webhook.site, requestbin.com,
            and httpbin.org.
          </span>
        </div>

        <div className="flex flex-col sm:items-end gap-2">
          {state.kind === "idle" && (
            <button
              onClick={mint}
              className="rounded-md bg-sky-400 text-sky-950 hover:bg-sky-300 transition-colors px-3 py-1.5 text-sm font-medium"
            >
              Get sandbox key →
            </button>
          )}
          {state.kind === "loading" && (
            <span className="text-sm text-zinc-400">Minting…</span>
          )}
          {state.kind === "ready" && (
            <div className="flex flex-col gap-1">
              <p className="text-xs text-zinc-500">
                Copy this. It is not retrievable later.
              </p>
              <code
                onClick={() => navigator.clipboard?.writeText(state.key)}
                className="text-xs font-mono text-sky-200 bg-sky-300/10 rounded px-2 py-1 break-all cursor-pointer hover:bg-sky-300/15"
                title="Click to copy"
              >
                {state.key}
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
