import type { Metadata } from "next";
import { PageHeader } from "@/components/PageHeader";

export const metadata: Metadata = {
  title: "Privacy Policy"
};

export default function PrivacyPage() {
  return (
    <>
      <PageHeader eyebrow="Privacy" title="Privacy Policy" description="How Global Path Africa handles accounts, application data and uploaded documents." />
      <LegalContent
        items={[
          ["Data we collect", "We collect account details, membership status, application records and uploaded documents needed to provide the platform."],
          ["How data is used", "Your data is used to authenticate accounts, process applications, track status updates, provide support and improve opportunity quality."],
          ["Document security", "Uploaded documents are handled through secure server routes. Production deployments should connect uploads to private object storage."],
          ["Third parties", "Payments may use external providers. We do not store card numbers or CVV data."]
        ]}
      />
    </>
  );
}

function LegalContent({ items }: { items: Array<[string, string]> }) {
  return (
    <section className="py-12">
      <div className="container-page max-w-3xl rounded-lg border border-slate-200 bg-white p-6 prose-clean">
        {items.map(([title, body]) => (
          <div key={title}>
            <h2 className="mb-2 font-heading text-xl font-extrabold text-navy">{title}</h2>
            <p>{body}</p>
          </div>
        ))}
      </div>
    </section>
  );
}
