import { accessibleTiers } from "./access";
import { isActiveDeadline } from "./format";
import { hasDatabaseUrl, prisma } from "./prisma";
import { fallbackBlogPosts, fallbackJobResources, fallbackJobs, fallbackScholarships } from "./seed-data";
import type { AccessTier, AppUser, Job, Scholarship, UserApplication } from "./types";

type ScholarshipFilters = {
  search?: string;
  country?: string;
  level?: string;
  coverage?: string;
  tier?: string;
};

type JobFilters = {
  search?: string;
  category?: string;
  workplace?: string;
  tier?: string;
};

function normalizeNeedle(value?: string) {
  return value?.trim().toLowerCase() ?? "";
}

function balanceByCountry(items: Scholarship[]) {
  const buckets = new Map<string, Scholarship[]>();
  for (const item of items) {
    buckets.set(item.country, [...(buckets.get(item.country) ?? []), item]);
  }

  const balanced: Scholarship[] = [];
  while ([...buckets.values()].some((bucket) => bucket.length > 0)) {
    for (const country of [...buckets.keys()].sort()) {
      const next = buckets.get(country)?.shift();
      if (next) balanced.push(next);
    }
  }
  return balanced;
}

export async function getScholarships(filters: ScholarshipFilters = {}, user: AppUser | null = null) {
  const allowed = accessibleTiers(user);
  let items: Scholarship[] = fallbackScholarships;

  if (hasDatabaseUrl()) {
    const rows = await prisma.scholarship.findMany({
      where: {
        isActive: true,
        accessTier: filters.tier && allowed.includes(filters.tier as AccessTier) ? (filters.tier as AccessTier) : { in: allowed },
        deadline: { gte: new Date() }
      },
      orderBy: [{ isFeatured: "desc" }, { deadline: "asc" }, { country: "asc" }]
    });
    items = rows as Scholarship[];
  }

  const search = normalizeNeedle(filters.search);
  items = items.filter((item) => {
    const visibleTier = !filters.tier || item.accessTier === filters.tier;
    const allowedTier = allowed.includes(item.accessTier);
    const activeDeadline = isActiveDeadline(item.deadline);
    const country = !filters.country || item.country === filters.country;
    const level = !filters.level || item.degreeLevel === filters.level;
    const coverage = !filters.coverage || item.coverageType === filters.coverage;
    const query =
      !search ||
      [item.title, item.provider, item.country, item.description, item.fieldOfStudy]
        .join(" ")
        .toLowerCase()
        .includes(search);

    return item.isActive && activeDeadline && country && level && coverage && query && visibleTier && allowedTier;
  });

  return balanceByCountry(items);
}

export async function getScholarship(slug: string) {
  if (hasDatabaseUrl()) {
    const row = await prisma.scholarship.findUnique({ where: { slug } });
    if (row && row.isActive && isActiveDeadline(row.deadline)) return row as Scholarship;
  }
  return fallbackScholarships.find((item) => item.slug === slug && item.isActive && isActiveDeadline(item.deadline)) ?? null;
}

export async function getAllVisibleScholarshipCountries() {
  const items = await getScholarships({}, { id: "system", firstName: "", lastName: "", email: "", membershipType: "PREMIUM_PLUS", scholarshipAccess: true });
  return [...new Set(items.map((item) => item.country))].sort();
}

export async function getJobs(filters: JobFilters = {}, user: AppUser | null = null) {
  const allowed = accessibleTiers(user);
  let items: Job[] = fallbackJobs;

  if (hasDatabaseUrl()) {
    const rows = await prisma.job.findMany({
      where: {
        isActive: true,
        accessTier: filters.tier && allowed.includes(filters.tier as AccessTier) ? (filters.tier as AccessTier) : { in: allowed }
      },
      orderBy: [{ isFeatured: "desc" }, { deadline: "asc" }, { updatedAt: "desc" }]
    });
    items = rows as Job[];
  }

  const search = normalizeNeedle(filters.search);
  return items.filter((item) => {
    const query =
      !search ||
      [item.title, item.organization, item.location, item.country, item.description, item.sector]
        .join(" ")
        .toLowerCase()
        .includes(search);
    const category =
      !filters.category ||
      (filters.category === "remote" ? item.workplaceType === "REMOTE" : item.workplaceType !== "REMOTE");
    const workplace = !filters.workplace || item.workplaceType === filters.workplace;
    const tier = !filters.tier || item.accessTier === filters.tier;
    return item.isActive && isActiveDeadline(item.deadline) && query && category && workplace && tier && allowed.includes(item.accessTier);
  });
}

export async function getJob(slug: string) {
  if (hasDatabaseUrl()) {
    const row = await prisma.job.findUnique({ where: { slug } });
    if (row && row.isActive && isActiveDeadline(row.deadline)) return row as Job;
  }
  return fallbackJobs.find((item) => item.slug === slug && item.isActive && isActiveDeadline(item.deadline)) ?? null;
}

export async function getPlatformStats() {
  if (hasDatabaseUrl()) {
    const [scholarships, jobs, applications, users] = await Promise.all([
      prisma.scholarship.count({ where: { isActive: true, deadline: { gte: new Date() } } }),
      prisma.job.count({ where: { isActive: true } }),
      prisma.application.count(),
      prisma.user.count()
    ]);
    return { scholarships, jobs, applications, users };
  }

  return {
    scholarships: fallbackScholarships.filter((item) => item.isActive && isActiveDeadline(item.deadline)).length,
    jobs: fallbackJobs.filter((item) => item.isActive && isActiveDeadline(item.deadline)).length,
    applications: 0,
    users: 0
  };
}

export async function getDashboardApplications(userId: string): Promise<UserApplication[]> {
  if (!hasDatabaseUrl()) return [];

  const rows = await prisma.application.findMany({
    where: { userId },
    include: { scholarship: true, job: true },
    orderBy: { createdAt: "desc" }
  });

  return rows.map((row) => ({
    id: row.id,
    type: row.type,
    referenceId: row.referenceId,
    status: row.status,
    notes: row.notes,
    adminNotes: row.adminNotes,
    createdAt: row.createdAt,
    title: row.scholarship?.title ?? row.job?.title ?? row.referenceId
  }));
}

export async function getBlogPosts() {
  if (hasDatabaseUrl()) {
    return prisma.blogPost.findMany({
      where: { isActive: true },
      orderBy: [{ isFeatured: "desc" }, { publishedAt: "desc" }]
    });
  }
  return fallbackBlogPosts.filter((post) => post.isActive);
}

export async function getJobResources() {
  if (hasDatabaseUrl()) {
    return prisma.jobResource.findMany({
      where: { isActive: true },
      orderBy: [{ isFeatured: "desc" }, { sortOrder: "asc" }]
    });
  }
  return fallbackJobResources.filter((resource) => resource.isActive);
}
