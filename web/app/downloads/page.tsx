import Link from "next/link";

export const metadata = { title: "Downloads" };

const SPEC = [
  {
    title: "OpenAPI spec (YAML)",
    href: "/openapi.yaml",
    blurb: "The source of truth. Feed it into your own generator, lint with Spectral, import into Postman/Insomnia.",
    ext: ".yaml",
  },
];

const SDK_LINKS = [
  {
    title: "TypeScript SDK",
    blurb: "npm i @philiprehberger/webhook-relay-client",
    href: "https://github.com/philiprehberger/webhook-relay/tree/main/sdks/typescript",
  },
  {
    title: "PHP SDK",
    blurb: "composer require philiprehberger/webhook-relay-client",
    href: "https://github.com/philiprehberger/webhook-relay/tree/main/sdks/php",
  },
  {
    title: "Python SDK",
    blurb: "pip install webhook-relay-client",
    href: "https://github.com/philiprehberger/webhook-relay/tree/main/sdks/python",
  },
  {
    title: "Go SDK",
    blurb: "go get github.com/philiprehberger/webhook-relay/sdks/go",
    href: "https://github.com/philiprehberger/webhook-relay/tree/main/sdks/go",
  },
];

export default function Downloads() {
  return (
    <div className="mx-auto max-w-5xl px-6 py-16">
      <p className="text-xs uppercase tracking-widest text-sky-300/80 mb-4">Downloads</p>
      <h1 className="text-3xl sm:text-4xl font-semibold tracking-tight mb-4">
        Spec, SDK source, and Postman collection
      </h1>
      <p className="text-zinc-400 max-w-2xl mb-12">
        Everything you need to integrate without leaving your editor. The
        OpenAPI spec is the source of truth for every SDK in this repo and
        every code sample on this site.
      </p>

      <section className="mb-12">
        <h2 className="text-xl font-semibold mb-4">API specification</h2>
        <div className="grid sm:grid-cols-2 gap-3">
          {SPEC.map((s) => (
            <a
              key={s.href}
              href={s.href}
              download
              className="block rounded-lg border border-zinc-800 bg-zinc-900/40 p-5 hover:border-zinc-700 hover:bg-zinc-900/70 transition-colors"
            >
              <p className="text-sm font-medium text-zinc-100 mb-1">
                {s.title}{" "}
                <span className="text-xs text-zinc-500 font-mono">{s.ext}</span>
              </p>
              <p className="text-sm text-zinc-400">{s.blurb}</p>
              <p className="text-xs text-sky-300/80 mt-3">Download →</p>
            </a>
          ))}
        </div>
      </section>

      <section>
        <h2 className="text-xl font-semibold mb-4">SDK source</h2>
        <div className="grid sm:grid-cols-2 gap-3">
          {SDK_LINKS.map((s) => (
            <Link
              key={s.href}
              href={s.href}
              className="block rounded-lg border border-zinc-800 bg-zinc-900/40 p-5 hover:border-zinc-700 hover:bg-zinc-900/70 transition-colors"
            >
              <p className="text-sm font-medium text-zinc-100 mb-1">{s.title}</p>
              <p className="text-xs font-mono text-zinc-400">{s.blurb}</p>
              <p className="text-xs text-sky-300/80 mt-3">View on GitHub →</p>
            </Link>
          ))}
        </div>
      </section>

      <section className="mt-12 rounded-md border border-zinc-800 bg-zinc-900/30 p-5">
        <p className="text-sm text-zinc-300">
          A Postman collection auto-generated from the spec and a PDF
          reference are on the roadmap.{" "}
          <Link href="/reference" className="text-sky-300 hover:underline underline-offset-4">
            Use the interactive reference
          </Link>{" "}
          in the meantime.
        </p>
      </section>
    </div>
  );
}
