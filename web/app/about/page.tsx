import Link from "next/link";

export const metadata = { title: "About" };

export default function About() {
  return (
    <div className="mx-auto max-w-3xl px-6 py-16">
      <p className="text-xs uppercase tracking-widest text-sky-300/80 mb-4">About</p>
      <h1 className="text-3xl sm:text-4xl font-semibold tracking-tight mb-6">
        This is a portfolio demo.
      </h1>
      <div className="space-y-5 text-zinc-300 leading-relaxed max-w-2xl">
        <p>
          Webhook Relay is a real, live, working API. The dashboard is real
          Filament. The OpenAPI spec is hand-authored. The four SDKs are
          generated from that spec and the signature verifiers are
          hand-written for each language.
        </p>
        <p>
          It is not a commercial service. There is no SLA. The pricing page
          is mocked. If you sign up and route production traffic through
          it, expect to be disappointed.
        </p>
        <p>
          The reason it exists: I&apos;m{" "}
          <Link
            href="https://philiprehberger.com"
            className="text-sky-300 hover:underline underline-offset-4"
          >
            Philip Rehberger
          </Link>
          , and most engineering portfolios stop at the README. This is the
          demonstration that I can deliver the whole product surface a
          serious client expects — endpoints, ergonomics, reliability
          semantics, generated SDKs, a try-it console, an operator
          dashboard, and a deploy story — not just the API.
        </p>
        <p>
          If you have a webhook system you&apos;d like to look like this
          one — or you&apos;re shipping a different API and want the same
          shape applied to it — talk to me through{" "}
          <Link
            href="https://scopeforged.com"
            className="text-sky-300 hover:underline underline-offset-4"
          >
            ScopeForged
          </Link>
          .
        </p>
      </div>

      <div className="mt-12 grid sm:grid-cols-2 gap-3">
        <Link
          href="https://github.com/philiprehberger/webhook-relay"
          className="rounded-md border border-zinc-800 bg-zinc-900/40 p-5 hover:border-zinc-700 hover:bg-zinc-900/70 transition-colors"
        >
          <p className="text-sm font-medium text-zinc-100">Source on GitHub</p>
          <p className="text-xs text-zinc-500 mt-1">
            Full repo, public. Read the commits — they&apos;re the story.
          </p>
        </Link>
        <Link
          href="https://scopeforged.com"
          className="rounded-md border border-zinc-800 bg-zinc-900/40 p-5 hover:border-zinc-700 hover:bg-zinc-900/70 transition-colors"
        >
          <p className="text-sm font-medium text-zinc-100">ScopeForged</p>
          <p className="text-xs text-zinc-500 mt-1">
            The agency this build was made under. Custom dev work.
          </p>
        </Link>
      </div>
    </div>
  );
}
