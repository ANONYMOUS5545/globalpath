import type { Metadata } from "next";
import { FileText } from "lucide-react";
import { PageHeader } from "@/components/PageHeader";
import { Badge } from "@/components/ui/Badge";
import { Button } from "@/components/ui/Button";
import { requireUser } from "@/lib/auth";
import { getDashboardApplications } from "@/lib/data";
import { formatDate } from "@/lib/format";

export const metadata: Metadata = {
  title: "Applications"
};

export const dynamic = "force-dynamic";

export default async function ApplicationsPage() {
  const user = await requireUser();
  const applications = await getDashboardApplications(user.id);

  return (
    <>
      <PageHeader eyebrow="Applications" title="Application tracking" description="Track submitted scholarships and jobs, including document-supported direct applications." />
      <section className="py-10">
        <div className="container-page">
          <div className="card-border rounded-lg bg-white p-5 premium-shadow">
            <div className="mb-5 flex flex-wrap items-center justify-between gap-3">
              <h2 className="font-heading text-xl font-extrabold text-navy">All applications</h2>
              <Button href="/scholarships" variant="outline">Find Opportunities</Button>
            </div>
            {applications.length ? (
              <div className="overflow-x-auto">
                <table className="w-full text-left text-sm">
                  <thead className="text-xs uppercase tracking-wide text-slate-500">
                    <tr><th className="py-3">Opportunity</th><th>Type</th><th>Status</th><th>Submitted</th><th>Notes</th></tr>
                  </thead>
                  <tbody className="divide-y divide-slate-100">
                    {applications.map((application) => (
                      <tr key={application.id}>
                        <td className="min-w-72 py-3 font-semibold text-slate-800">{application.title}</td>
                        <td>{application.type.toLowerCase()}</td>
                        <td><Badge tone="blue">{application.status.replace("_", " ").toLowerCase()}</Badge></td>
                        <td className="text-slate-500">{formatDate(application.createdAt)}</td>
                        <td className="max-w-80 truncate text-slate-500">{application.adminNotes || application.notes || "No notes yet"}</td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            ) : (
              <div className="rounded-md bg-slate-50 p-10 text-center">
                <FileText className="mx-auto mb-3 text-navy" size={34} />
                <h2 className="font-heading text-2xl font-extrabold text-navy">No applications yet</h2>
                <p className="mt-2 text-slate-600">Apply from a scholarship or job detail page to start tracking.</p>
              </div>
            )}
          </div>
        </div>
      </section>
    </>
  );
}
