import type { Metadata } from "next";
import { BriefcaseBusiness, Filter, Search } from "lucide-react";
import { JobCard } from "@/components/JobCard";
import { PageHeader } from "@/components/PageHeader";
import { Button } from "@/components/ui/Button";
import { getCurrentUser } from "@/lib/auth";
import { getJobResources, getJobs } from "@/lib/data";
import { accessTierLabel, workplaceLabel } from "@/lib/format";

export const metadata: Metadata = {
  title: "Jobs",
  description: "Remote and onsite international jobs with free and premium access tiers."
};

type PageProps = {
  searchParams?: Promise<Record<string, string | string[] | undefined>>;
};

function single(value: string | string[] | undefined) {
  return Array.isArray(value) ? value[0] : value ?? "";
}

export default async function JobsPage({ searchParams }: PageProps) {
  const params = (await searchParams) ?? {};
  const user = await getCurrentUser();
  const filters = {
    search: single(params.search),
    category: single(params.category),
    workplace: single(params.workplace),
    tier: single(params.tier)
  };
  const [jobs, resources] = await Promise.all([getJobs(filters, user), getJobResources()]);
  const remoteCount = jobs.filter((job) => job.workplaceType === "REMOTE").length;
  const onsiteCount = jobs.length - remoteCount;

  return (
    <>
      <PageHeader
        eyebrow="Jobs"
        title="Remote and onsite international roles"
        description="Browse direct employer opportunities, organized by work mode and membership tier. Free users keep useful access while premium members unlock advanced listings."
      />
      <section className="py-10">
        <div className="container-page">
          <form className="card-border mb-7 grid gap-3 rounded-lg bg-white p-4 premium-shadow md:grid-cols-[1.5fr_1fr_1fr_1fr_auto]">
            <label className="relative">
              <Search className="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" size={17} />
              <input name="search" defaultValue={filters.search} placeholder="Search job, employer or sector" className="form-input pl-10" />
            </label>
            <select name="category" defaultValue={filters.category} className="form-input">
              <option value="">All categories</option>
              <option value="remote">Remote jobs</option>
              <option value="onsite">Onsite jobs</option>
            </select>
            <select name="workplace" defaultValue={filters.workplace} className="form-input">
              <option value="">All work modes</option>
              {(["REMOTE", "ONSITE", "HYBRID"] as const).map((workplace) => (
                <option key={workplace} value={workplace}>{workplaceLabel(workplace)}</option>
              ))}
            </select>
            <select name="tier" defaultValue={filters.tier} className="form-input">
              <option value="">All visible tiers</option>
              {(["FREE", "PREMIUM", "PREMIUM_PLUS"] as const).map((tier) => (
                <option key={tier} value={tier}>{accessTierLabel(tier)}</option>
              ))}
            </select>
            <Button type="submit"><Filter size={16} /> Filter</Button>
          </form>

          <div className="mb-6 grid gap-3 md:grid-cols-3">
            {[
              ["Visible jobs", jobs.length],
              ["Remote jobs", remoteCount],
              ["Onsite/hybrid jobs", onsiteCount]
            ].map(([label, value]) => (
              <div key={label} className="rounded-lg border border-slate-200 bg-white p-4">
                <div className="font-heading text-2xl font-extrabold text-navy">{value}</div>
                <div className="mt-1 text-sm font-semibold text-slate-500">{label}</div>
              </div>
            ))}
          </div>

          {jobs.length ? (
            <div className="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
              {jobs.map((item) => <JobCard key={item.id} job={item} />)}
            </div>
          ) : (
            <div className="rounded-lg border border-slate-200 bg-white p-10 text-center">
              <BriefcaseBusiness className="mx-auto mb-4 text-navy" size={34} />
              <h2 className="font-heading text-2xl font-extrabold text-navy">No jobs matched</h2>
              <p className="mt-2 text-slate-600">Try a broader search or switch between remote and onsite categories.</p>
              <Button href="/jobs" className="mt-5">Clear Filters</Button>
            </div>
          )}

          <div className="mt-12">
            <div className="mb-5">
              <p className="font-accent text-xs font-bold uppercase tracking-[0.28em] text-gold">Resources</p>
              <h2 className="mt-2 font-heading text-2xl font-extrabold text-navy">Trusted job application portals</h2>
            </div>
            <div className="grid gap-4 md:grid-cols-3">
              {resources.map((resource) => (
                <article key={resource.id} className="rounded-lg border border-slate-200 bg-white p-5">
                  <div className="mb-2 text-xs font-bold uppercase tracking-wide text-gold">{resource.category.replace("_", " ")}</div>
                  <h3 className="font-heading text-lg font-extrabold text-navy">{resource.title}</h3>
                  <p className="mt-2 text-sm leading-6 text-slate-600">{resource.summary}</p>
                  <a href={resource.applyUrl} target="_blank" rel="noreferrer" className="mt-4 inline-flex text-sm font-bold text-navy">
                    Open official portal
                  </a>
                </article>
              ))}
            </div>
          </div>
        </div>
      </section>
    </>
  );
}
