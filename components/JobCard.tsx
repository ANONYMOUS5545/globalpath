import { ArrowUpRight, BriefcaseBusiness, CalendarDays, LockKeyhole, MapPin } from "lucide-react";
import Link from "next/link";
import { Badge } from "./ui/Badge";
import { accessTierLabel, deadlineLabel, jobTypeLabel, workplaceLabel } from "@/lib/format";
import type { Job } from "@/lib/types";

export function JobCard({ job }: { job: Job }) {
  return (
    <article className="card-border flex h-full flex-col rounded-lg bg-white p-5 premium-shadow">
      <div className="mb-4 flex items-start justify-between gap-3">
        <div className="flex h-11 w-11 items-center justify-center rounded-md bg-navy/8 text-navy">
          <BriefcaseBusiness size={22} />
        </div>
        {job.accessTier === "FREE" ? (
          <Badge tone="green">Free</Badge>
        ) : (
          <Badge tone={job.accessTier === "PREMIUM_PLUS" ? "gold" : "navy"}>
            <LockKeyhole size={12} /> {accessTierLabel(job.accessTier)}
          </Badge>
        )}
      </div>

      <div className="mb-3 flex flex-wrap gap-2">
        <Badge tone={job.workplaceType === "REMOTE" ? "blue" : "gray"}>{workplaceLabel(job.workplaceType)}</Badge>
        <Badge tone="gold">{jobTypeLabel(job.jobType)}</Badge>
      </div>

      <h3 className="mb-2 font-heading text-lg font-extrabold leading-snug text-navy">
        <Link href={`/jobs/${job.slug}`}>{job.title}</Link>
      </h3>
      <p className="mb-1 text-sm font-bold text-slate-700">{job.organization}</p>
      <p className="mb-4 flex items-center gap-1.5 text-sm text-slate-500">
        <MapPin size={15} /> {job.location}
      </p>
      <p className="mb-5 line-clamp-3 text-sm leading-6 text-slate-600">{job.description}</p>

      <div className="mt-auto flex items-center justify-between border-t border-slate-100 pt-4">
        <span className="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500">
          <CalendarDays size={14} /> {deadlineLabel(job.deadline)}
        </span>
        <Link href={`/jobs/${job.slug}`} className="inline-flex items-center gap-1 text-sm font-bold text-navy">
          View <ArrowUpRight size={15} />
        </Link>
      </div>
    </article>
  );
}
