import Link from "next/link";

export function Footer() {
  return (
    <footer className="mt-24 border-t border-zinc-800 py-12">
      <div className="mx-auto max-w-6xl px-6 grid grid-cols-2 md:grid-cols-4 gap-8 text-sm">
        <div className="col-span-2">
          <p className="text-zinc-100 font-medium mb-2">Webhook Relay</p>
          <p className="text-zinc-500 max-w-xs leading-relaxed">
            Production-shaped webhook delivery infrastructure. A portfolio
            build by{" "}
            <Link
              href="https://philiprehberger.com"
              className="text-zinc-300 hover:text-sky-300 underline-offset-4 hover:underline"
            >
              Philip Rehberger
            </Link>
            . Not a production service.
          </p>
        </div>
        <div>
          <p className="text-zinc-300 mb-3">Product</p>
          <ul className="space-y-2 text-zinc-500">
            <li><FooterLink href="/docs">Docs</FooterLink></li>
            <li><FooterLink href="/reference">Reference</FooterLink></li>
            <li><FooterLink href="/sdks">SDKs</FooterLink></li>
            <li><FooterLink href="/downloads">Downloads</FooterLink></li>
          </ul>
        </div>
        <div>
          <p className="text-zinc-300 mb-3">Project</p>
          <ul className="space-y-2 text-zinc-500">
            <li><FooterLink href="/about">About</FooterLink></li>
            <li><FooterLink href="/status">Status</FooterLink></li>
            <li>
              <FooterLink href="https://github.com/philiprehberger/webhook-relay">
                GitHub
              </FooterLink>
            </li>
            <li>
              <FooterLink href="https://scopeforged.com">ScopeForged</FooterLink>
            </li>
          </ul>
        </div>
      </div>
    </footer>
  );
}

function FooterLink({ href, children }: { href: string; children: React.ReactNode }) {
  return (
    <Link href={href} className="hover:text-zinc-200 transition-colors">
      {children}
    </Link>
  );
}
