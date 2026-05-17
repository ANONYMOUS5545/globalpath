import type { Metadata } from "next";
import { PageHeader } from "@/components/PageHeader";
import { Button } from "@/components/ui/Button";

export const metadata: Metadata = {
  title: "Password Reset"
};

export default function ForgotPasswordPage() {
  return (
    <>
      <PageHeader eyebrow="Account" title="Password reset" description="Password reset email delivery is ready to connect to your SMTP or transactional email provider." />
      <section className="py-12">
        <div className="container-page max-w-xl rounded-lg border border-slate-200 bg-white p-6">
          <label className="block">
            <span className="mb-1.5 block text-sm font-bold text-slate-700">Email address</span>
            <input type="email" className="form-input" placeholder="you@example.com" />
          </label>
          <Button href="/login" className="mt-5 w-full">Return to Login</Button>
        </div>
      </section>
    </>
  );
}
