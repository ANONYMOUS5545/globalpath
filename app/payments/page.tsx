import type { Metadata } from "next";
import { CreditCard } from "lucide-react";
import { PageHeader } from "@/components/PageHeader";
import { Button } from "@/components/ui/Button";
import { requireUser } from "@/lib/auth";

export const metadata: Metadata = {
  title: "Payments"
};

export default async function PaymentsPage() {
  await requireUser();
  return (
    <>
      <PageHeader eyebrow="Payments" title="Payment history" description="Membership and support payments are recorded securely without storing card data." />
      <section className="py-12">
        <div className="container-page rounded-lg border border-slate-200 bg-white p-10 text-center">
          <CreditCard className="mx-auto mb-4 text-navy" size={34} />
          <h2 className="font-heading text-2xl font-extrabold text-navy">Payment provider setup pending</h2>
          <p className="mx-auto mt-2 max-w-xl text-slate-600">Configure Stripe, Flutterwave or M-Pesa environment keys to activate live checkout. Bank-transfer pending records are already supported.</p>
          <Button href="/membership" className="mt-5">View Plans</Button>
        </div>
      </section>
    </>
  );
}
