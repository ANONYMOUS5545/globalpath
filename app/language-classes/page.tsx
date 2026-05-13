import type { Metadata } from "next";
import { Languages } from "lucide-react";
import { PageHeader } from "@/components/PageHeader";
import { Button } from "@/components/ui/Button";

export const metadata: Metadata = {
  title: "Language Classes"
};

export default function LanguageClassesPage() {
  return (
    <>
      <PageHeader eyebrow="Language classes" title="Online language preparation for study and work" description="Structured tutoring pathways for students and professionals preparing for international applications." />
      <section className="py-12">
        <div className="container-page grid gap-5 md:grid-cols-4">
          {["German", "French", "English exam prep", "Dutch"].map((language) => (
            <div key={language} className="rounded-lg border border-slate-200 bg-white p-5">
              <Languages className="mb-4 text-navy" />
              <h2 className="font-heading text-lg font-extrabold text-navy">{language}</h2>
              <p className="mt-2 text-sm leading-6 text-slate-600">Live online classes focused on applications, interviews and everyday confidence.</p>
            </div>
          ))}
        </div>
        <div className="container-page mt-7 text-center">
          <Button href="/contact">Ask About Classes</Button>
        </div>
      </section>
    </>
  );
}
