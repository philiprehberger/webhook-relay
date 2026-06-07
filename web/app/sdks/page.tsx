import Link from "next/link";

export const metadata = { title: "SDKs" };

const SDKS = [
  {
    href: "/sdks/typescript",
    lang: "TypeScript",
    install: "npm i @philiprehberger/webhook-relay-client",
    pkg: "@philiprehberger/webhook-relay-client",
    blurb: "Node 18+. Works in serverless runtimes (Vercel, Cloudflare, Lambda).",
  },
  {
    href: "/sdks/php",
    lang: "PHP",
    install: "composer require philiprehberger/webhook-relay-client",
    pkg: "philiprehberger/webhook-relay-client",
    blurb: "PHP 8.1+. Drop-in for Laravel and standalone apps.",
  },
  {
    href: "/sdks/python",
    lang: "Python",
    install: "pip install webhook-relay-client",
    pkg: "webhook-relay-client",
    blurb: "Python 3.10+. FastAPI, Django, Flask, anything that hands you a request.",
  },
  {
    href: "/sdks/go",
    lang: "Go",
    install: "go get github.com/philiprehberger/webhook-relay/sdks/go",
    pkg: "github.com/philiprehberger/webhook-relay/sdks/go",
    blurb: "Go 1.22+. Zero runtime dependencies beyond the standard library.",
  },
];

export default function SDKsIndex() {
  return (
    <div className="mx-auto max-w-5xl px-6 py-16">
      <p className="text-xs uppercase tracking-widest text-sky-300/80 mb-4">SDKs</p>
      <h1 className="text-3xl sm:text-4xl font-semibold tracking-tight mb-4">
        One install, four languages
      </h1>
      <p className="text-lg text-zinc-400 max-w-2xl mb-12">
        Generated from the OpenAPI spec, with hand-tuned signature verifiers
        for the receiver side. Identical 5-test contract across every
        language so behavior is portable.
      </p>
      <div className="grid sm:grid-cols-2 gap-4">
        {SDKS.map((s) => (
          <Link
            key={s.href}
            href={s.href}
            className="block rounded-lg border border-zinc-800 bg-zinc-900/40 p-5 hover:border-zinc-700 hover:bg-zinc-900/70 transition-colors"
          >
            <p className="text-sm uppercase tracking-widest text-zinc-500 mb-1">{s.lang}</p>
            <p className="text-base font-medium text-zinc-100 mb-2 font-mono break-all">{s.pkg}</p>
            <p className="text-sm text-zinc-400 mb-4">{s.blurb}</p>
            <p className="text-xs font-mono text-sky-300/80">{s.install}</p>
          </Link>
        ))}
      </div>
    </div>
  );
}
