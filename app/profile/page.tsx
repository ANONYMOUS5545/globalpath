import type { Metadata } from "next";
import { PageHeader } from "@/components/PageHeader";
import { Button } from "@/components/ui/Button";
import { requireUser } from "@/lib/auth";
import { membershipLabel } from "@/lib/format";

export const metadata: Metadata = {
  title: "Profile"
};

export const dynamic = "force-dynamic";

export default async function ProfilePage() {
  const user = await requireUser();
  return (
    <>
      <PageHeader eyebrow="Profile" title="Member profile" description="Review your account details and membership status." />
      <section className="py-12">
        <div className="container-page max-w-2xl rounded-lg border border-slate-200 bg-white p-6">
          <dl className="space-y-4 text-sm">
            <Row label="Name" value={`${user.firstName} ${user.lastName}`} />
            <Row label="Email" value={user.email} />
            <Row label="Country" value={user.country ?? "Not set"} />
            <Row label="Membership" value={membershipLabel(user.membershipType)} />
          </dl>
          <Button href="/dashboard" className="mt-6">Back to Dashboard</Button>
        </div>
      </section>
    </>
  );
}

function Row({ label, value }: { label: string; value: string }) {
  return (
    <div className="flex justify-between gap-5 border-b border-slate-100 pb-3">
      <dt className="font-semibold text-slate-500">{label}</dt>
      <dd className="text-right font-bold text-navy">{value}</dd>
    </div>
  );
}
