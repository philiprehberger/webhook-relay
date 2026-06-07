"use client";

import { useEffect, useRef, useState } from "react";

type EchoEvent = {
  id: string;
  event_id: string;
  subscription_id: string;
  status: "pending" | "success" | "failed" | "dead";
  attempts_made: number;
  final_status_code: number | null;
  updated_at: string;
};

type Line =
  | { kind: "info"; text: string }
  | { kind: "delivery"; payload: EchoEvent };

export function LiveEcho() {
  const [token, setToken] = useState<string>("");
  const [lines, setLines] = useState<Line[]>([]);
  const [connected, setConnected] = useState(false);
  const sourceRef = useRef<EventSource | null>(null);

  useEffect(() => {
    return () => sourceRef.current?.close();
  }, []);

  function connect() {
    sourceRef.current?.close();
    setLines([{ kind: "info", text: "Connecting…" }]);
    setConnected(false);

    const url =
      `https://api.webhook-relay.dcsuniverse.com/v1/echo/stream?key=${encodeURIComponent(token)}`;
    const es = new EventSource(url);
    sourceRef.current = es;

    es.addEventListener("ready", () => {
      setConnected(true);
      setLines([{ kind: "info", text: "Connected. Fire an event to see deliveries." }]);
    });

    es.addEventListener("error", (ev) => {
      const data = (ev as MessageEvent).data
        ? safeParse((ev as MessageEvent).data)?.detail ?? "Stream error."
        : "Stream error.";
      setLines((prev) => [...prev, { kind: "info", text: String(data) }]);
      setConnected(false);
      es.close();
    });

    es.addEventListener("delivery", (ev) => {
      const payload = safeParse((ev as MessageEvent).data) as EchoEvent | null;
      if (!payload) return;
      const next: Line = { kind: "delivery", payload };
      setLines((prev) => [next, ...prev].slice(0, 20));
    });

    es.addEventListener("closing", () => {
      setLines((prev) => [...prev, { kind: "info", text: "Stream ended (60s cap). Reconnect to continue." }]);
      setConnected(false);
      es.close();
    });
  }

  function disconnect() {
    sourceRef.current?.close();
    sourceRef.current = null;
    setConnected(false);
    setLines((prev) => [...prev, { kind: "info", text: "Disconnected." }]);
  }

  return (
    <div className="rounded-lg border border-zinc-800 bg-zinc-900/40 p-6">
      <div className="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between mb-4">
        <div>
          <p className="text-xs uppercase tracking-widest text-sky-300/80 mb-2">Live Echo</p>
          <h3 className="text-xl font-semibold mb-1">Watch deliveries land in real time</h3>
          <p className="text-sm text-zinc-400">
            Paste a key, connect, then fire events from{" "}
            <a href="/reference" className="text-sky-300 hover:underline underline-offset-4">the reference</a>{" "}
            and watch each delivery stream in here.
          </p>
        </div>
      </div>

      <div className="flex flex-col sm:flex-row gap-2 mb-4">
        <input
          type="password"
          value={token}
          onChange={(e) => setToken(e.target.value)}
          placeholder="whk_sandbox_… or whk_test_…"
          className="flex-1 rounded-md bg-zinc-950 border border-zinc-800 px-3 py-2 text-sm font-mono text-zinc-200 focus:border-sky-400 focus:outline-none"
        />
        {connected ? (
          <button
            onClick={disconnect}
            className="rounded-md border border-zinc-700 hover:border-zinc-500 px-4 py-2 text-sm transition-colors"
          >
            Disconnect
          </button>
        ) : (
          <button
            onClick={connect}
            disabled={token.length < 16}
            className="rounded-md bg-sky-400 text-sky-950 hover:bg-sky-300 disabled:opacity-50 disabled:cursor-not-allowed transition-colors px-4 py-2 text-sm font-medium"
          >
            Connect →
          </button>
        )}
      </div>

      <div className="rounded-md border border-zinc-800 bg-zinc-950 p-3 max-h-72 overflow-y-auto font-mono text-xs space-y-1">
        {lines.length === 0 ? (
          <p className="text-zinc-600">Paste a key and connect to begin.</p>
        ) : (
          lines.map((line, i) =>
            line.kind === "info" ? (
              <p key={i} className="text-zinc-500">— {line.text}</p>
            ) : (
              <p key={i} className="flex gap-3">
                <span className="text-zinc-500">{new Date(line.payload.updated_at).toLocaleTimeString()}</span>
                <span className={statusColor(line.payload.status)}>{line.payload.status.padEnd(8)}</span>
                <span className="text-zinc-400">attempt {line.payload.attempts_made}</span>
                <span className="text-zinc-400">
                  {line.payload.final_status_code ? `HTTP ${line.payload.final_status_code}` : ""}
                </span>
                <span className="text-zinc-600 truncate">{line.payload.id}</span>
              </p>
            ),
          )
        )}
      </div>
    </div>
  );
}

function statusColor(s: EchoEvent["status"]): string {
  switch (s) {
    case "success":
      return "text-emerald-300";
    case "pending":
      return "text-sky-300";
    case "failed":
      return "text-amber-300";
    case "dead":
      return "text-red-300";
  }
}

function safeParse(s: string): Record<string, unknown> | null {
  try {
    return JSON.parse(s);
  } catch {
    return null;
  }
}
