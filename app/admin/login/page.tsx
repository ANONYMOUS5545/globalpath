import type { Metadata } from "next";
import { LockKeyhole } from "lucide-react";
import { PageHeader } from "@/components/PageHeader";
import { Button } from "@/components/ui/Button";

export const metadata: Metadata = {
  title: "Admin Login"
};

type PageProps = {
  searchParams?: Promise<Record<string, string | string[] | undefined>>;
};

export default async function AdminLoginPage({ searchParams }: PageProps) {
  const params = (await searchParams) ?? {};
  const error = params.error ? "Invalid admin credentials or missing database configuration." : null;

  return (
    <>
      <PageHeader eyebrow="Admin" title="Secure admin access" description="Sign in with the configured admin credentials to manage users, opportunities, applications and platform operations." />
      <section className="py-10">
        <div className="container-page max-w-xl">
          <form action="/api/auth/admin-login" method="post" className="card-border rounded-lg bg-white p-6 premium-shadow">
            <div className="mb-5 grid h-12 w-12 place-items-center rounded-md bg-navy text-white">
              <LockKeyhole size={22} />
            </div>
            {error ? <div className="mb-4 rounded-md border border-red-200 bg-red-50 p-3 text-sm font-semibold text-red-700">{error}</div> : null}
            <label className="mb-4 block">
              <span className="mb-1.5 block text-sm font-bold text-slate-700">Admin email</span>
              <input name="email" type="email" required className="form-input" autoComplete="username" />
            </label>
            <label className="mb-5 block">
              <span className="mb-1.5 block text-sm font-bold text-slate-700">Password</span>
              <input name="password" type="password" required className="form-input" autoComplete="current-password" />
            </label>
            <Button type="submit" className="w-full">Login to Admin</Button>
          </form>
        </div>
      </section>
    </>
  );
}
