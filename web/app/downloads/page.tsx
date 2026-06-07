import Link from "next/link";

export const metadata = { title: "Downloads" };

const VERSION = "0.4.0";

const SPEC = [
  {
    title: "OpenAPI 3.1 (YAML)",
    href: "/openapi.yaml",
    blurb: "Source of truth. Feed it into your own generator, lint with Spectral, import into Postman or Insomnia.",
    badge: ".yaml",
    size: "26 KB",
  },
  {
    title: "OpenAPI 3.1 (JSON)",
    href: "/openapi.json",
    blurb: "Same spec, JSON-encoded. Better for build tools that don't read YAML.",
    badge: ".json",
    size: "37 KB",
  },
  {
    title: "Postman collection",
    href: "/postman-collection.json",
    blurb: "Drop into Postman / Insomnia / Bruno. Includes every endpoint with example bodies. Set `Authorization: Bearer whk_...` as a collection-level header.",
    badge: ".json",
    size: "299 KB",
  },
  {
    title: "API reference",
    href: `/openapi-reference-${VERSION}.pdf`,
    blurb: "Print-ready PDF of the full reference, rendered from the Scalar view. Pre-rendered so you don't have to print the docs site yourself.",
    badge: ".pdf",
    size: "8.7 MB",
  },
];

const SDKS = [
  {
    title: "TypeScript SDK",
    version: VERSION,
    blurb: "Generated client + verifier helper. Node 18+, ESM.",
    href: `/sdks/webhook-relay-sdk-typescript-${VERSION}.zip`,
    install: "npm i @philiprehberger/webhook-relay-client",
    size: "61 KB",
  },
  {
    title: "PHP SDK",
    version: VERSION,
    blurb: "PSR-4 namespaced, Guzzle-based. PHP 8.1+.",
    href: `/sdks/webhook-relay-sdk-php-${VERSION}.zip`,
    install: "composer require philiprehberger/webhook-relay-client",
    size: "133 KB",
  },
  {
    title: "Python SDK",
    version: VERSION,
    blurb: "urllib3 + pydantic, Python 3.10+. pyproject.toml-based.",
    href: `/sdks/webhook-relay-sdk-python-${VERSION}.zip`,
    install: "pip install webhook-relay-client",
    size: "108 KB",
  },
  {
    title: "Go SDK",
    version: VERSION,
    blurb: "Zero runtime deps beyond stdlib. Go 1.22+.",
    href: `/sdks/webhook-relay-sdk-go-${VERSION}.zip`,
    install: "go get github.com/philiprehberger/webhook-relay/sdks/go",
    size: "91 KB",
  },
];

export default function Downloads() {
  return (
    <div className="mx-auto max-w-5xl px-6 py-16">
      <p className="text-xs uppercase tracking-widest text-sky-300/80 mb-4">Downloads</p>
      <h1 className="text-3xl sm:text-4xl font-semibold tracking-tight mb-4">
        Spec, SDKs, Postman — every deliverable, downloadable
      </h1>
      <p className="text-zinc-400 max-w-2xl mb-12">
        Everything regenerates from the same OpenAPI spec. If you only grab
        one file, take the YAML — it&apos;s the source every other artifact
        on this page was built from.
      </p>

      <section className="mb-12">
        <h2 className="text-xl font-semibold mb-4">API specification</h2>
        <div className="grid sm:grid-cols-2 lg:grid-cols-4 gap-3">
          {SPEC.map((s) => (
            <a
              key={s.href}
              href={s.href}
              download
              className="flex flex-col rounded-lg border border-zinc-800 bg-zinc-900/40 p-5 hover:border-zinc-700 hover:bg-zinc-900/70 transition-colors"
            >
              <div className="flex items-baseline justify-between mb-2">
                <p className="text-sm font-medium text-zinc-100">{s.title}</p>
                <span className="text-[10px] uppercase tracking-widest text-zinc-500 font-mono">
                  {s.badge}
                </span>
              </div>
              <p className="text-sm text-zinc-400 leading-relaxed flex-1">{s.blurb}</p>
              <div className="mt-4 flex items-center justify-between text-xs">
                <span className="text-zinc-500">{s.size}</span>
                <span className="text-sky-300/80">Download →</span>
              </div>
            </a>
          ))}
        </div>
      </section>

      <section className="mb-12">
        <h2 className="text-xl font-semibold mb-4">
          SDK source archives{" "}
          <span className="text-sm font-normal text-zinc-500">v{VERSION}</span>
        </h2>
        <div className="grid sm:grid-cols-2 gap-3">
          {SDKS.map((s) => (
            <div
              key={s.href}
              className="rounded-lg border border-zinc-800 bg-zinc-900/40 p-5 hover:border-zinc-700 transition-colors"
            >
              <div className="flex items-baseline justify-between mb-1">
                <p className="text-sm font-medium text-zinc-100">{s.title}</p>
                <span className="text-xs text-zinc-500">{s.size}</span>
              </div>
              <p className="text-sm text-zinc-400 leading-relaxed mb-3">{s.blurb}</p>
              <p className="text-xs font-mono text-sky-300/80 mb-4">{s.install}</p>
              <div className="flex gap-2 text-xs">
                <a
                  href={s.href}
                  download
                  className="rounded-md bg-sky-400/15 text-sky-200 hover:bg-sky-400/25 px-3 py-1.5 transition-colors"
                >
                  Download zip
                </a>
                <Link
                  href={`https://github.com/philiprehberger/webhook-relay/tree/main/sdks/${s.title.split(" ")[0].toLowerCase()}`}
                  className="rounded-md border border-zinc-800 hover:border-zinc-600 px-3 py-1.5 text-zinc-400 hover:text-zinc-200 transition-colors"
                >
                  View on GitHub
                </Link>
              </div>
            </div>
          ))}
        </div>
      </section>

      <section className="rounded-md border border-zinc-800 bg-zinc-900/30 p-5">
        <p className="text-sm text-zinc-300">
          <strong className="text-zinc-100">Regenerate locally?</strong>{" "}
          Clone the repo and run{" "}
          <code className="text-sky-200 bg-sky-300/10 px-1.5 py-0.5 rounded text-xs font-mono">
            npm run sdk:zip
          </code>
          {" "}for the SDK archives and{" "}
          <code className="text-sky-200 bg-sky-300/10 px-1.5 py-0.5 rounded text-xs font-mono">
            npm run docs:pdf
          </code>
          {" "}for the reference PDF. Every artifact on this page is reproducible.
        </p>
      </section>
    </div>
  );
}
