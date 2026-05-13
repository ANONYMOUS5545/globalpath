import type { Metadata } from "next";
import { PageHeader } from "@/components/PageHeader";

export const metadata: Metadata = {
  title: "Terms of Service"
};

export default function TermsPage() {
  return (
    <>
      <PageHeader eyebrow="Terms" title="Terms of Service" description="Responsible use terms for opportunity discovery, direct applications and premium support." />
      <section className="py-12">
        <div className="container-page max-w-3xl rounded-lg border border-slate-200 bg-white p-6 prose-clean">
          <h2 className="mb-2 font-heading text-xl font-extrabold text-navy">Platform role</h2>
          <p>Global Path Africa helps users discover and organize applications. Final admission, hiring, visa and funding decisions remain with official institutions and employers.</p>
          <h2 className="mb-2 font-heading text-xl font-extrabold text-navy">User responsibilities</h2>
          <p>Users must provide accurate information, upload lawful documents and verify official application requirements before submission.</p>
          <h2 className="mb-2 font-heading text-xl font-extrabold text-navy">Premium services</h2>
          <p>Premium support improves preparation and organization. It does not guarantee scholarship awards, job offers or visa approvals.</p>
        </div>
      </section>
    </>
  );
}
