export const metadata = { title: "Pricing" };

const TIERS = [
  {
    name: "Hobby",
    price: "$0",
    period: "forever",
    blurb: "For trying it out and side projects.",
    features: [
      "1 workspace",
      "10k events / month",
      "1k req/s rate limit",
      "7-day delivery log retention",
      "Community support",
    ],
    cta: "Try the demo",
    accent: false,
  },
  {
    name: "Starter",
    price: "$49",
    period: "/mo",
    blurb: "For an early-stage SaaS shipping their first integrations.",
    features: [
      "3 workspaces",
      "200k events / month",
      "Higher rate limits",
      "30-day delivery log retention",
      "Email support",
    ],
    cta: "Talk to sales",
    accent: true,
  },
  {
    name: "Growth",
    price: "$249",
    period: "/mo",
    blurb: "For teams running webhook delivery at scale.",
    features: [
      "Unlimited workspaces",
      "2M events / month",
      "Custom backoff schedules",
      "90-day delivery log retention",
      "Priority Slack support",
    ],
    cta: "Talk to sales",
    accent: false,
  },
  {
    name: "Enterprise",
    price: "Custom",
    period: "",
    blurb: "For enterprise contracts with on-prem or hybrid needs.",
    features: [
      "Self-hosted deployment",
      "Custom retention + SLAs",
      "Dedicated support engineer",
      "SOC2 documentation",
      "Single-tenant infrastructure",
    ],
    cta: "Talk to sales",
    accent: false,
  },
];

export default function Pricing() {
  return (
    <div className="mx-auto max-w-6xl px-6 py-16">
      <div className="text-center mb-12">
        <p className="text-xs uppercase tracking-widest text-sky-300/80 mb-3">Pricing</p>
        <h1 className="text-3xl sm:text-4xl font-semibold tracking-tight mb-3">
          Tiers calibrated for real usage
        </h1>
        <p className="text-zinc-400 max-w-2xl mx-auto">
          Volume-based, not seat-based. Failed deliveries don&apos;t count
          against your event quota — pay for the work that succeeded.
        </p>
      </div>
      <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-4">
        {TIERS.map((tier) => (
          <div
            key={tier.name}
            className={`rounded-lg border p-6 flex flex-col ${
              tier.accent
                ? "border-sky-400/30 bg-sky-400/5"
                : "border-zinc-800 bg-zinc-900/40"
            }`}
          >
            <p className="text-sm uppercase tracking-widest text-zinc-500 mb-2">
              {tier.name}
            </p>
            <p className="text-3xl font-semibold text-zinc-100">
              {tier.price}
              <span className="text-base text-zinc-500 font-normal">{tier.period}</span>
            </p>
            <p className="text-sm text-zinc-400 mt-3 mb-6 leading-relaxed">{tier.blurb}</p>
            <ul className="space-y-2 text-sm text-zinc-300 mb-6">
              {tier.features.map((f) => (
                <li key={f} className="flex gap-2">
                  <span className="text-sky-300 mt-0.5">·</span>
                  <span>{f}</span>
                </li>
              ))}
            </ul>
            <div className="mt-auto">
              <button
                disabled
                className={`w-full rounded-md py-2 text-sm font-medium cursor-not-allowed ${
                  tier.accent
                    ? "bg-sky-400/20 text-sky-200"
                    : "bg-zinc-800/50 text-zinc-400"
                }`}
              >
                {tier.cta}
              </button>
            </div>
          </div>
        ))}
      </div>
      <p className="mt-12 text-center text-xs text-zinc-500 max-w-2xl mx-auto">
        These tiers are mocked for the demo. Webhook Relay is a portfolio
        build, not a commercial product. See <a href="/about" className="text-sky-300/80 hover:text-sky-300 underline-offset-4 hover:underline">About</a>.
      </p>
    </div>
  );
}
