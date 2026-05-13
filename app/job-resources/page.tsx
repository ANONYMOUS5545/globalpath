import type { Metadata } from "next";
import { ArrowUpRight, BriefcaseBusiness, Filter, Search, ShieldCheck } from "lucide-react";
import { PageHeader } from "@/components/PageHeader";
import { Badge } from "@/components/ui/Badge";
import { Button } from "@/components/ui/Button";
import { getJobResourceFacets, getJobResources } from "@/lib/data";

export const metadata: Metadata = {
  title: "Job Resources",
  description: "Trusted remote and onsite job application resources with cost notes and official source links."
};

type PageProps = {
  searchParams?: Promise<Record<string, string | string[] | undefined>>;
};

function single(value: string | string[] | undefined) {
  return Array.isArray(value) ? value[0] : value ?? "";
}

function label(value: string) {
  return value.split("_").join(" ");
}

export default async function JobResourcesPage({ searchParams }: PageProps) {
  const params = (await searchParams) ?? {};
  const filters = {
    search: single(params.search),
    category: single(params.category),
    cost: single(params.cost)
  };
  const [resources, facets] = await Promise.all([getJobResources(filters), getJobResourceFacets()]);

  return (
    <>
      <PageHeader
        eyebrow="Job resources"
        title="Trusted portals for remote and onsite applications"
        description="Use official job boards and employer portals with clear cost notes. Avoid anyone asking for money to unlock vacancies."
      />
      <section className="py-10">
        <div className="container-page">
          <form className="card-border mb-7 grid gap-3 rounded-lg bg-white p-4 premium-shadow md:grid-cols-[1.5fr_1fr_1fr_auto]">
            <label className="relative">
              <Search className="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" size={17} />
              <input name="search" defaultValue={filters.search} placeholder="Search portals, countries or job tracks" className="form-input pl-10" />
            </label>
            <select name="category" defaultValue={filters.category} className="form-input">
              <option value="">All categories</option>
              {facets.categories.map((category) => <option key={category} value={category}>{label(category)}</option>)}
            </select>
            <select name="cost" defaultValue={filters.cost} className="form-input">
              <option value="">All cost types</option>
              {facets.costs.map((cost) => <option key={cost} value={cost}>{cost}</option>)}
            </select>
            <Button type="submit"><Filter size={16} /> Filter</Button>
          </form>

          <div className="mb-6 flex flex-wrap items-center justify-between gap-3">
            <p className="text-sm font-semibold text-slate-600">
              Showing <span className="text-navy">{resources.length}</span> trusted job resources
            </p>
            <div className="flex flex-wrap gap-2">
              {facets.categories.map((category) => (
                <a key={category} href={`/job-resources?category=${category}`} className="rounded-md border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-600 hover:border-navy hover:text-navy">
                  {label(category)}
                </a>
              ))}
            </div>
          </div>

          {resources.length ? (
            <div className="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
              {resources.map((resource) => (
                <article key={resource.id} className="rounded-lg border border-slate-200 bg-white p-6 premium-shadow">
                  <div className="mb-4 flex items-start justify-between gap-3">
                    <BriefcaseBusiness className="text-navy" size={26} />
                    <Badge tone={resource.applicationCostType === "free" ? "green" : "gold"}>{resource.applicationCostType}</Badge>
                  </div>
                  <p className="font-accent text-xs font-bold uppercase tracking-[0.2em] text-gold">{label(resource.category)}</p>
                  <h2 className="mt-2 font-heading text-xl font-extrabold text-navy">{resource.title}</h2>
                  <p className="mt-2 text-sm font-semibold text-slate-600">{resource.organization}</p>
                  <p className="mt-3 text-sm leading-6 text-slate-600">{resource.summary}</p>
                  {resource.costNotes ? (
                    <div className="mt-4 rounded-md bg-slate-50 p-3 text-xs leading-5 text-slate-600">
                      <ShieldCheck className="mb-2 text-navy" size={15} />
                      {resource.costNotes}
                    </div>
                  ) : null}
                  <a href={resource.applyUrl} target="_blank" rel="noreferrer" className="mt-5 inline-flex items-center gap-2 text-sm font-bold text-navy">
                    Open official portal <ArrowUpRight size={15} />
                  </a>
                </article>
              ))}
            </div>
          ) : (
            <div className="rounded-lg border border-slate-200 bg-white p-10 text-center">
              <BriefcaseBusiness className="mx-auto mb-4 text-navy" size={34} />
              <h2 className="font-heading text-2xl font-extrabold text-navy">No resources matched</h2>
              <p className="mt-2 text-slate-600">Try a broader search or clear the selected category.</p>
              <Button href="/job-resources" className="mt-5">Clear Filters</Button>
            </div>
          )}
        </div>
      </section>
    </>
  );
}
