import type { Metadata } from "next";
import { BriefcaseBusiness, CreditCard, FileText, GraduationCap, ShieldCheck, Star } from "lucide-react";
import { PageHeader } from "@/components/PageHeader";
import { Button } from "@/components/ui/Button";
import { Badge } from "@/components/ui/Badge";
import { requireUser } from "@/lib/auth";
import { getDashboardApplications, getJobs, getScholarships } from "@/lib/data";
import { formatDate, membershipLabel } from "@/lib/format";

export const metadata: Metadata = {
  title: "Dashboard"
};

export const dynamic = "force-dynamic";

export default async function DashboardPage() {
  const user = await requireUser();
  const [applications, scholarships, jobs] = await Promise.all([
    getDashboardApplications(user.id),
    getScholarships({}, user),
    getJobs({}, user)
  ]);

  return (
    <>
      <PageHeader
        eyebrow="Dashboard"
        title={`Welcome back, ${user.firstName}`}
        description="Track applications, manage membership access and continue your scholarship or job search."
      />
      <section className="py-10">
        <div className="container-page grid gap-7 lg:grid-cols-[280px_minmax(0,1fr)]">
          <aside className="space-y-4">
            <div className="card-border rounded-lg bg-white p-5 premium-shadow">
              <div className="mb-4 grid h-16 w-16 place-items-center rounded-md bg-navy font-heading text-xl font-extrabold text-white">
                {user.firstName[0]}{user.lastName[0]}
              </div>
              <h2 className="font-heading text-xl font-extrabold text-navy">{user.firstName} {user.lastName}</h2>
              <p className="mt-1 text-sm text-slate-500">{user.email}</p>
              <Badge tone={user.membershipType === "FREE" ? "gray" : "gold"} className="mt-4">
                <Star size={12} /> {membershipLabel(user.membershipType)}
              </Badge>
            </div>
            <nav className="card-border rounded-lg bg-white p-3">
              {[
                ["/dashboard", "Dashboard"],
                ["/applications", "Applications"],
                ["/scholarships", "Scholarships"],
                ["/jobs", "Jobs"],
                ["/membership", "Membership"]
              ].map(([href, label]) => (
                <a key={href} href={href} className="block rounded-md px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 hover:text-navy">
                  {label}
                </a>
              ))}
            </nav>
          </aside>

          <div className="space-y-6">
            <div className="grid gap-4 md:grid-cols-4">
              <Stat icon={<FileText />} label="Applications" value={applications.length} />
              <Stat icon={<GraduationCap />} label="Visible scholarships" value={scholarships.length} />
              <Stat icon={<BriefcaseBusiness />} label="Visible jobs" value={jobs.length} />
              <Stat icon={<CreditCard />} label="Plan" value={membershipLabel(user.membershipType)} small />
            </div>

            <div className="card-border rounded-lg bg-white p-5 premium-shadow">
              <div className="mb-4 flex flex-wrap items-center justify-between gap-3">
                <div>
                  <h2 className="font-heading text-xl font-extrabold text-navy">Quick actions</h2>
                  <p className="mt-1 text-sm text-slate-500">Continue from the most common workflows.</p>
                </div>
                {user.membershipType === "FREE" ? <Button href="/membership#premium" variant="gold">Upgrade</Button> : null}
              </div>
              <div className="grid gap-3 md:grid-cols-3">
                <Button href="/scholarships" variant="outline"><GraduationCap size={16} /> Find Scholarships</Button>
                <Button href="/jobs" variant="outline"><BriefcaseBusiness size={16} /> Browse Jobs</Button>
                <Button href="/applications" variant="outline"><FileText size={16} /> Track Applications</Button>
              </div>
            </div>

            <div className="card-border rounded-lg bg-white p-5 premium-shadow">
              <div className="mb-4 flex items-center justify-between">
                <h2 className="font-heading text-xl font-extrabold text-navy">Recent applications</h2>
                <Button href="/applications" variant="ghost">View all</Button>
              </div>
              {applications.length ? (
                <div className="overflow-x-auto">
                  <table className="w-full text-left text-sm">
                    <thead className="text-xs uppercase tracking-wide text-slate-500">
                      <tr><th className="py-3">Opportunity</th><th>Status</th><th>Date</th></tr>
                    </thead>
                    <tbody className="divide-y divide-slate-100">
                      {applications.slice(0, 5).map((application) => (
                        <tr key={application.id}>
                          <td className="py-3 font-semibold text-slate-800">{application.title}</td>
                          <td><Badge tone="blue">{application.status.replace("_", " ").toLowerCase()}</Badge></td>
                          <td className="text-slate-500">{formatDate(application.createdAt)}</td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              ) : (
                <div className="rounded-md bg-slate-50 p-6 text-center">
                  <ShieldCheck className="mx-auto mb-3 text-navy" />
                  <p className="font-semibold text-slate-700">No applications yet.</p>
                  <p className="mt-1 text-sm text-slate-500">Open a scholarship or job detail page to apply directly.</p>
                </div>
              )}
            </div>
          </div>
        </div>
      </section>
    </>
  );
}

function Stat({ icon, label, value, small }: { icon: React.ReactNode; label: string; value: React.ReactNode; small?: boolean }) {
  return (
    <div className="rounded-lg border border-slate-200 bg-white p-4">
      <div className="mb-3 text-navy">{icon}</div>
      <div className={small ? "font-heading text-lg font-extrabold text-navy" : "font-heading text-3xl font-extrabold text-navy"}>{value}</div>
      <div className="mt-1 text-xs font-bold uppercase tracking-wide text-slate-500">{label}</div>
    </div>
  );
}
