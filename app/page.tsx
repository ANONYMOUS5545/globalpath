import Image from "next/image";
import Link from "next/link";
import { ArrowRight, BookOpenText, BriefcaseBusiness, CheckCircle2, GraduationCap, IdCard, Languages, Plane, ShieldCheck, Sparkles, UsersRound } from "lucide-react";
import { Button } from "@/components/ui/Button";
import { ScholarshipCard } from "@/components/ScholarshipCard";
import { JobCard } from "@/components/JobCard";
import { getCurrentUser } from "@/lib/auth";
import { getJobs, getPlatformStats, getScholarships } from "@/lib/data";
import type { LucideIcon } from "lucide-react";

const featureCards: Array<[LucideIcon, string, string]> = [
  [GraduationCap, "Verified scholarship access", "Only active opportunities are displayed, with clear deadlines and Global Path application tracking."],
  [BriefcaseBusiness, "Remote and onsite jobs", "Jobs are separated into useful categories with plan-aware access and direct applications."],
  [Languages, "Language training", "German, French, Dutch and English preparation stay part of the study and work pathway."],
  [IdCard, "Visa guidance", "Visa support keeps document order, proof preparation and interview readiness in the same platform."],
  [Plane, "Study abroad planning", "Country and programme planning connects scholarships, applications, language prep and visa readiness."],
  [ShieldCheck, "Secure application tracking", "Accounts, uploads, status updates and admin review are handled through secure routes."]
];

const pathwayCards: Array<[LucideIcon, string, string, string]> = [
  [BookOpenText, "Language Training", "Live online German, French, Dutch and English exam preparation for study, work and visa confidence.", "/language-classes"],
  [Plane, "Study Abroad", "Country shortlisting, programme readiness, document planning and application timelines.", "/study-abroad"],
  [IdCard, "Visa Help", "Document ordering, financial proof preparation and interview readiness for students and professionals.", "/visas"]
];

export default async function HomePage() {
  const user = await getCurrentUser();
  const [stats, scholarships, jobs] = await Promise.all([
    getPlatformStats(),
    getScholarships({}, user),
    getJobs({}, user)
  ]);

  return (
    <>
      <section className="relative isolate overflow-hidden bg-navy text-white">
        <Image
          src="https://images.unsplash.com/photo-1523580846011-d3a5bc25702b?auto=format&fit=crop&w=1800&q=80"
          alt="International students walking across a university campus"
          fill
          priority
          className="absolute inset-0 -z-20 object-cover"
        />
        <div className="absolute inset-0 -z-10 bg-navy/82" />
        <div className="container-page grid min-h-[680px] items-center py-16">
          <div className="max-w-3xl">
            <div className="mb-5 inline-flex items-center gap-2 rounded-md border border-gold/40 bg-white/8 px-3 py-2 font-accent text-xs font-bold uppercase tracking-[0.24em] text-gold">
              <Sparkles size={15} /> Premium global pathways
            </div>
            <h1 className="text-balance font-heading text-5xl font-extrabold leading-[1.05] md:text-7xl">
              Global Path Africa
            </h1>
            <p className="mt-6 max-w-2xl text-lg leading-8 text-white/78">
              Verified scholarships, international jobs and guided applications for African students and professionals ready to study, work and advance globally.
            </p>
            <div className="mt-8 flex flex-wrap gap-3">
              <Button href="/scholarships" variant="gold">
                Explore Scholarships <ArrowRight size={17} />
              </Button>
              <Button href="/jobs" variant="outline" className="border-white/35 bg-white/10 text-white hover:bg-white/15">
                Browse Jobs
              </Button>
            </div>
            <div className="mt-10 grid max-w-2xl grid-cols-3 gap-3">
              {[
                [stats.scholarships, "Active scholarships"],
                [stats.jobs, "Jobs and resources"],
                ["Private", "Member platform"]
              ].map(([value, label]) => (
                <div key={label} className="rounded-lg border border-white/12 bg-white/9 p-4">
                  <div className="font-heading text-2xl font-extrabold text-gold">{value}</div>
                  <div className="mt-1 text-xs uppercase tracking-wide text-white/62">{label}</div>
                </div>
              ))}
            </div>
          </div>
        </div>
      </section>

      <section className="bg-white py-12">
        <div className="container-page grid gap-5 md:grid-cols-3">
          {featureCards.map(([Icon, title, body]) => (
            <div key={String(title)} className="rounded-lg border border-slate-200 bg-white p-5">
              <Icon className="mb-4 text-navy" size={24} />
              <h2 className="font-heading text-lg font-extrabold text-navy">{title}</h2>
              <p className="mt-2 text-sm leading-6 text-slate-600">{body}</p>
            </div>
          ))}
        </div>
      </section>

      <section className="py-16">
        <div className="container-page">
          <div className="mb-8 max-w-2xl">
            <p className="font-accent text-xs font-bold uppercase tracking-[0.28em] text-gold">Study pathway</p>
            <h2 className="mt-2 font-heading text-3xl font-extrabold text-navy">Scholarships, language training and visa readiness in one flow</h2>
            <p className="mt-2 text-slate-600">The platform is not only a listing directory. It supports the surrounding preparation work applicants need before they submit.</p>
          </div>
          <div className="grid gap-5 md:grid-cols-3">
            {pathwayCards.map(([Icon, title, body, href]) => (
              <Link key={title} href={href} className="rounded-lg border border-slate-200 bg-white p-6 premium-shadow transition hover:-translate-y-1 hover:border-navy/30">
                <Icon className="mb-4 text-navy" size={26} />
                <h3 className="font-heading text-xl font-extrabold text-navy">{title}</h3>
                <p className="mt-2 text-sm leading-6 text-slate-600">{body}</p>
                <span className="mt-5 inline-flex items-center gap-2 text-sm font-bold text-navy">
                  Open <ArrowRight size={15} />
                </span>
              </Link>
            ))}
          </div>
        </div>
      </section>

      <section className="py-16">
        <div className="container-page">
          <div className="mb-8 flex flex-wrap items-end justify-between gap-4">
            <div>
              <p className="font-accent text-xs font-bold uppercase tracking-[0.28em] text-gold">Scholarships</p>
              <h2 className="mt-2 font-heading text-3xl font-extrabold text-navy">Active, balanced European opportunities</h2>
              <p className="mt-2 max-w-2xl text-slate-600">Curated across multiple European countries so the list does not over-focus on only one destination.</p>
            </div>
            <Link href="/scholarships" className="inline-flex items-center gap-2 text-sm font-bold text-navy">
              View all <ArrowRight size={16} />
            </Link>
          </div>
          <div className="grid gap-5 md:grid-cols-3">
            {scholarships.slice(0, 3).map((item) => <ScholarshipCard key={item.id} scholarship={item} />)}
          </div>
        </div>
      </section>

      <section className="bg-white py-16">
        <div className="container-page">
          <div className="mb-8 flex flex-wrap items-end justify-between gap-4">
            <div>
              <p className="font-accent text-xs font-bold uppercase tracking-[0.28em] text-gold">Jobs</p>
              <h2 className="mt-2 font-heading text-3xl font-extrabold text-navy">Remote and onsite roles with clear access</h2>
              <p className="mt-2 max-w-2xl text-slate-600">Free roles remain useful, while premium listings support a stronger upgrade path.</p>
            </div>
            <Link href="/jobs" className="inline-flex items-center gap-2 text-sm font-bold text-navy">
              View jobs <ArrowRight size={16} />
            </Link>
          </div>
          <div className="grid gap-5 md:grid-cols-3">
            {jobs.slice(0, 3).map((item) => <JobCard key={item.id} job={item} />)}
          </div>
        </div>
      </section>

      <section className="py-16">
        <div className="container-page rounded-lg bg-navy p-8 text-white md:p-10">
          <div className="grid gap-8 md:grid-cols-[1.2fr_0.8fr] md:items-center">
            <div>
              <p className="font-accent text-xs font-bold uppercase tracking-[0.28em] text-gold">Premium support</p>
              <h2 className="mt-2 font-heading text-3xl font-extrabold">A better way to manage serious applications</h2>
              <p className="mt-3 max-w-2xl leading-7 text-white/72">
                Create an account, upload documents, apply directly, and track status updates from a dashboard built for scholarships, jobs and study-abroad support.
              </p>
            </div>
            <div className="space-y-3">
              {["Premium badges and access tiers", "CV, passport, certificate and recommendation uploads", "Admin analytics and application management"].map((item) => (
                <div key={item} className="flex items-center gap-3 rounded-md bg-white/8 p-3 text-sm font-semibold text-white/85">
                  <CheckCircle2 size={18} className="text-gold" /> {item}
                </div>
              ))}
              <Button href={user ? "/dashboard" : "/register"} variant="gold" className="w-full">
                {user ? "Open Dashboard" : "Create Free Account"} <UsersRound size={17} />
              </Button>
            </div>
          </div>
        </div>
      </section>
    </>
  );
}
