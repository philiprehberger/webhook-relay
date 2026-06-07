import Link from "next/link";

const NAV = [
  { href: "/docs", label: "Docs" },
  { href: "/reference", label: "Reference" },
  { href: "/sdks", label: "SDKs" },
  { href: "/downloads", label: "Downloads" },
  { href: "/pricing", label: "Pricing" },
  { href: "/status", label: "Status" },
  { href: "/about", label: "About" },
];

export function TopNav() {
  return (
    <header className="border-b border-zinc-800/80 bg-zinc-950/80 backdrop-blur sticky top-0 z-40">
      <div className="mx-auto max-w-6xl px-6 h-14 flex items-center justify-between">
        <Link
          href="/"
          className="text-sm font-medium tracking-tight text-zinc-100 hover:text-sky-300 transition-colors"
        >
          Webhook Relay
        </Link>
        <nav className="hidden md:flex items-center gap-6 text-sm">
          {NAV.map((item) => (
            <Link
              key={item.href}
              href={item.href}
              className="text-zinc-400 hover:text-zinc-100 transition-colors"
            >
              {item.label}
            </Link>
          ))}
        </nav>
        <Link
          href="/docs/quickstart"
          className="text-sm rounded-md bg-sky-400/15 text-sky-200 hover:bg-sky-400/25 transition-colors px-3 py-1.5"
        >
          Quickstart
        </Link>
      </div>
    </header>
  );
}
