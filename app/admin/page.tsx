import type { Metadata } from "next";
import { BarChart3, BriefcaseBusiness, GraduationCap, UsersRound } from "lucide-react";
import { PageHeader } from "@/components/PageHeader";
import { Badge } from "@/components/ui/Badge";
import { requireAdminUser } from "@/lib/auth";
import { getJobs, getPlatformStats, getScholarships } from "@/lib/data";
import { accessTierLabel, deadlineLabel } from "@/lib/format";

export const metadata: Metadata = {
  title: "Admin Dashboard"
};

export default async function AdminDashboardPage() {
  await requireAdminUser();
  const [stats, scholarships, jobs] = await Promise.all([
    getPlatformStats(),
    getScholarships({}, { id: "admin", firstName: "Admin", lastName: "User", email: "", membershipType: "PREMIUM_PLUS", scholarshipAccess: true }),
    getJobs({}, { id: "admin", firstName: "Admin", lastName: "User", email: "", membershipType: "PREMIUM_PLUS", scholarshipAccess: true })
  ]);

  return (
    <>
      <PageHeader eyebrow="Admin" title="Platform operations dashboard" description="Manage opportunity quality, users, applications, analytics and monetization from one production-ready admin surface." />
      <section className="py-10">
        <div className="container-page space-y-6">
          <div className="grid gap-4 md:grid-cols-4">
            <AdminStat icon={<UsersRound />} label="Members" value={stats.users} />
            <AdminStat icon={<GraduationCap />} label="Active scholarships" value={stats.scholarships} />
            <AdminStat icon={<BriefcaseBusiness />} label="Active jobs" value={stats.jobs} />
            <AdminStat icon={<BarChart3 />} label="Applications" value={stats.applications} />
          </div>

          <div className="grid gap-6 xl:grid-cols-2">
            <AdminTable
              title="Scholarship management"
              rows={scholarships.slice(0, 8).map((item) => ({
                title: item.title,
                meta: `${item.provider} · ${item.country}`,
                badge: accessTierLabel(item.accessTier),
                deadline: deadlineLabel(item.deadline)
              }))}
            />
            <AdminTable
              title="Jobs management"
              rows={jobs.slice(0, 8).map((item) => ({
                title: item.title,
                meta: `${item.organization} · ${item.location}`,
                badge: accessTierLabel(item.accessTier),
                deadline: deadlineLabel(item.deadline)
              }))}
            />
          </div>

          <div className="grid gap-4 md:grid-cols-3">
            {[
              ["User management", "Review active, premium and suspended users through Prisma-backed records."],
              ["Application management", "Update status, add admin notes and audit document submissions."],
              ["Notifications", "Use dashboard events and email hooks for application status changes."]
            ].map(([title, body]) => (
              <div key={title} className="rounded-lg border border-slate-200 bg-white p-5">
                <h2 className="font-heading text-lg font-extrabold text-navy">{title}</h2>
                <p className="mt-2 text-sm leading-6 text-slate-600">{body}</p>
              </div>
            ))}
          </div>
        </div>
      </section>
    </>
  );
}

function AdminStat({ icon, label, value }: { icon: React.ReactNode; label: string; value: number }) {
  return (
    <div className="rounded-lg border border-slate-200 bg-white p-4">
      <div className="mb-3 text-navy">{icon}</div>
      <div className="font-heading text-3xl font-extrabold text-navy">{value}</div>
      <div className="mt-1 text-xs font-bold uppercase tracking-wide text-slate-500">{label}</div>
    </div>
  );
}

function AdminTable({ title, rows }: { title: string; rows: Array<{ title: string; meta: string; badge: string; deadline: string }> }) {
  return (
    <div className="rounded-lg border border-slate-200 bg-white p-5 premium-shadow">
      <h2 className="mb-4 font-heading text-xl font-extrabold text-navy">{title}</h2>
      <div className="space-y-3">
        {rows.map((row) => (
          <div key={row.title} className="flex items-start justify-between gap-4 rounded-md border border-slate-100 p-3">
            <div>
              <div className="font-semibold text-slate-800">{row.title}</div>
              <div className="mt-1 text-xs text-slate-500">{row.meta}</div>
            </div>
            <div className="text-right">
              <Badge tone={row.badge === "Free" ? "green" : "gold"}>{row.badge}</Badge>
              <div className="mt-1 text-xs font-semibold text-slate-500">{row.deadline}</div>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}
