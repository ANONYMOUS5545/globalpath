import type { Metadata } from "next";
import Link from "next/link";
import { PageHeader } from "@/components/PageHeader";
import { PasswordInput } from "@/components/PasswordInput";
import { Button } from "@/components/ui/Button";

export const metadata: Metadata = {
  title: "Login"
};

type PageProps = {
  searchParams?: Promise<Record<string, string | string[] | undefined>>;
};

export default async function LoginPage({ searchParams }: PageProps) {
  const params = (await searchParams) ?? {};
  const redirect = Array.isArray(params.redirect) ? params.redirect[0] : params.redirect ?? "/dashboard";
  const error = Array.isArray(params.error) ? params.error[0] : params.error;

  return (
    <>
      <PageHeader eyebrow="Account" title="Welcome back" description="Sign in to apply directly, upload documents and track scholarship or job applications." />
      <section className="py-10">
        <div className="container-page max-w-xl">
          <form action="/api/auth/login" method="post" className="card-border rounded-lg bg-white p-6 premium-shadow">
            <input type="hidden" name="redirect" value={redirect} />
            {error ? <div className="mb-4 rounded-md bg-red-50 p-3 text-sm font-semibold text-red-700">{error}</div> : null}
            <label className="mb-4 block">
              <span className="mb-1.5 block text-sm font-bold text-slate-700">Email address</span>
              <input name="email" type="email" required className="form-input" autoComplete="email" />
            </label>
            <div className="mb-5">
              <PasswordInput name="password" label="Password" autoComplete="current-password" />
            </div>
            <Button type="submit" className="w-full">Sign In</Button>
            <div className="mt-5 flex justify-between text-sm">
              <Link href="/register" className="font-semibold text-navy">Create account</Link>
              <Link href="/forgot-password" className="text-slate-500">Forgot password?</Link>
            </div>
          </form>
        </div>
      </section>
    </>
  );
}
