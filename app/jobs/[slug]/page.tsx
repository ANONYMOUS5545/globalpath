import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { ArrowUpRight, CalendarDays, LockKeyhole, MapPin } from "lucide-react";
import { ApplyForm } from "@/components/ApplyForm";
import { PageHeader } from "@/components/PageHeader";
import { Badge } from "@/components/ui/Badge";
import { Button } from "@/components/ui/Button";
import { canAccessTier, upgradeTarget } from "@/lib/access";
import { getCurrentUser } from "@/lib/auth";
import { getJob } from "@/lib/data";
import { accessTierLabel, deadlineLabel, formatDate, jobTypeLabel, workplaceLabel } from "@/lib/format";

type PageProps = {
  params: Promise<{ slug: string }>;
};

export async function generateMetadata({ params }: PageProps): Promise<Metadata> {
  const { slug } = await params;
  const job = await getJob(slug);
  return {
    title: job?.title ?? "Job",
    description: job?.description
  };
}

export default async function JobDetailPage({ params }: PageProps) {
  const { slug } = await params;
  const [job, user] = await Promise.all([getJob(slug), getCurrentUser()]);
  if (!job) notFound();

  const canApply = canAccessTier(user, job.accessTier);
  const locked = job.accessTier !== "FREE" && !canApply;

  return (
    <>
      <PageHeader
        eyebrow={`${workplaceLabel(job.workplaceType)} job`}
        title={job.title}
        description={`${job.organization} - ${job.location} - Deadline ${formatDate(job.deadline)}`}
      />
      <section className="py-10">
        <div className="container-page grid gap-7 lg:grid-cols-[minmax(0,1fr)_360px]">
          <article className="card-border rounded-lg bg-white p-6 premium-shadow">
            <div className="mb-5 flex flex-wrap gap-2">
              <Badge tone="blue">{workplaceLabel(job.workplaceType)}</Badge>
              <Badge tone="gold">{jobTypeLabel(job.jobType)}</Badge>
              <Badge tone={job.accessTier === "FREE" ? "green" : "navy"}>
                {job.accessTier !== "FREE" && <LockKeyhole size={12} />}
                {accessTierLabel(job.accessTier)}
              </Badge>
              <Badge tone="gray">{job.sector}</Badge>
            </div>
            <section className="mb-7">
              <h2 className="mb-2 font-heading text-xl font-extrabold text-navy">Role Overview</h2>
              <p className="leading-8 text-slate-700">{job.description}</p>
            </section>
            <section className="mb-7">
              <h2 className="mb-2 font-heading text-xl font-extrabold text-navy">Requirements</h2>
              <p className="leading-8 text-slate-700">{job.requirements}</p>
            </section>
            <section className="rounded-lg border border-slate-200 bg-slate-50 p-4">
              <h2 className="mb-2 font-heading text-lg font-extrabold text-navy">Application Guidance</h2>
              <ul className="list-disc space-y-2 pl-5 text-sm leading-6 text-slate-700">
                <li>Use a role-specific CV that mirrors the employer requirements.</li>
                <li>Upload clean PDF documents and track your submission from the dashboard.</li>
                <li>Apply through Global Path so your documents, submission and review notes stay in one dashboard.</li>
              </ul>
            </section>
          </article>

          <aside className="space-y-4 lg:sticky lg:top-28 lg:self-start">
            <div className="card-border rounded-lg bg-white p-5 premium-shadow">
              <div className="mb-4 rounded-md bg-gold/12 p-4 text-center">
                <CalendarDays className="mx-auto mb-2 text-[#8a6416]" size={24} />
                <div className="text-xs font-bold uppercase tracking-wide text-[#8a6416]">Deadline</div>
                <div className="font-heading text-xl font-extrabold text-navy">{deadlineLabel(job.deadline)}</div>
              </div>
              <dl className="space-y-3 text-sm">
                <Row label="Employer" value={job.organization} />
                <Row label="Location" value={job.location} icon={<MapPin size={14} />} />
                <Row label="Work mode" value={workplaceLabel(job.workplaceType)} />
                <Row label="Access" value={accessTierLabel(job.accessTier)} />
              </dl>
              <div className="mt-5 rounded-md bg-navy/5 p-3 text-sm font-semibold leading-6 text-navy">
                Applications are handled inside Global Path. Sign in to submit and track this role.
              </div>
            </div>

            <div className="card-border rounded-lg bg-white p-5 premium-shadow">
              <h2 className="mb-2 font-heading text-xl font-extrabold text-navy">Apply from Global Path</h2>
              <p className="mb-4 text-sm leading-6 text-slate-600">Submit your documents and track review status from your member dashboard.</p>
              {!user ? (
                <Button href={`/login?redirect=/jobs/${job.slug}`} className="w-full">
                  Login to Apply <ArrowUpRight size={16} />
                </Button>
              ) : locked ? (
                <div className="space-y-3">
                  <p className="rounded-md bg-gold/12 p-3 text-sm font-semibold leading-6 text-[#7a5b16]">
                    Premium members see more roles, higher-value listings and guided application support.
                  </p>
                  <Button href={upgradeTarget(job.accessTier)} variant="gold" className="w-full">
                    Upgrade to Apply
                  </Button>
                </div>
              ) : (
                <ApplyForm type="JOB" referenceId={job.id} />
              )}
            </div>
          </aside>
        </div>
      </section>
    </>
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
