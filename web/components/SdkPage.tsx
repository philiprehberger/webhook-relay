import Link from "next/link";
import { CodeBlock } from "./CodeBlock";

export type SdkSnippets = {
  lang: string;
  pkg: string;
  install: string;
  sourceUrl: string;
  send: string;
  sendLang: string;
  verify: string;
  verifyLang: string;
  notes?: React.ReactNode;
};

export function SdkPage({ snippets }: { snippets: SdkSnippets }) {
  return (
    <div className="mx-auto max-w-3xl px-6 py-16">
      <p className="text-xs uppercase tracking-widest text-sky-300/80 mb-3">{snippets.lang} SDK</p>
      <h1 className="text-3xl sm:text-4xl font-semibold tracking-tight mb-2">{snippets.pkg}</h1>
      <p className="text-zinc-400 mb-10">
        Generated client + hand-written signature verifier.{" "}
        <Link href={snippets.sourceUrl} className="text-sky-300 hover:underline underline-offset-4">
          View source on GitHub
        </Link>
        .
      </p>

      <h2 className="text-xl font-semibold mt-4 mb-3">Install</h2>
      <CodeBlock language="shell">{snippets.install}</CodeBlock>

      <h2 className="text-xl font-semibold mt-10 mb-3">Send an event</h2>
      <CodeBlock language={snippets.sendLang}>{snippets.send}</CodeBlock>

      <h2 className="text-xl font-semibold mt-10 mb-3">Verify on the receiver</h2>
      <CodeBlock language={snippets.verifyLang}>{snippets.verify}</CodeBlock>

      {snippets.notes && (
        <div className="mt-10 rounded-md border border-zinc-800 bg-zinc-900/40 p-5 text-sm text-zinc-300 leading-relaxed">
          {snippets.notes}
        </div>
      )}

      <div className="mt-12 flex gap-3 text-sm">
        <Link
          href="/reference"
          className="rounded-md border border-zinc-700 hover:border-zinc-500 px-3 py-1.5 transition-colors"
        >
          API reference
        </Link>
        <Link
          href="/downloads"
          className="rounded-md border border-zinc-800 hover:border-zinc-600 hover:text-zinc-200 px-3 py-1.5 text-zinc-400 transition-colors"
        >
          Download source
        </Link>
      </div>
    </div>
  );
}
