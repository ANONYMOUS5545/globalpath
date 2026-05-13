import { ArrowUpRight, CalendarDays, GraduationCap, LockKeyhole, MapPin } from "lucide-react";
import Link from "next/link";
import { Badge } from "./ui/Badge";
import { accessTierLabel, coverageLabel, deadlineLabel, degreeLabel } from "@/lib/format";
import type { Scholarship } from "@/lib/types";

export function ScholarshipCard({ scholarship }: { scholarship: Scholarship }) {
  const premium = scholarship.accessTier !== "FREE";

  return (
    <article className="card-border flex h-full flex-col rounded-lg bg-white p-5 premium-shadow">
      <div className="mb-4 flex items-start justify-between gap-3">
        <div className="flex h-11 w-11 items-center justify-center rounded-md bg-navy/8 text-navy">
          <GraduationCap size={22} />
        </div>
        {premium ? (
          <Badge tone={scholarship.accessTier === "PREMIUM_PLUS" ? "gold" : "navy"}>
            <LockKeyhole size={12} /> {accessTierLabel(scholarship.accessTier)}
          </Badge>
        ) : (
          <Badge tone="green">Free</Badge>
        )}
      </div>

      <div className="mb-3 flex flex-wrap gap-2">
        <Badge tone="blue">{degreeLabel(scholarship.degreeLevel)}</Badge>
        <Badge tone="gold">{coverageLabel(scholarship.coverageType)}</Badge>
      </div>

      <h3 className="mb-2 font-heading text-lg font-extrabold leading-snug text-navy">
        <Link href={`/scholarships/${scholarship.slug}`}>{scholarship.title}</Link>
      </h3>
      <p className="mb-1 text-sm font-bold text-slate-700">{scholarship.provider}</p>
      <p className="mb-4 flex items-center gap-1.5 text-sm text-slate-500">
        <MapPin size={15} /> {scholarship.country}
      </p>
      <p className="mb-5 line-clamp-3 text-sm leading-6 text-slate-600">{scholarship.description}</p>

      <div className="mt-auto flex items-center justify-between border-t border-slate-100 pt-4">
        <span className="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500">
          <CalendarDays size={14} /> {deadlineLabel(scholarship.deadline)}
        </span>
        <Link href={`/scholarships/${scholarship.slug}`} className="inline-flex items-center gap-1 text-sm font-bold text-navy">
          Details <ArrowUpRight size={15} />
        </Link>
      </div>
    </article>
  );
}
