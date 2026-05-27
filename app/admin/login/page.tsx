import type { Metadata } from "next";
import Link from "next/link";
import { PageHeader } from "@/components/PageHeader";
import { PasswordInput } from "@/components/PasswordInput";
import { Button } from "@/components/ui/Button";

export const metadata: Metadata = {
  title: "Admin Login"
};

type PageProps = {
  searchParams?: Promise<Record<string, string | string[] | undefined>>;
};

export default async function AdminLoginPage({ searchParams }: PageProps) {
  const params = (await searchParams) ?? {};
  const error = Array.isArray(params.error) ? params.error[0] : params.error;

  return (
    <>
      <PageHeader eyebrow="Admin" title="Secure admin access" description="Sign in with your configured admin credentials to manage users, applications and opportunities." />
      <section className="py-10">
        <div className="container-page max-w-xl">
          <form action="/api/auth/admin-login" method="post" className="card-border rounded-lg bg-white p-6 premium-shadow">
            {error ? <div className="mb-4 rounded-md bg-red-50 p-3 text-sm font-semibold text-red-700">{error}</div> : null}
            <label className="mb-4 block">
              <span className="mb-1.5 block text-sm font-bold text-slate-700">Admin email</span>
              <input name="email" type="email" required className="form-input" autoComplete="email" />
            </label>
            <div className="mb-5">
              <PasswordInput name="password" label="Admin password" autoComplete="current-password" />
            </div>
            <Button type="submit" className="w-full">Sign In to Admin</Button>
            <p className="mt-5 text-center text-sm text-slate-500">
              <Link href="/" className="font-semibold text-navy">Back to website</Link>
            </p>
          </form>
        </div>
      </section>
    </>
  );
}
