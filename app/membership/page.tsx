import type { Metadata } from "next";
import { CheckCircle2, Crown, LockKeyhole, Sparkles } from "lucide-react";
import { PageHeader } from "@/components/PageHeader";
import { Button } from "@/components/ui/Button";
import { getCurrentUser } from "@/lib/auth";
import { membershipLabel, money } from "@/lib/format";

export const metadata: Metadata = {
  title: "Membership",
  description: "Free, Premium and Premium Plus membership plans for scholarships, jobs and application support."
};

const plans = [
  {
    id: "free",
    name: "Free",
    price: 0,
    description: "For exploring active opportunities and preparing your first applications.",
    cta: "Start Free",
    href: "/register",
    features: ["Browse free scholarships", "Browse free jobs", "Create application tracker", "Official source links", "Opportunity alerts"]
  },
  {
    id: "premium",
    name: "Premium",
    price: 9.99,
    description: "For serious applicants who want richer listings and guided application structure.",
    cta: "Upgrade to Premium",
    href: "/api/payments/start?plan=premium_monthly",
    featured: true,
    features: ["Everything in Free", "Premium scholarships", "Premium jobs", "Document upload support", "Priority email support", "Application status tracking"]
  },
  {
    id: "premium-plus",
    name: "Premium Plus",
    price: 19.99,
    description: "For competitive applicants who want the strongest access and human support layer.",
    cta: "Get Premium Plus",
    href: "/api/payments/start?plan=premium_plus_monthly",
    gold: true,
    features: ["Everything in Premium", "Premium Plus listings", "CV and cover letter review", "Scholarship support priority", "WhatsApp direct support", "Dedicated application guidance"]
  }
];

export default async function MembershipPage() {
  const user = await getCurrentUser();

  return (
    <>
      <PageHeader
        eyebrow="Membership"
        title="A logical upgrade path for serious applicants"
        description="The free plan stays useful. Premium and Premium Plus unlock higher-value opportunities, better support and a more guided application experience."
      />
      <section className="py-12">
        <div className="container-page">
          {user ? (
            <div className="mb-7 rounded-lg border border-slate-200 bg-white p-4 text-sm text-slate-700">
              Current plan: <strong className="text-navy">{membershipLabel(user.membershipType)}</strong>
            </div>
          ) : null}

          <div className="grid gap-5 lg:grid-cols-3">
            {plans.map((plan) => (
              <article
                key={plan.id}
                id={plan.id}
                className={`relative rounded-lg border bg-white p-6 premium-shadow ${
                  plan.featured ? "border-navy" : plan.gold ? "border-gold" : "border-slate-200"
                }`}
              >
                {plan.featured ? (
                  <div className="absolute -top-3 left-6 rounded-md bg-navy px-3 py-1 text-xs font-bold uppercase tracking-wide text-white">Most popular</div>
                ) : null}
                <div className="mb-5 flex items-center justify-between">
                  <h2 className="font-heading text-2xl font-extrabold text-navy">{plan.name}</h2>
                  {plan.gold ? <Crown className="text-gold" /> : plan.featured ? <Sparkles className="text-navy" /> : <LockKeyhole className="text-slate-400" />}
                </div>
                <div className="mb-2 font-heading text-4xl font-extrabold text-navy">
                  {plan.price === 0 ? "$0" : money(plan.price)}
                  {plan.price > 0 ? <span className="text-base font-semibold text-slate-500"> / mo</span> : null}
                </div>
                <p className="mb-6 min-h-16 text-sm leading-6 text-slate-600">{plan.description}</p>
                <ul className="mb-7 space-y-3">
                  {plan.features.map((feature) => (
                    <li key={feature} className="flex items-start gap-2 text-sm text-slate-700">
                      <CheckCircle2 size={17} className="mt-0.5 shrink-0 text-emerald-600" /> {feature}
                    </li>
                  ))}
                </ul>
                <Button href={plan.price === 0 && user ? "/dashboard" : plan.href} variant={plan.gold ? "gold" : plan.featured ? "primary" : "outline"} className="w-full">
                  {plan.price === 0 && user ? "Go to Dashboard" : plan.cta}
                </Button>
              </article>
            ))}
          </div>

          <div className="mt-8 grid gap-5 md:grid-cols-2">
            <SupportBox
              title="Scholarship Application Support"
              price={49.99}
              body="Statement of purpose guidance, document review, reference letter planning and deadline management."
            />
            <SupportBox
              title="Visa Application Support"
              price={79.99}
              body="Country-specific document review, application ordering, financial proof guidance and interview preparation."
            />
          </div>
        </div>
      </section>
    </>
  );
}

function SupportBox({ title, price, body }: { title: string; price: number; body: string }) {
  return (
    <div className="rounded-lg border border-slate-200 bg-white p-6">
      <p className="font-accent text-xs font-bold uppercase tracking-[0.24em] text-gold">One-time support</p>
      <h2 className="mt-2 font-heading text-xl font-extrabold text-navy">{title}</h2>
      <p className="mt-2 text-sm leading-6 text-slate-600">{body}</p>
      <div className="mt-5 flex items-center justify-between">
        <span className="font-heading text-3xl font-extrabold text-navy">{money(price)}</span>
        <Button href="/dashboard" variant="outline">Request Support</Button>
      </div>
    </div>
  );
}
