import Link from "next/link";
import { ArrowRight, Mail, ShieldCheck } from "lucide-react";
import { brand } from "@/lib/seed-data";

export function Footer() {
  return (
    <footer className="bg-navy text-white">
      <div className="container-page grid gap-10 py-12 md:grid-cols-[1.5fr_1fr_1fr_1.2fr]">
        <div>
          <div className="mb-4 flex items-center gap-3">
            <span className="grid h-11 w-11 place-items-center rounded-md bg-white font-accent text-lg font-bold text-navy">GP</span>
            <div>
              <div className="font-heading text-lg font-extrabold">Global Path Africa</div>
              <div className="font-accent text-xs uppercase tracking-[0.25em] text-gold">Study. Work. Advance.</div>
            </div>
          </div>
          <p className="max-w-sm text-sm leading-7 text-white/70">
            A modern opportunity platform helping African students and professionals find verified scholarships, international jobs and premium application support.
          </p>
          <div className="mt-5 flex items-center gap-2 text-sm text-white/70">
            <ShieldCheck size={16} className="text-gold" />
            Secure accounts, official sources and clear application tracking.
          </div>
        </div>

        <div>
          <h3 className="mb-4 font-heading text-sm font-bold uppercase tracking-wide text-gold">Opportunities</h3>
          <ul className="space-y-2 text-sm text-white/70">
            <li><Link href="/scholarships">Scholarships</Link></li>
            <li><Link href="/jobs?category=remote">Remote Jobs</Link></li>
            <li><Link href="/jobs?category=onsite">Onsite Jobs</Link></li>
            <li><Link href="/membership#premium">Premium Access</Link></li>
          </ul>
        </div>

        <div>
          <h3 className="mb-4 font-heading text-sm font-bold uppercase tracking-wide text-gold">Account</h3>
          <ul className="space-y-2 text-sm text-white/70">
            <li><Link href="/register">Create Account</Link></li>
            <li><Link href="/login">Sign In</Link></li>
            <li><Link href="/dashboard">Dashboard</Link></li>
            <li><Link href="/applications">Applications</Link></li>
          </ul>
        </div>

        <div>
          <h3 className="mb-4 font-heading text-sm font-bold uppercase tracking-wide text-gold">Opportunity Alerts</h3>
          <p className="mb-4 text-sm leading-6 text-white/70">Get new scholarship and job alerts curated for serious applicants.</p>
          <form action="/api/newsletter" method="post" className="flex overflow-hidden rounded-md bg-white">
            <label className="sr-only" htmlFor="footer-email">Email</label>
            <input id="footer-email" name="email" type="email" required placeholder="Email address" className="min-w-0 flex-1 px-3 py-2 text-sm text-slate-900 outline-none" />
            <button type="submit" className="bg-gold px-3 text-navy" aria-label="Subscribe">
              <ArrowRight size={18} />
            </button>
          </form>
          <a href={`mailto:${brand.email}`} className="mt-4 inline-flex items-center gap-2 text-sm text-white/70">
            <Mail size={15} /> {brand.email}
          </a>
        </div>
      </div>
      <div className="border-t border-white/10 py-5">
        <div className="container-page flex flex-wrap items-center justify-between gap-3 text-xs text-white/55">
          <p>© {new Date().getFullYear()} Global Path Africa. All rights reserved.</p>
          <p>Optimized for Vercel, PostgreSQL and secure direct applications.</p>
        </div>
      </div>
    </footer>
  );
}
