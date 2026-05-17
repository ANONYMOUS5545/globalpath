import type { Metadata } from "next";
import { FileText, ListChecks, MessageCircle } from "lucide-react";
import type { LucideIcon } from "lucide-react";
import { PageHeader } from "@/components/PageHeader";
import { Button } from "@/components/ui/Button";

export const metadata: Metadata = {
  title: "Scholarship Support"
};

const supportItems: Array<[LucideIcon, string, string]> = [
  [FileText, "Statement guidance", "Shape your motivation, goals and fit into a clearer application narrative."],
  [ListChecks, "Document review", "Check transcripts, CV, references and required files before submission."],
  [MessageCircle, "Follow-up support", "Get practical support through priority channels while preparing applications."]
];

export default function ScholarshipSupportPage() {
  return (
    <>
      <PageHeader eyebrow="Scholarship support" title="Stronger scholarship applications, better organized" description="Guided support for statements, documents, recommendation planning and deadline management." />
      <section className="py-12">
        <div className="container-page grid gap-5 md:grid-cols-3">
          {supportItems.map(([Icon, title, body]) => (
            <div key={title} className="rounded-lg border border-slate-200 bg-white p-6">
              <Icon className="mb-4 text-navy" />
              <h2 className="font-heading text-xl font-extrabold text-navy">{title}</h2>
              <p className="mt-2 text-sm leading-6 text-slate-600">{body}</p>
            </div>
          ))}
        </div>
        <div className="container-page mt-7 text-center">
          <Button href="/membership#scholarship-support" variant="gold">Get Scholarship Support</Button>
        </div>
      </section>
    </>
  );
}
