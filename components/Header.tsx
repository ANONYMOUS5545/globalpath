import Link from "next/link";
import { BriefcaseBusiness, GraduationCap, LayoutDashboard, LogIn, Menu, ShieldCheck, UserRound } from "lucide-react";
import { Button } from "./ui/Button";
import type { AppUser } from "@/lib/types";
import { brand } from "@/lib/seed-data";
import { membershipLabel } from "@/lib/format";

const nav = [
  { href: "/", label: "Home" },
  { href: "/scholarships", label: "Scholarships" },
  { href: "/jobs", label: "Jobs" },
  { href: "/membership", label: "Membership" },
  { href: "/dashboard", label: "Dashboard" }
];

export function Header({ user }: { user: AppUser | null }) {
  return (
    <header className="sticky top-0 z-30 border-b border-slate-200/80 bg-white/95 backdrop-blur">
      <div className="hidden border-b border-white/10 bg-navy text-white md:block">
        <div className="container-page flex h-9 items-center justify-between text-xs">
          <div className="flex items-center gap-5">
            <span>{brand.email}</span>
            <a href={`${brand.whatsappUrl}?text=Hello%20Global%20Path%20Africa`} className="text-white/85 hover:text-white">
              +{brand.whatsappNumber}
            </a>
          </div>
          <div className="flex items-center gap-2 text-white/80">
            <ShieldCheck size={14} />
            Official sources, active deadlines and secure applications
          </div>
        </div>
      </div>

      <div className="container-page flex h-18 items-center justify-between gap-4">
        <Link href="/" className="flex items-center gap-3">
          <span className="grid h-11 w-11 place-items-center rounded-md bg-navy font-accent text-lg font-bold text-gold">GP</span>
          <span className="leading-tight">
            <span className="block font-heading text-base font-extrabold tracking-wide text-navy">Global Path</span>
            <span className="block font-accent text-[0.68rem] font-bold uppercase tracking-[0.24em] text-gold">Africa</span>
          </span>
        </Link>

        <nav className="hidden items-center gap-1 lg:flex">
          {nav.map((item) => (
            <Link key={item.href} href={item.href} className="rounded-md px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 hover:text-navy">
              {item.label}
            </Link>
          ))}
        </nav>

        <div className="hidden items-center gap-2 md:flex">
          {user ? (
            <>
              <Link href="/dashboard" className="inline-flex items-center gap-2 rounded-md border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700">
                <UserRound size={16} />
                {user.firstName}
                <span className="rounded bg-gold/15 px-1.5 py-0.5 text-[0.68rem] text-[#8a6416]">{membershipLabel(user.membershipType)}</span>
              </Link>
              <form action="/api/auth/logout" method="post">
                <button className="rounded-md px-3 py-2 text-sm font-semibold text-slate-500 hover:bg-slate-100" type="submit">
                  Logout
                </button>
              </form>
            </>
          ) : (
            <>
              <Button href="/login" variant="outline">
                <LogIn size={16} /> Login
              </Button>
              <Button href="/register" variant="gold">
                Join Free
              </Button>
            </>
          )}
        </div>

        <details className="relative lg:hidden">
          <summary className="list-none rounded-md border border-slate-200 p-2 text-navy">
            <Menu size={22} />
          </summary>
          <div className="absolute right-0 top-12 w-72 rounded-md border border-slate-200 bg-white p-3 shadow-xl">
            {nav.map((item) => (
              <Link key={item.href} href={item.href} className="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                {item.label === "Scholarships" && <GraduationCap size={16} />}
                {item.label === "Jobs" && <BriefcaseBusiness size={16} />}
                {item.label === "Dashboard" && <LayoutDashboard size={16} />}
                {item.label}
              </Link>
            ))}
            <div className="mt-2 border-t border-slate-100 pt-2">
              {user ? <Button href="/dashboard" className="w-full">My Dashboard</Button> : <Button href="/register" variant="gold" className="w-full">Create Account</Button>}
            </div>
          </div>
        </details>
      </div>
    </header>
  );
}
