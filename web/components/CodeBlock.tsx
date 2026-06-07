export function CodeBlock({
  language,
  children,
}: {
  language?: string;
  children: React.ReactNode;
}) {
  return (
    <div className="rounded-lg border border-zinc-800 bg-zinc-900/60 overflow-hidden my-4">
      {language && (
        <div className="px-4 py-2 border-b border-zinc-800/80 text-xs uppercase tracking-widest text-zinc-500">
          {language}
        </div>
      )}
      <pre className="px-4 py-3 text-sm font-mono text-zinc-200 overflow-x-auto">
        <code>{children}</code>
      </pre>
    </div>
  );
}

export function InlineCode({ children }: { children: React.ReactNode }) {
  return (
    <code className="text-sky-300 bg-sky-300/10 rounded px-1.5 py-0.5 text-[0.92em] font-mono">
      {children}
    </code>
  );
}
