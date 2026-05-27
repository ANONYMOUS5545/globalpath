import type { Metadata } from "next";
import {
  BarChart3,
  Bell,
  BriefcaseBusiness,
  CreditCard,
  FileText,
  GraduationCap,
  LayoutDashboard,
  LockKeyhole,
  MessageSquare,
  Settings2,
  ShieldCheck,
  UsersRound
} from "lucide-react";
import { Badge } from "@/components/ui/Badge";
import { requireAdminUser } from "@/lib/auth";
import { getJobs, getPlatformStats, getScholarships } from "@/lib/data";
import { accessTierLabel, deadlineLabel, formatDate, membershipLabel, money } from "@/lib/format";
import { hasDatabaseUrl, prisma } from "@/lib/prisma";

export const metadata: Metadata = {
  title: "Admin Dashboard"
};

export const dynamic = "force-dynamic";

const adminMenu = [
  [LayoutDashboard, "Dashboard"],
  [UsersRound, "Users"],
  [FileText, "Applications"],
  [GraduationCap, "Scholarships"],
  [BriefcaseBusiness, "Jobs"],
  [CreditCard, "Finance"],
  [MessageSquare, "Messages"],
  [Bell, "Notifications"],
  [Settings2, "Site controls"]
] as const;

export default async function AdminDashboardPage() {
  const admin = await requireAdminUser();
  const [stats, scholarships, jobs, operations] = await Promise.all([
    getPlatformStats(),
    getScholarships({}, { id: "admin", firstName: "Admin", lastName: "User", email: "", membershipType: "PREMIUM_PLUS", scholarshipAccess: true }),
    getJobs({}, { id: "admin", firstName: "Admin", lastName: "User", email: "", membershipType: "PREMIUM_PLUS", scholarshipAccess: true }),
    getAdminOperations()
  ]);

  return (
    <section className="bg-[#eef1f6] py-8">
      <div className="container-page grid gap-6 xl:grid-cols-[92px_300px_minmax(0,1fr)]">
        <aside className="hidden rounded-lg border border-slate-200 bg-white p-3 shadow-sm xl:block">
          <div className="grid h-12 place-items-center rounded-md bg-navy font-heading font-extrabold text-gold">GP</div>
          <div className="mt-6 space-y-2">
            {adminMenu.map(([Icon, label]) => (
              <a key={label} href={`#${label.toLowerCase().replace(/\s+/g, "-")}`} className="grid h-11 place-items-center rounded-md text-slate-600 hover:bg-slate-100 hover:text-navy" aria-label={label}>
                <Icon size={18} />
              </a>
            ))}
          </div>
        </aside>

        <aside className="rounded-lg border border-slate-200 bg-white shadow-sm">
          <div className="border-b border-slate-100 p-5">
            <div className="flex items-center gap-3">
              <div className="grid h-10 w-10 place-items-center rounded-md bg-navy font-heading font-extrabold text-gold">GP</div>
              <div>
                <h1 className="font-heading text-xl font-extrabold text-navy">Global Path Admin</h1>
                <p className="text-xs font-semibold text-slate-500">Super admin workspace</p>
              </div>
            </div>
          </div>
          <nav className="border-b border-slate-100 p-4">
            <p className="mb-2 px-3 text-xs font-bold uppercase tracking-wide text-slate-400">Menu</p>
            {adminMenu.map(([Icon, label], index) => (
              <a
                key={label}
                href={`#${label.toLowerCase().replace(/\s+/g, "-")}`}
                className={`mb-1 flex items-center gap-3 rounded-md px-3 py-2.5 text-sm font-semibold ${index === 0 ? "bg-navy text-white" : "text-slate-700 hover:bg-slate-100 hover:text-navy"}`}
              >
                <Icon size={17} /> {label}
              </a>
            ))}
          </nav>
          <div className="p-4">
            <div className="rounded-lg border border-gold/30 bg-gold/10 p-4">
              <ShieldCheck className="mb-2 text-navy" />
              <p className="text-sm font-bold text-navy">Current role: Super Admin</p>
              <p className="mt-1 text-xs leading-5 text-slate-600">Can manage users, applications, content access, financial reports and site operations.</p>
            </div>
          </div>
          <div className="border-t border-slate-100 p-4">
            <p className="text-sm font-bold text-slate-800">{admin.firstName} {admin.lastName}</p>
            <p className="break-words text-xs text-slate-500">{admin.email}</p>
          </div>
        </aside>

        <main className="min-w-0 space-y-6">
          <div id="dashboard" className="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div className="flex flex-wrap items-start justify-between gap-4">
              <div>
                <p className="font-accent text-xs font-bold uppercase tracking-[0.28em] text-gold">Operations</p>
                <h2 className="mt-2 font-heading text-3xl font-extrabold text-navy">Admin command center</h2>
                <p className="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Real platform controls for users, applications, opportunity quality, payments and support follow-up.</p>
              </div>
              <Badge tone="green"><ShieldCheck size={12} /> Protected</Badge>
            </div>
          </div>

          <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <AdminStat icon={<GraduationCap />} label="Scholarships" value={stats.scholarships} />
            <AdminStat icon={<BriefcaseBusiness />} label="Jobs" value={stats.jobs} />
            <AdminStat icon={<FileText />} label="Applications" value={stats.applications} />
            <AdminStat icon={<CreditCard />} label="Revenue tracked" value={money(operations.finance.completedRevenue)} small />
          </div>

          <section id="finance" className="grid gap-4 lg:grid-cols-3">
            <FinanceCard title="Completed revenue" value={money(operations.finance.completedRevenue)} note={`${operations.finance.completedPayments} completed payments`} />
            <FinanceCard title="Pending revenue" value={money(operations.finance.pendingRevenue)} note={`${operations.finance.pendingPayments} pending payment requests`} />
            <FinanceCard title="Plan mix" value={`${operations.finance.premiumUsers} premium`} note="Premium and Premium Plus members with elevated access" />
          </section>

          <section id="users" className="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div className="mb-4 flex items-center justify-between gap-3">
              <div>
                <h2 className="font-heading text-xl font-extrabold text-navy">User management</h2>
                <p className="mt-1 text-sm text-slate-500">Upgrade plans, suspend accounts, restore access and grant scholarship privileges.</p>
              </div>
              <UsersRound className="text-navy" />
            </div>
            <div className="overflow-x-auto">
              <table className="w-full min-w-[920px] text-left text-sm">
                <thead className="text-xs uppercase tracking-wide text-slate-500">
                  <tr><th className="py-3">User</th><th>Country</th><th>Plan</th><th>Status</th><th>Last active</th><th>Admin action</th></tr>
                </thead>
                <tbody className="divide-y divide-slate-100">
                  {operations.users.map((user) => (
                    <tr key={user.id}>
                      <td className="py-3">
                        <div className="font-semibold text-slate-800">{user.firstName} {user.lastName}</div>
                        <div className="text-xs text-slate-500">{user.email}</div>
                      </td>
                      <td>{user.country ?? "Not provided"}</td>
                      <td><Badge tone={user.membershipType === "FREE" ? "green" : "gold"}>{membershipLabel(user.membershipType)}</Badge></td>
                      <td>{user.status.toLowerCase()}</td>
                      <td className="text-slate-500">{formatDate(user.lastLogin ?? user.createdAt)}</td>
                      <td>
                        <form action="/api/admin/users" method="post" className="grid gap-2 md:grid-cols-[1fr_1fr_1fr_auto]">
                          <input type="hidden" name="id" value={user.id} />
                          <select name="membershipType" defaultValue={user.membershipType} className="form-input py-2 text-xs">
                            <option value="FREE">Free</option>
                            <option value="PREMIUM">Premium</option>
                            <option value="PREMIUM_PLUS">Premium Plus</option>
                          </select>
                          <select name="status" defaultValue={user.status} className="form-input py-2 text-xs">
                            <option value="ACTIVE">Active</option>
                            <option value="SUSPENDED">Suspended</option>
                            <option value="PENDING">Pending</option>
                          </select>
                          <label className="flex items-center gap-2 text-xs font-semibold text-slate-600">
                            <input type="checkbox" name="scholarshipAccess" defaultChecked={user.scholarshipAccess} />
                            Scholarship access
                          </label>
                          <button type="submit" className="rounded-md bg-navy px-3 py-2 text-xs font-bold text-white hover:bg-navy-900">Save</button>
                        </form>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </section>

          <section id="applications" className="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div className="mb-4 flex items-center justify-between gap-3">
              <div>
                <h2 className="font-heading text-xl font-extrabold text-navy">Application management</h2>
                <p className="mt-1 text-sm text-slate-500">Update member-visible statuses and admin notes.</p>
              </div>
              <FileText className="text-navy" />
            </div>
            <div className="grid gap-4">
              {operations.applications.length ? operations.applications.map((application) => (
                <form key={application.id} action="/api/admin/applications" method="post" className="rounded-lg border border-slate-100 p-4">
                  <input type="hidden" name="id" value={application.id} />
                  <div className="mb-3 flex flex-wrap justify-between gap-3">
                    <div>
                      <h3 className="font-bold text-slate-800">{application.title}</h3>
                      <p className="text-xs text-slate-500">{application.userEmail} - {application.type.toLowerCase()} - {formatDate(application.createdAt)}</p>
                    </div>
                    <Badge tone="blue">{application.status.toLowerCase().replace("_", " ")}</Badge>
                  </div>
                  <div className="grid gap-3 md:grid-cols-[220px_minmax(0,1fr)_auto]">
                    <select name="status" defaultValue={application.status} className="form-input">
                      <option value="SUBMITTED">Submitted</option>
                      <option value="UNDER_REVIEW">Under review</option>
                      <option value="ACCEPTED">Accepted</option>
                      <option value="REJECTED">Rejected</option>
                      <option value="WITHDRAWN">Withdrawn</option>
                    </select>
                    <textarea name="adminNotes" defaultValue={application.adminNotes ?? ""} className="form-input min-h-12" placeholder="Member-visible update" />
                    <button type="submit" className="rounded-md bg-navy px-4 py-2 text-sm font-bold text-white hover:bg-navy-900">Update</button>
                  </div>
                </form>
              )) : (
                <div className="rounded-md bg-slate-50 p-8 text-center text-sm font-semibold text-slate-600">No applications yet.</div>
              )}
            </div>
          </section>

          <section id="site-controls" className="grid gap-4 md:grid-cols-3">
            {[
              ["Opportunity access", "Premium gates, active deadlines and official links are controlled from scholarship and job records."],
              ["Financial reports", "Payment records, pending bank-transfer requests and completed revenue are visible in this dashboard."],
              ["Support workflow", "Applications, notes and notifications are ready for member follow-up from the admin workspace."]
            ].map(([title, body]) => (
              <div key={title} className="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <LockKeyhole className="mb-3 text-navy" />
                <h2 className="font-heading text-lg font-extrabold text-navy">{title}</h2>
                <p className="mt-2 text-sm leading-6 text-slate-600">{body}</p>
              </div>
            ))}
          </section>

          <section id="scholarships" className="grid gap-6 xl:grid-cols-2">
            <OpportunityManager
              title="Scholarship controls"
              rows={scholarships.slice(0, 8).map((item) => ({
                id: item.id,
                type: "scholarship",
                title: item.title,
                meta: `${item.provider} - ${item.country}`,
                accessTier: item.accessTier,
                isActive: item.isActive,
                isFeatured: item.isFeatured,
                deadline: deadlineLabel(item.deadline)
              }))}
            />
            <OpportunityManager
              title="Job controls"
              rows={jobs.slice(0, 8).map((item) => ({
                id: item.id,
                type: "job",
                title: item.title,
                meta: `${item.organization} - ${item.location}`,
                accessTier: item.accessTier,
                isActive: item.isActive,
                isFeatured: item.isFeatured,
                deadline: deadlineLabel(item.deadline)
              }))}
            />
          </section>
        </main>
      </div>
    </section>
  );
}

async function getAdminOperations() {
  if (!hasDatabaseUrl()) {
    return {
      users: [],
      applications: [],
      finance: { completedRevenue: 0, pendingRevenue: 0, completedPayments: 0, pendingPayments: 0, premiumUsers: 0 }
    };
  }

  const [users, applications, payments, premiumUsers] = await Promise.all([
    prisma.user.findMany({ orderBy: [{ lastLogin: "desc" }, { createdAt: "desc" }], take: 12 }),
    prisma.application.findMany({ include: { user: true, scholarship: true, job: true }, orderBy: { createdAt: "desc" }, take: 12 }),
    prisma.payment.findMany({ orderBy: { paymentDate: "desc" }, take: 200 }),
    prisma.user.count({ where: { membershipType: { in: ["PREMIUM", "PREMIUM_PLUS"] } } })
  ]);

  const completed = payments.filter((payment) => payment.status === "COMPLETED");
  const pending = payments.filter((payment) => payment.status === "PENDING");

  return {
    users,
    applications: applications.map((application) => ({
      id: application.id,
      title: application.scholarship?.title ?? application.job?.title ?? application.referenceId,
      userEmail: application.user.email,
      type: application.type,
      status: application.status,
      adminNotes: application.adminNotes,
      createdAt: application.createdAt
    })),
    finance: {
      completedRevenue: completed.reduce((sum, payment) => sum + Number(payment.amount), 0),
      pendingRevenue: pending.reduce((sum, payment) => sum + Number(payment.amount), 0),
      completedPayments: completed.length,
      pendingPayments: pending.length,
      premiumUsers
    }
  };
}

function AdminStat({ icon, label, value, small }: { icon: React.ReactNode; label: string; value: React.ReactNode; small?: boolean }) {
  return (
    <div className="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
      <div className="mb-3 text-navy">{icon}</div>
      <div className={small ? "font-heading text-xl font-extrabold text-navy" : "font-heading text-3xl font-extrabold text-navy"}>{value}</div>
      <div className="mt-1 text-xs font-bold uppercase tracking-wide text-slate-500">{label}</div>
    </div>
  );
}

function FinanceCard({ title, value, note }: { title: string; value: string; note: string }) {
  return (
    <div className="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
      <BarChart3 className="mb-3 text-navy" />
      <h2 className="font-heading text-lg font-extrabold text-navy">{title}</h2>
      <div className="mt-2 font-heading text-3xl font-extrabold text-navy">{value}</div>
      <p className="mt-1 text-sm text-slate-500">{note}</p>
    </div>
  );
}

function OpportunityManager({
  title,
  rows
}: {
  title: string;
  rows: Array<{ id: string; type: "scholarship" | "job"; title: string; meta: string; accessTier: "FREE" | "PREMIUM" | "PREMIUM_PLUS"; isActive: boolean; isFeatured: boolean; deadline: string }>;
}) {
  return (
    <div className="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
      <h2 className="mb-4 font-heading text-xl font-extrabold text-navy">{title}</h2>
      <div className="space-y-3">
        {rows.map((row) => (
          <form key={row.id} action="/api/admin/opportunities" method="post" className="rounded-md border border-slate-100 p-3">
            <input type="hidden" name="id" value={row.id} />
            <input type="hidden" name="type" value={row.type} />
            <div className="min-w-0">
              <div className="font-semibold text-slate-800">{row.title}</div>
              <div className="mt-1 text-xs text-slate-500">{row.meta}</div>
            </div>
            <div className="mt-3 grid gap-2 md:grid-cols-[1fr_auto_auto_auto] md:items-center">
              <select name="accessTier" defaultValue={row.accessTier} className="form-input py-2 text-xs">
                <option value="FREE">Free</option>
                <option value="PREMIUM">Premium</option>
                <option value="PREMIUM_PLUS">Premium Plus</option>
              </select>
              <label className="flex items-center gap-2 text-xs font-semibold text-slate-600">
                <input type="checkbox" name="isActive" defaultChecked={row.isActive} />
                Active
              </label>
              <label className="flex items-center gap-2 text-xs font-semibold text-slate-600">
                <input type="checkbox" name="isFeatured" defaultChecked={row.isFeatured} />
                Featured
              </label>
              <button type="submit" className="rounded-md bg-navy px-3 py-2 text-xs font-bold text-white hover:bg-navy-900">Save</button>
            </div>
            <div className="mt-2 flex items-center justify-between gap-2 text-xs text-slate-500">
              <span>{row.deadline}</span>
              <Badge tone={row.accessTier === "FREE" ? "green" : "gold"}>{accessTierLabel(row.accessTier)}</Badge>
            </div>
          </form>
        ))}
      </div>
    </div>
  );
}
