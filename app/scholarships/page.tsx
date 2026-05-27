import type { Metadata } from "next";
import { Filter, Search } from "lucide-react";
import { PageHeader } from "@/components/PageHeader";
import { ScholarshipCard } from "@/components/ScholarshipCard";
import { Button } from "@/components/ui/Button";
import { getCurrentUser } from "@/lib/auth";
import { getAllVisibleScholarshipCountries, getScholarships } from "@/lib/data";
import { accessTierLabel, coverageLabel, degreeLabel } from "@/lib/format";

export const metadata: Metadata = {
  title: "Scholarships",
  description: "Active scholarships for African students from reputable universities and globally recognized institutions."
};

type PageProps = {
  searchParams?: Promise<Record<string, string | string[] | undefined>>;
};

function single(value: string | string[] | undefined) {
  return Array.isArray(value) ? value[0] : value ?? "";
}

export default async function ScholarshipsPage({ searchParams }: PageProps) {
  const params = (await searchParams) ?? {};
  const user = await getCurrentUser();
  const filters = {
    search: single(params.search),
    country: single(params.country),
    level: single(params.level),
    coverage: single(params.coverage),
    tier: single(params.tier)
  };
  const [scholarships, countries] = await Promise.all([getScholarships(filters, user), getAllVisibleScholarshipCountries()]);

  return (
    <>
      <PageHeader
        eyebrow="Scholarships"
        title="International scholarships with active deadlines"
        description="Browse curated scholarships from reputable universities, public institutions and globally recognized funding bodies. Expired opportunities are automatically hidden."
      />

      <section className="py-10">
        <div className="container-page">
          <form className="card-border mb-7 grid gap-3 rounded-lg bg-white p-4 premium-shadow md:grid-cols-[1.5fr_1fr_1fr_1fr_auto]">
            <label className="relative">
              <Search className="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" size={17} />
              <input name="search" defaultValue={filters.search} placeholder="Search scholarship, country or provider" className="form-input pl-10" />
            </label>
            <select name="country" defaultValue={filters.country} className="form-input">
              <option value="">All countries</option>
              {countries.map((country) => <option key={country} value={country}>{country}</option>)}
            </select>
            <select name="level" defaultValue={filters.level} className="form-input">
              <option value="">All levels</option>
              {(["UNDERGRADUATE", "POSTGRADUATE", "PHD", "ALL"] as const).map((level) => (
                <option key={level} value={level}>{degreeLabel(level)}</option>
              ))}
            </select>
            <select name="coverage" defaultValue={filters.coverage} className="form-input">
              <option value="">All funding</option>
              {(["FULL", "PARTIAL", "FELLOWSHIP", "EXCHANGE"] as const).map((coverage) => (
                <option key={coverage} value={coverage}>{coverageLabel(coverage)}</option>
              ))}
            </select>
            <Button type="submit">
              <Filter size={16} /> Filter
            </Button>
          </form>

          <div className="mb-6 flex flex-wrap items-center justify-between gap-3">
            <div>
              <p className="text-sm font-semibold text-slate-600">
                Showing <span className="text-navy">{scholarships.length}</span> active scholarships
              </p>
              <p className="mt-1 text-xs text-slate-500">Balanced across European destinations and sorted by relevance, access and current deadline.</p>
            </div>
            <div className="flex flex-wrap gap-2">
              {(["FREE", "PREMIUM", "PREMIUM_PLUS"] as const).map((tier) => (
                <a
                  key={tier}
                  href={`/scholarships?tier=${tier}`}
                  className="rounded-md border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-600 hover:border-navy hover:text-navy"
                >
                  {accessTierLabel(tier)}
                </a>
              ))}
            </div>
          </div>

          <div className="mb-7 rounded-lg border border-gold/30 bg-white p-5">
            <h2 className="font-heading text-xl font-extrabold text-navy">More scholarships are available with Premium</h2>
            <p className="mt-2 text-sm leading-6 text-slate-600">
              Premium unlocks higher-value scholarship listings, document support, priority guidance and better application tracking.
            </p>
            <Button href="/membership#premium" variant="gold" className="mt-4">Compare Plans</Button>
          </div>

          {scholarships.length ? (
            <div className="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
              {scholarships.map((item) => <ScholarshipCard key={item.id} scholarship={item} />)}
            </div>
          ) : (
            <div className="rounded-lg border border-slate-200 bg-white p-10 text-center">
              <h2 className="font-heading text-2xl font-extrabold text-navy">No active scholarships matched</h2>
              <p className="mt-2 text-slate-600">Adjust your filters or check back after the next official update cycle.</p>
              <Button href="/scholarships" className="mt-5">Clear Filters</Button>
            </div>
          )}
        </div>
      </section>
    </>
  );
}
