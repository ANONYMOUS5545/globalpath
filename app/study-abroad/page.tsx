import type { Metadata } from "next";
import { BookOpenCheck, CalendarCheck2, FileText, GraduationCap, Landmark, Languages, Plane, SearchCheck } from "lucide-react";
import type { LucideIcon } from "lucide-react";
import { PageHeader } from "@/components/PageHeader";
import { Button } from "@/components/ui/Button";

export const metadata: Metadata = {
  title: "Study Abroad",
  description: "Study abroad planning, university shortlisting, document preparation, language training and visa readiness."
};

const pathwayItems: Array<[LucideIcon, string, string]> = [
  [SearchCheck, "Programme Shortlisting", "Match target countries, degree levels, budget, scholarship profile and intake timing."],
  [GraduationCap, "Scholarship Strategy", "Prioritize active funding routes and premium opportunities with strong applicant fit."],
  [FileText, "Application Pack", "Prepare CVs, statements, transcripts, references and proof documents in a clean sequence."],
  [Languages, "Language Preparation", "Connect German, French, Dutch, Swedish or English preparation to the destination."],
  [Landmark, "Visa Readiness", "Plan financial proof, document ordering, appointments and interview confidence."],
  [CalendarCheck2, "Deadline Management", "Track intake windows, scholarship cutoffs, language exams and visa milestones."]
];

const destinations = [
  "Germany",
  "France",
  "Netherlands",
  "Sweden",
  "Norway",
  "Finland",
  "Belgium",
  "Austria",
  "Switzerland",
  "Italy",
  "Spain",
  "Denmark",
  "Ireland",
  "Poland"
];

export default function StudyAbroadPage() {
  return (
    <>
      <PageHeader
        eyebrow="Study abroad"
        title="Plan your study abroad pathway with fewer loose ends"
        description="Bring scholarships, programme selection, documents, language training and visa preparation into one organized application journey."
      />

      <section className="py-12">
        <div className="container-page grid gap-5 md:grid-cols-2 xl:grid-cols-3">
          {pathwayItems.map(([Icon, title, body]) => (
            <article key={title} className="rounded-lg border border-slate-200 bg-white p-6 premium-shadow">
              <Icon className="mb-4 text-navy" size={26} />
              <h2 className="font-heading text-xl font-extrabold text-navy">{title}</h2>
              <p className="mt-2 text-sm leading-6 text-slate-600">{body}</p>
            </article>
          ))}
        </div>
      </section>

      <section className="bg-white py-12">
        <div className="container-page grid gap-8 lg:grid-cols-[0.9fr_1.1fr] lg:items-start">
          <div>
            <p className="font-accent text-xs font-bold uppercase tracking-[0.28em] text-gold">European coverage</p>
            <h2 className="mt-2 font-heading text-3xl font-extrabold text-navy">Balanced destination planning</h2>
            <p className="mt-3 leading-7 text-slate-600">
              The study pathway intentionally covers a wide European region instead of repeatedly pushing only a few countries. This keeps scholarship discovery and language preparation more strategic.
            </p>
            <div className="mt-6 flex flex-wrap gap-3">
              <Button href="/scholarships">Find Scholarships</Button>
              <Button href="/language-classes" variant="outline">Language Training</Button>
            </div>
          </div>
          <div className="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
            {destinations.map((destination) => (
              <div key={destination} className="rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-700">
                {destination}
              </div>
            ))}
          </div>
        </div>
      </section>

      <section className="py-12">
        <div className="container-page rounded-lg bg-navy p-8 text-white md:flex md:items-center md:justify-between md:gap-8">
          <div>
            <p className="font-accent text-xs font-bold uppercase tracking-[0.28em] text-gold">Next step</p>
            <h2 className="mt-2 font-heading text-3xl font-extrabold">Start with your target country and intake</h2>
            <p className="mt-2 max-w-2xl text-white/72">Create an account, save opportunities and use the dashboard to track applications and documents.</p>
          </div>
          <Button href="/register" variant="gold" className="mt-6 md:mt-0">
            <Plane size={16} /> Create Account
          </Button>
        </div>
      </section>
    </>
  );
}
