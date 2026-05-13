import type { AccessTier, CoverageType, DegreeLevel, JobType, MembershipType, WorkplaceType } from "./types";

export function cx(...classes: Array<string | false | null | undefined>) {
  return classes.filter(Boolean).join(" ");
}

export function slugify(value: string) {
  return value
    .toLowerCase()
    .trim()
    .replace(/&/g, " and ")
    .replace(/[^a-z0-9]+/g, "-")
    .replace(/^-+|-+$/g, "");
}

export function formatDate(date: Date | string | null | undefined) {
  if (!date) return "Open";
  const parsed = typeof date === "string" ? new Date(date) : date;
  return new Intl.DateTimeFormat("en", {
    day: "2-digit",
    month: "short",
    year: "numeric"
  }).format(parsed);
}

export function daysUntil(date: Date | string | null | undefined) {
  if (!date) return null;
  const target = new Date(date);
  const today = new Date();
  today.setHours(0, 0, 0, 0);
  target.setHours(0, 0, 0, 0);
  return Math.ceil((target.getTime() - today.getTime()) / 86_400_000);
}

export function isActiveDeadline(date: Date | string | null | undefined) {
  const days = daysUntil(date);
  return days === null || days >= 0;
}

export function deadlineLabel(date: Date | string | null | undefined) {
  const days = daysUntil(date);
  if (days === null) return "Open";
  if (days < 0) return "Deadline passed";
  if (days === 0) return "Deadline today";
  if (days <= 14) return `${days} days left`;
  return formatDate(date);
}

export function titleCase(value: string) {
  return value
    .toLowerCase()
    .split(/[_\s-]+/)
    .filter(Boolean)
    .map((part) => part.charAt(0).toUpperCase() + part.slice(1))
    .join(" ");
}

export function membershipLabel(value: MembershipType) {
  const labels: Record<MembershipType, string> = {
    FREE: "Free",
    PREMIUM: "Premium",
    PREMIUM_PLUS: "Premium Plus"
  };
  return labels[value];
}

export function accessTierLabel(value: AccessTier) {
  const labels: Record<AccessTier, string> = {
    FREE: "Free",
    PREMIUM: "Premium",
    PREMIUM_PLUS: "Premium Plus"
  };
  return labels[value];
}

export function degreeLabel(value: DegreeLevel) {
  const labels: Record<DegreeLevel, string> = {
    UNDERGRADUATE: "Undergraduate",
    POSTGRADUATE: "Postgraduate",
    PHD: "PhD",
    ALL: "All levels"
  };
  return labels[value];
}

export function coverageLabel(value: CoverageType) {
  const labels: Record<CoverageType, string> = {
    FULL: "Fully funded",
    PARTIAL: "Partial funding",
    FELLOWSHIP: "Fellowship",
    EXCHANGE: "Exchange"
  };
  return labels[value];
}

export function jobTypeLabel(value: JobType) {
  const labels: Record<JobType, string> = {
    FULL_TIME: "Full time",
    PART_TIME: "Part time",
    CONTRACT: "Contract",
    INTERNSHIP: "Internship",
    VOLUNTEER: "Volunteer"
  };
  return labels[value];
}

export function workplaceLabel(value: WorkplaceType) {
  const labels: Record<WorkplaceType, string> = {
    REMOTE: "Remote",
    ONSITE: "Onsite",
    HYBRID: "Hybrid"
  };
  return labels[value];
}

export function money(value: number, currency = "USD") {
  return new Intl.NumberFormat("en", {
    style: "currency",
    currency,
    maximumFractionDigits: value % 1 === 0 ? 0 : 2
  }).format(value);
}
