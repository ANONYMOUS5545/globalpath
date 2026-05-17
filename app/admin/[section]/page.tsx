import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { PageHeader } from "@/components/PageHeader";
import { Badge } from "@/components/ui/Badge";
import { Button } from "@/components/ui/Button";
import { requireAdminUser } from "@/lib/auth";
import { getBlogPosts, getJobResources, getJobs, getScholarships } from "@/lib/data";
import { formatDate } from "@/lib/format";
import { hasDatabaseUrl, prisma } from "@/lib/prisma";

const sections = {
  users: "User Management",
  scholarships: "Scholarship Management",
  jobs: "Jobs Management",
  applications: "Application Management",
  payments: "Payments",
  messages: "Support Messages",
  subscribers: "Subscribers",
  blog: "Blog Management",
  "job-resources": "Job Resources",
  analytics: "Analytics",
  notifications: "Notifications"
} as const;

type SectionKey = keyof typeof sections;

type PageProps = {
  params: Promise<{ section: string }>;
};

export async function generateMetadata({ params }: PageProps): Promise<Metadata> {
  const { section } = await params;
  return { title: sections[section as SectionKey] ?? "Admin" };
}

export default async function AdminSectionPage({ params }: PageProps) {
  await requireAdminUser();
  const { section } = await params;
  if (!(section in sections)) notFound();

  const key = section as SectionKey;
  const rows = await loadRows(key);

  return (
    <>
      <PageHeader eyebrow="Admin" title={sections[key]} description="Restored management screens from the original XAMPP admin, now available in the Vercel-ready Next.js interface." />
      <section className="py-10">
        <div className="container-page grid gap-6 lg:grid-cols-[260px_minmax(0,1fr)]">
          <AdminNav active={key} />
          <div className="card-border rounded-lg bg-white p-5 premium-shadow">
            <div className="mb-5 flex flex-wrap items-center justify-between gap-3">
              <div>
                <h2 className="font-heading text-xl font-extrabold text-navy">{sections[key]}</h2>
                <p className="mt-1 text-sm text-slate-500">{rows.length} records visible</p>
              </div>
              <Button href="/admin" variant="outline">Back to Dashboard</Button>
            </div>
            <div className="overflow-x-auto">
              <table className="w-full text-left text-sm">
                <thead className="text-xs uppercase tracking-wide text-slate-500">
                  <tr>
                    <th className="py-3">Record</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Updated</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-slate-100">
                  {rows.map((row) => (
                    <tr key={row.id}>
                      <td className="min-w-72 py-3">
                        <div className="font-semibold text-slate-800">{row.title}</div>
                        <div className="mt-1 text-xs text-slate-500">{row.subtitle}</div>
                      </td>
                      <td>{row.category}</td>
                      <td><Badge tone={row.status === "active" || row.status === "completed" ? "green" : "blue"}>{row.status}</Badge></td>
                      <td className="text-slate-500">{row.updatedAt}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
            {!rows.length ? (
              <div className="mt-5 rounded-md bg-slate-50 p-8 text-center text-sm font-semibold text-slate-600">
                No records yet. Connect PostgreSQL, run the seed, or create records through the database-backed admin workflow.
              </div>
            ) : null}
          </div>
        </div>
      </section>
    </>
  );
}

function AdminNav({ active }: { active: SectionKey }) {
  return (
    <nav className="card-border rounded-lg bg-white p-3 lg:sticky lg:top-28 lg:self-start">
      {Object.entries(sections).map(([href, label]) => (
        <a
          key={href}
          href={`/admin/${href}`}
          className={`block rounded-md px-3 py-2 text-sm font-semibold ${active === href ? "bg-navy text-white" : "text-slate-700 hover:bg-slate-100 hover:text-navy"}`}
        >
          {label}
        </a>
      ))}
    </nav>
  );
}

async function loadRows(section: SectionKey) {
  if (section === "scholarships") {
    const items = await getScholarships({}, { id: "admin", firstName: "Admin", lastName: "", email: "", membershipType: "PREMIUM_PLUS", scholarshipAccess: true });
    return items.map((item) => ({
      id: item.id,
      title: item.title,
      subtitle: `${item.provider} - ${item.country}`,
      category: item.accessTier,
      status: item.isActive ? "active" : "inactive",
      updatedAt: formatDate(item.deadline)
    }));
  }

  if (section === "jobs") {
    const items = await getJobs({}, { id: "admin", firstName: "Admin", lastName: "", email: "", membershipType: "PREMIUM_PLUS", scholarshipAccess: true });
    return items.map((item) => ({
      id: item.id,
      title: item.title,
      subtitle: `${item.organization} - ${item.location}`,
      category: item.workplaceType,
      status: item.isActive ? "active" : "inactive",
      updatedAt: formatDate(item.deadline)
    }));
  }

  if (section === "job-resources") {
    const items = await getJobResources();
    return items.map((item) => ({
      id: item.id,
      title: item.title,
      subtitle: `${item.organization} - ${item.region}`,
      category: item.category,
      status: item.isActive ? "active" : "inactive",
      updatedAt: String(item.sortOrder)
    }));
  }

  if (section === "blog") {
    const items = await getBlogPosts();
    return items.map((item) => ({
      id: item.id,
      title: item.title,
      subtitle: item.excerpt,
      category: item.category,
      status: item.isActive ? "active" : "inactive",
      updatedAt: formatDate(item.publishedAt)
    }));
  }

  if (!hasDatabaseUrl()) return [];

  if (section === "users") {
    const users = await prisma.user.findMany({ orderBy: { createdAt: "desc" }, take: 100 });
    return users.map((user) => ({
      id: user.id,
      title: `${user.firstName} ${user.lastName}`,
      subtitle: user.email,
      category: user.membershipType,
      status: user.status.toLowerCase(),
      updatedAt: formatDate(user.updatedAt)
    }));
  }

  if (section === "applications") {
    const applications = await prisma.application.findMany({ include: { user: true, scholarship: true, job: true }, orderBy: { createdAt: "desc" }, take: 100 });
    return applications.map((item) => ({
      id: item.id,
      title: item.scholarship?.title ?? item.job?.title ?? item.referenceId,
      subtitle: item.user.email,
      category: item.type,
      status: item.status.toLowerCase(),
      updatedAt: formatDate(item.updatedAt)
    }));
  }

  if (section === "payments") {
    const payments = await prisma.payment.findMany({ include: { user: true }, orderBy: { paymentDate: "desc" }, take: 100 });
    return payments.map((item) => ({
      id: item.id,
      title: `${item.plan} - ${item.currency} ${item.amount}`,
      subtitle: item.user.email,
      category: item.gateway,
      status: item.status.toLowerCase(),
      updatedAt: formatDate(item.paymentDate)
    }));
  }

  if (section === "messages") {
    const messages = await prisma.supportMessage.findMany({ orderBy: { createdAt: "desc" }, take: 100 });
    return messages.map((item) => ({
      id: item.id,
      title: item.name ?? item.email ?? "Guest message",
      subtitle: item.message,
      category: item.isEscalated ? "escalated" : "support",
      status: item.status,
      updatedAt: formatDate(item.createdAt)
    }));
  }

  if (section === "subscribers") {
    const subscribers = await prisma.subscriber.findMany({ orderBy: { subscribedAt: "desc" }, take: 100 });
    return subscribers.map((item) => ({
      id: item.id,
      title: item.email,
      subtitle: item.name ?? item.country ?? "Newsletter subscriber",
      category: item.country ?? "Global",
      status: item.isActive ? "active" : "inactive",
      updatedAt: formatDate(item.subscribedAt)
    }));
  }

  if (section === "notifications") {
    const notifications = await prisma.notification.findMany({ orderBy: { createdAt: "desc" }, take: 100 });
    return notifications.map((item) => ({
      id: item.id,
      title: item.title,
      subtitle: item.body,
      category: item.userId ? "user" : "system",
      status: item.readAt ? "read" : "unread",
      updatedAt: formatDate(item.createdAt)
    }));
  }

  const [users, scholarships, jobs, applications] = await Promise.all([
    prisma.user.count(),
    prisma.scholarship.count({ where: { isActive: true } }),
    prisma.job.count({ where: { isActive: true } }),
    prisma.application.count()
  ]);

  return [
    { id: "users", title: "Users", subtitle: "Registered member accounts", category: "members", status: "active", updatedAt: String(users) },
    { id: "scholarships", title: "Scholarships", subtitle: "Active scholarship records", category: "opportunities", status: "active", updatedAt: String(scholarships) },
    { id: "jobs", title: "Jobs", subtitle: "Active job records", category: "opportunities", status: "active", updatedAt: String(jobs) },
    { id: "applications", title: "Applications", subtitle: "Submitted applications", category: "pipeline", status: "active", updatedAt: String(applications) }
  ];
}
