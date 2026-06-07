import Link from "next/link";

const SECTIONS = [
  {
    title: "Getting started",
    items: [
      { href: "/docs", label: "Overview" },
      { href: "/docs/quickstart", label: "Quickstart" },
    ],
  },
  {
    title: "Concepts",
    items: [
      { href: "/docs/concepts/signing", label: "HMAC signing" },
      { href: "/docs/concepts/idempotency", label: "Idempotency keys" },
      { href: "/docs/concepts/retries", label: "Retries + dead-letter" },
      { href: "/docs/concepts/filtering", label: "Event filters" },
      { href: "/docs/concepts/receivers", label: "Receiver patterns" },
    ],
  },
  {
    title: "Reference",
    items: [
      { href: "/reference", label: "API reference + try-it" },
      { href: "/sdks", label: "SDKs" },
      { href: "/downloads", label: "Downloads" },
    ],
  },
];

export function DocsLayout({ children }: { children: React.ReactNode }) {
  return (
    <div className="mx-auto max-w-6xl px-6 py-10 grid lg:grid-cols-[220px_1fr] gap-10">
      <aside className="hidden lg:block">
        <nav className="sticky top-20 space-y-7">
          {SECTIONS.map((section) => (
            <div key={section.title}>
              <p className="text-xs uppercase tracking-widest text-zinc-500 mb-3">
                {section.title}
              </p>
              <ul className="space-y-2 text-sm">
                {section.items.map((item) => (
                  <li key={item.href}>
                    <Link
                      href={item.href}
                      className="text-zinc-400 hover:text-zinc-100 transition-colors"
                    >
                      {item.label}
                    </Link>
                  </li>
                ))}
              </ul>
            </div>
          ))}
        </nav>
      </aside>
      <article className="prose prose-invert prose-zinc max-w-none prose-headings:font-semibold prose-h1:text-3xl prose-h2:text-xl prose-h2:mt-12 prose-h3:text-lg prose-a:text-sky-300 prose-a:no-underline hover:prose-a:underline prose-code:text-sky-200 prose-code:bg-sky-300/10 prose-code:px-1 prose-code:py-0.5 prose-code:rounded prose-code:before:hidden prose-code:after:hidden prose-pre:bg-zinc-900/60 prose-pre:border prose-pre:border-zinc-800">
        {children}
      </article>
    </div>
  );
}
