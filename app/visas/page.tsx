import type { Metadata } from "next";
import { FileCheck2, Landmark, Plane } from "lucide-react";
import type { LucideIcon } from "lucide-react";
import { PageHeader } from "@/components/PageHeader";
import { Button } from "@/components/ui/Button";

export const metadata: Metadata = {
  title: "Visa Help"
};

const visaItems: Array<[LucideIcon, string, string]> = [
  [FileCheck2, "Document review", "Check that required documents are complete, ordered and consistent."],
  [Landmark, "Financial proof", "Prepare clearer bank, sponsor and scholarship evidence where required."],
  [Plane, "Submission readiness", "Understand appointment, biometrics and interview preparation steps."]
];

export default function VisasPage() {
  return (
    <>
      <PageHeader eyebrow="Visa help" title="Prepare cleaner visa applications" description="Country-specific guidance for students and professionals organizing documents, proof of funds and interview readiness." />
      <section className="py-12">
        <div className="container-page grid gap-5 md:grid-cols-3">
          {visaItems.map(([Icon, title, body]) => (
            <div key={title} className="rounded-lg border border-slate-200 bg-white p-6">
              <Icon className="mb-4 text-navy" />
              <h2 className="font-heading text-xl font-extrabold text-navy">{title}</h2>
              <p className="mt-2 text-sm leading-6 text-slate-600">{body}</p>
            </div>
          ))}
        </div>
        <div className="container-page mt-7 rounded-lg bg-navy p-6 text-white">
          <h2 className="font-heading text-2xl font-extrabold">Need guided visa support?</h2>
          <p className="mt-2 text-white/72">Request one-time visa application support from your dashboard.</p>
          <Button href="/membership#visa-support" variant="gold" className="mt-5">View Visa Support</Button>
        </div>
      </section>
    </>
  );
}
