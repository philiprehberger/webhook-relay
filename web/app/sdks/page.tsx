import Link from "next/link";

export const metadata = { title: "SDKs" };

const SDKS = [
  {
    href: "/sdks/typescript",
    lang: "TypeScript",
    install: "npm i @philiprehberger/webhook-relay-client",
    pkg: "@philiprehberger/webhook-relay-client",
    badgeAlt: "npm version",
    badgeSrc:
      "https://img.shields.io/npm/v/@philiprehberger/webhook-relay-client.svg?label=npm&color=10b981",
    blurb: "Node 18+. Dependency-free. Works in serverless runtimes (Vercel, Cloudflare, Lambda).",
  },
  {
    href: "/sdks/php",
    lang: "PHP",
    install: "composer require philiprehberger/php-webhook-relay-client",
    pkg: "philiprehberger/php-webhook-relay-client",
    badgeAlt: "Packagist version",
    badgeSrc:
      "https://img.shields.io/packagist/v/philiprehberger/php-webhook-relay-client.svg?label=packagist&color=10b981",
    blurb: "PHP 8.2+. curl + json extensions only — no Guzzle / PSR-18 plumbing.",
  },
  {
    href: "/sdks/python",
    lang: "Python",
    install: "pip install philiprehberger-webhook-relay-client",
    pkg: "philiprehberger-webhook-relay-client",
    badgeAlt: "PyPI version",
    badgeSrc:
      "https://img.shields.io/pypi/v/philiprehberger-webhook-relay-client.svg?label=pypi&color=10b981",
    blurb: "Python 3.10+. urllib-based, PEP 561 typed. FastAPI, Django, Flask, anything.",
  },
  {
    href: "/sdks/go",
    lang: "Go",
    install: "go get github.com/philiprehberger/go-webhook-relay-client",
    pkg: "github.com/philiprehberger/go-webhook-relay-client",
    badgeAlt: "Go Reference",
    badgeSrc:
      "https://pkg.go.dev/badge/github.com/philiprehberger/go-webhook-relay-client.svg",
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
        Published to npm, Packagist, PyPI, and the Go module proxy. Each
        package ships a hand-tuned signature verifier for the receiver
        side and a small dependency-free client for the sender side.
        Identical contract across every language so behavior is portable.
      </p>
      <div className="grid sm:grid-cols-2 gap-4">
        {SDKS.map((s) => (
          <Link
            key={s.href}
            href={s.href}
            className="block rounded-lg border border-zinc-800 bg-zinc-900/40 p-5 hover:border-zinc-700 hover:bg-zinc-900/70 transition-colors"
          >
            <div className="flex items-baseline justify-between mb-1">
              <p className="text-sm uppercase tracking-widest text-zinc-500">{s.lang}</p>
              {/* eslint-disable-next-line @next/next/no-img-element */}
              <img src={s.badgeSrc} alt={s.badgeAlt} className="h-4" />
            </div>
            <p className="text-base font-medium text-zinc-100 mb-2 font-mono break-all">{s.pkg}</p>
            <p className="text-sm text-zinc-400 mb-4">{s.blurb}</p>
            <p className="text-xs font-mono text-sky-300/80">{s.install}</p>
          </Link>
        ))}
      </div>
    </div>
  );
}
