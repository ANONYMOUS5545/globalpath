import type { Metadata } from "next";
import { PageHeader } from "@/components/PageHeader";

export const metadata: Metadata = {
  title: "FAQ"
};

const faqs = [
  ["Do I need an account to apply?", "Yes. Account creation is mandatory so applications, uploads and status updates can be tracked securely."],
  ["Are expired scholarships shown?", "No. The platform filters out scholarships whose deadlines have passed and includes a maintenance route for database cleanup."],
  ["What is premium access?", "Premium and Premium Plus unlock higher-value scholarships, advanced job listings and support features while keeping useful free access available."],
  ["Can I apply directly from the website?", "Yes. Opportunity detail pages include direct application forms with CV, passport, certificate and recommendation uploads."]
];

export default function FAQPage() {
  return (
    <>
      <PageHeader eyebrow="FAQ" title="Common questions" description="Clear answers about accounts, applications, premium access and opportunity quality." />
      <section className="py-12">
        <div className="container-page max-w-3xl space-y-4">
          {faqs.map(([question, answer]) => (
            <details key={question} className="rounded-lg border border-slate-200 bg-white p-5">
              <summary className="cursor-pointer font-heading text-lg font-extrabold text-navy">{question}</summary>
              <p className="mt-3 leading-7 text-slate-600">{answer}</p>
            </details>
          ))}
        </div>
      </section>
    </>
  );
}
