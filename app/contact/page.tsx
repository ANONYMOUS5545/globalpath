import type { Metadata } from "next";
import { Mail, MessageCircle } from "lucide-react";
import { PageHeader } from "@/components/PageHeader";
import { Button } from "@/components/ui/Button";
import { brand } from "@/lib/seed-data";

export const metadata: Metadata = {
  title: "Contact"
};

export default function ContactPage() {
  return (
    <>
      <PageHeader eyebrow="Contact" title="Talk to Global Path Africa" description="Reach the support team for scholarships, jobs, membership, visa support and application guidance." />
      <section className="py-12">
        <div className="container-page grid gap-6 md:grid-cols-2">
          <div className="rounded-lg border border-slate-200 bg-white p-6">
            <Mail className="mb-4 text-navy" />
            <h2 className="font-heading text-xl font-extrabold text-navy">Email support</h2>
            <p className="mt-2 text-slate-600">{brand.email}</p>
            <Button href={`mailto:${brand.email}`} className="mt-5">Send Email</Button>
          </div>
          <div className="rounded-lg border border-slate-200 bg-white p-6">
            <MessageCircle className="mb-4 text-navy" />
            <h2 className="font-heading text-xl font-extrabold text-navy">WhatsApp support</h2>
            <p className="mt-2 text-slate-600">+{brand.whatsappNumber}</p>
            <Button href={`${brand.whatsappUrl}?text=Hello%20Global%20Path%20Africa`} variant="gold" className="mt-5">Open WhatsApp</Button>
          </div>
        </div>
      </section>
    </>
  );
}
