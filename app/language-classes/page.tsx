import type { Metadata } from "next";
import { CalendarDays, CheckCircle2, Clock, Languages, MessageCircle, MonitorPlay, UsersRound } from "lucide-react";
import type { LucideIcon } from "lucide-react";
import { PageHeader } from "@/components/PageHeader";
import { Button } from "@/components/ui/Button";
import { Badge } from "@/components/ui/Badge";
import { brand } from "@/lib/seed-data";

export const metadata: Metadata = {
  title: "Language Training",
  description: "Online German, French, Dutch and English exam preparation for study abroad, work and visa readiness."
};

const courses = [
  {
    title: "German for Study & Visa Interviews",
    levels: "A1 to B2",
    schedule: "Weekday evenings and Saturday cohorts",
    bestFor: "Germany, Austria and Switzerland applicants",
    outcomes: ["Embassy interview confidence", "University vocabulary", "A1-B2 progression plan", "Goethe/TELC exam preparation"]
  },
  {
    title: "French for Campus and Professional Mobility",
    levels: "A1 to B2",
    schedule: "Live online small groups",
    bestFor: "France, Belgium, Canada and NGO applicants",
    outcomes: ["Campus communication", "Professional introductions", "DELF preparation", "Francophone visa confidence"]
  },
  {
    title: "English Exam Preparation",
    levels: "IELTS, TOEFL and Duolingo",
    schedule: "Diagnostic-led weekly classes",
    bestFor: "Scholarship, university and work applicants",
    outcomes: ["Writing task feedback", "Speaking practice", "Reading strategy", "Score-targeted study plan"]
  },
  {
    title: "Dutch Foundation Course",
    levels: "Starter to A2",
    schedule: "Weekend online classes",
    bestFor: "Netherlands and Belgium applicants",
    outcomes: ["Daily-life vocabulary", "Pronunciation foundation", "Study and work phrases", "Cultural orientation"]
  },
  {
    title: "Swedish Starter Course",
    levels: "Starter to A2",
    schedule: "Cohort-based online sessions",
    bestFor: "Sweden study and work applicants",
    outcomes: ["Introductory Swedish", "Interview basics", "Campus phrases", "Self-study roadmap"]
  },
  {
    title: "Application Interview English",
    levels: "Intermediate to advanced",
    schedule: "One-to-one coaching slots",
    bestFor: "Scholarship panels, job interviews and visa interviews",
    outcomes: ["Mock interviews", "Answer structure", "Confidence drills", "Professional vocabulary"]
  }
];

const steps = [
  "Take a short placement and goals assessment.",
  "Choose group classes or one-to-one coaching.",
  "Receive a weekly study plan and homework support.",
  "Prepare for the exam, interview or application milestone."
];

const trainingHighlights: Array<[LucideIcon, string, string]> = [
  [MonitorPlay, "Live Online", "Interactive classes, not static recordings."],
  [UsersRound, "Small Cohorts", "More speaking time and feedback."],
  [CalendarDays, "Application Timelines", "Lessons aligned with deadlines."],
  [MessageCircle, "Support", "WhatsApp follow-up for active learners."]
];

export default function LanguageClassesPage() {
  return (
    <>
      <PageHeader
        eyebrow="Language training"
        title="Online language preparation for study, work and visas"
        description="German, French, Dutch, Swedish and English preparation stay connected to the wider Global Path application workflow."
      />

      <section className="bg-white py-10">
        <div className="container-page grid gap-5 md:grid-cols-4">
          {trainingHighlights.map(([Icon, title, body]) => (
            <div key={title} className="rounded-lg border border-slate-200 bg-white p-5">
              <Icon className="mb-4 text-navy" size={24} />
              <h2 className="font-heading text-lg font-extrabold text-navy">{title}</h2>
              <p className="mt-2 text-sm leading-6 text-slate-600">{body}</p>
            </div>
          ))}
        </div>
      </section>

      <section className="py-12">
        <div className="container-page">
          <div className="mb-7 max-w-2xl">
            <p className="font-accent text-xs font-bold uppercase tracking-[0.28em] text-gold">Courses</p>
            <h2 className="mt-2 font-heading text-3xl font-extrabold text-navy">Choose the language path that matches your destination</h2>
          </div>
          <div className="grid gap-5 lg:grid-cols-3">
            {courses.map((course) => (
              <article key={course.title} className="rounded-lg border border-slate-200 bg-white p-6 premium-shadow">
                <div className="mb-4 flex items-start justify-between gap-3">
                  <Languages className="text-navy" size={26} />
                  <Badge tone="gold">{course.levels}</Badge>
                </div>
                <h3 className="font-heading text-xl font-extrabold text-navy">{course.title}</h3>
                <p className="mt-2 text-sm font-semibold text-slate-600">{course.bestFor}</p>
                <p className="mt-3 flex items-center gap-2 text-sm text-slate-500">
                  <Clock size={15} /> {course.schedule}
                </p>
                <ul className="mt-5 space-y-2">
                  {course.outcomes.map((outcome) => (
                    <li key={outcome} className="flex gap-2 text-sm leading-6 text-slate-700">
                      <CheckCircle2 className="mt-0.5 shrink-0 text-emerald-600" size={16} /> {outcome}
                    </li>
                  ))}
                </ul>
              </article>
            ))}
          </div>
        </div>
      </section>

      <section className="bg-white py-12">
        <div className="container-page grid gap-8 lg:grid-cols-[0.9fr_1.1fr] lg:items-center">
          <div>
            <p className="font-accent text-xs font-bold uppercase tracking-[0.28em] text-gold">How it works</p>
            <h2 className="mt-2 font-heading text-3xl font-extrabold text-navy">A practical class flow for real applications</h2>
            <p className="mt-3 leading-7 text-slate-600">
              Language training is connected to your target country, programme, visa path and interview requirements. The goal is not just grammar; it is readiness.
            </p>
          </div>
          <ol className="grid gap-3">
            {steps.map((step, index) => (
              <li key={step} className="flex gap-4 rounded-lg border border-slate-200 bg-slate-50 p-4">
                <span className="grid h-8 w-8 shrink-0 place-items-center rounded-md bg-navy font-heading text-sm font-extrabold text-white">{index + 1}</span>
                <span className="pt-1 text-sm font-semibold text-slate-700">{step}</span>
              </li>
            ))}
          </ol>
        </div>
      </section>

      <section className="py-12">
        <div className="container-page rounded-lg bg-navy p-8 text-white md:flex md:items-center md:justify-between md:gap-8">
          <div>
            <p className="font-accent text-xs font-bold uppercase tracking-[0.28em] text-gold">Enrollment</p>
            <h2 className="mt-2 font-heading text-3xl font-extrabold">Ask for the next language cohort</h2>
            <p className="mt-2 max-w-2xl text-white/72">Share your target country, deadline and current language level so the team can recommend the right class.</p>
          </div>
          <div className="mt-6 flex flex-wrap gap-3 md:mt-0">
            <Button href={`${brand.whatsappUrl}?text=Hello%20Global%20Path%20Africa%2C%20I%20want%20to%20join%20language%20training`} variant="gold">
              WhatsApp Enrollment
            </Button>
            <Button href="/contact" variant="outline" className="border-white/35 bg-white/10 text-white hover:bg-white/15">
              Contact Team
            </Button>
          </div>
        </div>
      </section>
    </>
  );
}
