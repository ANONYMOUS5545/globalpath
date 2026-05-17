import type { Metadata } from "next";
import { Globe2, ShieldCheck, UsersRound } from "lucide-react";
import type { LucideIcon } from "lucide-react";
import { PageHeader } from "@/components/PageHeader";

export const metadata: Metadata = {
  title: "About"
};

const values: Array<[LucideIcon, string, string]> = [
  [Globe2, "Global access", "Scholarships, study pathways and jobs from official sources across reputable institutions."],
  [ShieldCheck, "Trust first", "Active deadline filtering, secure accounts and clear application records keep the workflow reliable."],
  [UsersRound, "Human support", "Premium plans add practical guidance for documents, CVs, applications and next steps."]
];

export default function AboutPage() {
  return (
    <>
      <PageHeader
        eyebrow="About"
        title="A premium pathway platform for African talent"
        description="Global Path Africa helps students and professionals move from opportunity discovery to organized, supported applications."
      />
      <section className="py-12">
        <div className="container-page grid gap-6 md:grid-cols-3">
          {values.map(([Icon, title, body]) => (
            <article key={title} className="rounded-lg border border-slate-200 bg-white p-6">
              <Icon className="mb-4 text-navy" size={26} />
              <h2 className="font-heading text-xl font-extrabold text-navy">{title}</h2>
              <p className="mt-2 text-sm leading-6 text-slate-600">{body}</p>
            </article>
          ))}
        </div>
      </section>
    </>
  );
}
