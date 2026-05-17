import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { ArrowUpRight, CalendarDays, CheckCircle2, ExternalLink, FileText, LockKeyhole, MapPin } from "lucide-react";
import { ApplyForm } from "@/components/ApplyForm";
import { PageHeader } from "@/components/PageHeader";
import { Badge } from "@/components/ui/Badge";
import { Button } from "@/components/ui/Button";
import { canAccessTier, upgradeTarget } from "@/lib/access";
import { getCurrentUser } from "@/lib/auth";
import { getScholarship } from "@/lib/data";
import { accessTierLabel, coverageLabel, deadlineLabel, degreeLabel, formatDate } from "@/lib/format";

type PageProps = {
  params: Promise<{ slug: string }>;
};

export async function generateMetadata({ params }: PageProps): Promise<Metadata> {
  const { slug } = await params;
  const scholarship = await getScholarship(slug);
  return {
    title: scholarship?.title ?? "Scholarship",
    description: scholarship?.description
  };
}

export default async function ScholarshipDetailPage({ params }: PageProps) {
  const { slug } = await params;
  const [scholarship, user] = await Promise.all([getScholarship(slug), getCurrentUser()]);
  if (!scholarship) notFound();

  const canApply = canAccessTier(user, scholarship.accessTier);
  const locked = scholarship.accessTier !== "FREE" && !canApply;

  return (
    <>
      <PageHeader
        eyebrow={`${scholarship.country} scholarship`}
        title={scholarship.title}
        description={`${scholarship.provider} - ${coverageLabel(scholarship.coverageType)} - Deadline ${formatDate(scholarship.deadline)}`}
      />
      <section className="py-10">
        <div className="container-page grid gap-7 lg:grid-cols-[minmax(0,1fr)_360px]">
          <article className="space-y-5">
            <div className="card-border rounded-lg bg-white p-6 premium-shadow">
              <div className="mb-5 flex flex-wrap gap-2">
                <Badge tone="blue">{degreeLabel(scholarship.degreeLevel)}</Badge>
                <Badge tone="gold">{coverageLabel(scholarship.coverageType)}</Badge>
                <Badge tone={scholarship.accessTier === "FREE" ? "green" : "navy"}>
                  {scholarship.accessTier !== "FREE" && <LockKeyhole size={12} />}
                  {accessTierLabel(scholarship.accessTier)}
                </Badge>
              </div>

              <Section title="Overview">{scholarship.description}</Section>
              <Section title="Eligibility">{scholarship.eligibility}</Section>
              <Section title="Coverage">{scholarship.benefits}</Section>

              <div className="mt-7 grid gap-5 md:grid-cols-2">
                <InfoList title="Required Documents" items={scholarship.requiredDocuments} />
                <InfoList title="Application Process" items={scholarship.applicationProcess} ordered />
              </div>
            </div>
          </article>

          <aside className="space-y-4 lg:sticky lg:top-28 lg:self-start">
            <div className="card-border rounded-lg bg-white p-5 premium-shadow">
              <div className="mb-4 rounded-md bg-gold/12 p-4 text-center">
                <CalendarDays className="mx-auto mb-2 text-[#8a6416]" size={24} />
                <div className="text-xs font-bold uppercase tracking-wide text-[#8a6416]">Deadline</div>
                <div className="font-heading text-xl font-extrabold text-navy">{deadlineLabel(scholarship.deadline)}</div>
              </div>
              <dl className="space-y-3 text-sm">
                <Row label="University" value={scholarship.provider} />
                <Row label="Country" value={scholarship.country} icon={<MapPin size={14} />} />
                <Row label="Level" value={degreeLabel(scholarship.degreeLevel)} />
                <Row label="Coverage" value={coverageLabel(scholarship.coverageType)} />
              </dl>
              <a
                href={scholarship.officialUrl}
                target="_blank"
                rel="noreferrer"
                className="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-md border border-navy/20 px-4 py-2.5 text-sm font-bold text-navy hover:bg-navy/5"
              >
                Official Source <ExternalLink size={16} />
              </a>
            </div>

            <div className="card-border rounded-lg bg-white p-5 premium-shadow">
              <h2 className="mb-2 font-heading text-xl font-extrabold text-navy">Apply from Global Path</h2>
              <p className="mb-4 text-sm leading-6 text-slate-600">
                Create an account, attach your documents and submit through Global Path so status feedback is tracked from your dashboard.
              </p>
              {!user ? (
                <Button href={`/login?redirect=/scholarships/${scholarship.slug}`} className="w-full">
                  Login to Apply <ArrowUpRight size={16} />
                </Button>
              ) : locked ? (
                <Button href={upgradeTarget(scholarship.accessTier)} variant="gold" className="w-full">
                  Upgrade to Apply
                </Button>
              ) : (
                <ApplyForm type="SCHOLARSHIP" referenceId={scholarship.id} />
              )}
            </div>
          </aside>
        </div>
      </section>
    </>
  );
}

function Section({ title, children }: { title: string; children: React.ReactNode }) {
  return (
    <section className="mb-6">
      <h2 className="mb-2 font-heading text-xl font-extrabold text-navy">{title}</h2>
      <p className="leading-8 text-slate-700">{children}</p>
    </section>
  );
}

function InfoList({ title, items, ordered = false }: { title: string; items: string[]; ordered?: boolean }) {
  const List = ordered ? "ol" : "ul";
  return (
    <div className="rounded-lg border border-slate-200 bg-slate-50 p-4">
      <h3 className="mb-3 inline-flex items-center gap-2 font-heading text-base font-extrabold text-navy">
        {ordered ? <CheckCircle2 size={17} /> : <FileText size={17} />} {title}
      </h3>
      <List className={ordered ? "list-decimal space-y-2 pl-5 text-sm leading-6 text-slate-700" : "list-disc space-y-2 pl-5 text-sm leading-6 text-slate-700"}>
        {items.map((item) => <li key={item}>{item}</li>)}
      </List>
    </div>
  );
}

function Row({ label, value, icon }: { label: string; value: string; icon?: React.ReactNode }) {
  return (
    <div className="flex items-start justify-between gap-4 border-b border-slate-100 pb-3 last:border-0">
      <dt className="text-slate-500">{label}</dt>
      <dd className="flex items-center gap-1.5 text-right font-bold text-slate-800">
        {icon} {value}
      </dd>
    </div>
  );
}
