import type { Metadata } from "next";
import Link from "next/link";
import { PageHeader } from "@/components/PageHeader";
import { Button } from "@/components/ui/Button";
import { africanCountries } from "@/lib/seed-data";

export const metadata: Metadata = {
  title: "Create Account"
};

type PageProps = {
  searchParams?: Promise<Record<string, string | string[] | undefined>>;
};

export default async function RegisterPage({ searchParams }: PageProps) {
  const params = (await searchParams) ?? {};
  const error = Array.isArray(params.error) ? params.error[0] : params.error;

  return (
    <>
      <PageHeader eyebrow="Join free" title="Create your Global Path account" description="Account creation is required before applying so your documents, submissions and status updates stay organized." />
      <section className="py-10">
        <div className="container-page max-w-2xl">
          <form action="/api/auth/register" method="post" className="card-border rounded-lg bg-white p-6 premium-shadow">
            {error ? <div className="mb-4 rounded-md bg-red-50 p-3 text-sm font-semibold text-red-700">{error}</div> : null}
            <div className="grid gap-4 md:grid-cols-2">
              <label className="block">
                <span className="mb-1.5 block text-sm font-bold text-slate-700">First name</span>
                <input name="firstName" required className="form-input" autoComplete="given-name" />
              </label>
              <label className="block">
                <span className="mb-1.5 block text-sm font-bold text-slate-700">Last name</span>
                <input name="lastName" required className="form-input" autoComplete="family-name" />
              </label>
            </div>
            <div className="mt-4 grid gap-4 md:grid-cols-2">
              <label className="block">
                <span className="mb-1.5 block text-sm font-bold text-slate-700">Email</span>
                <input name="email" type="email" required className="form-input" autoComplete="email" />
              </label>
              <label className="block">
                <span className="mb-1.5 block text-sm font-bold text-slate-700">Phone</span>
                <input name="phone" className="form-input" autoComplete="tel" />
              </label>
            </div>
            <label className="mt-4 block">
              <span className="mb-1.5 block text-sm font-bold text-slate-700">Country</span>
              <select name="country" required className="form-input">
                <option value="">Select country</option>
                {africanCountries.map((country) => <option key={country.code} value={country.name}>{country.name}</option>)}
              </select>
            </label>
            <div className="mt-4 grid gap-4 md:grid-cols-2">
              <label className="block">
                <span className="mb-1.5 block text-sm font-bold text-slate-700">Password</span>
                <input name="password" type="password" required minLength={8} className="form-input" autoComplete="new-password" />
              </label>
              <label className="block">
                <span className="mb-1.5 block text-sm font-bold text-slate-700">Confirm password</span>
                <input name="confirmPassword" type="password" required minLength={8} className="form-input" autoComplete="new-password" />
              </label>
            </div>
            <label className="mt-5 flex items-start gap-3 text-sm text-slate-600">
              <input name="terms" type="checkbox" required className="mt-1" />
              <span>I agree to the Terms of Service and Privacy Policy.</span>
            </label>
            <Button type="submit" className="mt-6 w-full">Create Free Account</Button>
            <p className="mt-5 text-center text-sm text-slate-500">
              Already have an account? <Link href="/login" className="font-semibold text-navy">Sign in</Link>
            </p>
          </form>
        </div>
      </section>
    </>
  );
}
