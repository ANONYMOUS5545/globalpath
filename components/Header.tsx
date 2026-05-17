import Link from "next/link";
import {
  BookOpenText,
  BriefcaseBusiness,
  CreditCard,
  FileText,
  GraduationCap,
  IdCard,
  LayoutDashboard,
  LogIn,
  Menu,
  Newspaper,
  Plane,
  ShieldCheck,
  UserPen,
  UserRound
} from "lucide-react";
import { Button } from "./ui/Button";
import type { AppUser } from "@/lib/types";
import { brand } from "@/lib/seed-data";
import { membershipLabel } from "@/lib/format";

const nav = [
  { href: "/", label: "Home" },
  { href: "/study-abroad", label: "Study Abroad" },
  { href: "/language-classes", label: "Language Training" },
  { href: "/visas", label: "Visa Help" },
  { href: "/membership", label: "Membership" },
  { href: "/blog", label: "Blog" }
];

const scholarshipNav = [
  { href: "/scholarships", label: "All Scholarships" },
  { href: "/scholarships?level=POSTGRADUATE", label: "Postgraduate" },
  { href: "/scholarships?level=UNDERGRADUATE", label: "Undergraduate" },
  { href: "/scholarships?level=PHD", label: "PhD Funding" },
  { href: "/scholarship-support", label: "Application Support" }
];

const jobNav = [
  { href: "/jobs", label: "All Jobs" },
  { href: "/jobs?category=remote", label: "Remote Jobs" },
  { href: "/jobs?category=onsite", label: "Onsite Jobs" },
  { href: "/jobs?sector=International%20Development", label: "International Development" },
  { href: "/jobs?sector=Healthcare", label: "Healthcare" },
  { href: "/job-resources", label: "Application Resources" }
];

const mobileNav = [
  ...nav.slice(0, 1),
  ...scholarshipNav,
  ...jobNav,
  ...nav.slice(1),
  { href: "/about", label: "About" },
  { href: "/contact", label: "Contact" },
  { href: "/dashboard", label: "Dashboard" }
];

export function Header({ user }: { user: AppUser | null }) {
  const isAdmin = user?.email.toLowerCase() === (process.env.ADMIN_EMAIL ?? "admin@globalpathafrica.org").toLowerCase();

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

        <nav className="hidden items-center gap-1 xl:flex">
          {nav.slice(0, 1).map((item) => (
            <Link key={item.href} href={item.href} className="rounded-md px-2.5 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 hover:text-navy">
              {item.label}
            </Link>
          ))}
          <NavDropdown label="Scholarships" items={scholarshipNav} />
          <NavDropdown label="Jobs Abroad" items={jobNav} />
          {nav.slice(1).map((item) => (
            <Link key={item.href} href={item.href} className="rounded-md px-2.5 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 hover:text-navy">
              {item.label}
            </Link>
          ))}
          <details className="relative">
            <summary className="list-none rounded-md px-2.5 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 hover:text-navy">
              More
            </summary>
            <div className="absolute right-0 top-10 w-64 rounded-md border border-slate-200 bg-white p-2 shadow-xl">
              {[
                ["/scholarship-support", "Scholarship Support"],
                ["/job-resources", "Job Resources"],
                ["/about", "About"],
                ["/contact", "Contact"],
                ["/faq", "FAQ"]
              ].map(([href, label]) => (
                <Link key={href} href={href} className="block rounded-md px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 hover:text-navy">
                  {label}
                </Link>
              ))}
            </div>
          </details>
        </nav>

        <div className="hidden items-center gap-2 md:flex">
          {user ? (
            <>
              <details className="relative">
                <summary className="inline-flex list-none items-center gap-2 rounded-md border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                  <UserRound size={16} />
                  {user.firstName}
                  <span className="rounded bg-gold/15 px-1.5 py-0.5 text-[0.68rem] text-[#8a6416]">{membershipLabel(user.membershipType)}</span>
                </summary>
                <div className="absolute right-0 top-11 w-56 rounded-md border border-slate-200 bg-white p-2 shadow-xl">
                  <AccountLink href="/dashboard" label="Dashboard" icon={<LayoutDashboard size={16} />} />
                  <AccountLink href="/profile" label="Profile" icon={<UserPen size={16} />} />
                  <AccountLink href="/applications" label="Applications" icon={<FileText size={16} />} />
                  <AccountLink href="/payments" label="Payments" icon={<CreditCard size={16} />} />
                  {isAdmin ? <AccountLink href="/admin" label="Admin Dashboard" icon={<ShieldCheck size={16} />} /> : null}
                </div>
              </details>
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

        <details className="relative xl:hidden">
          <summary className="list-none rounded-md border border-slate-200 p-2 text-navy">
            <Menu size={22} />
          </summary>
          <div className="absolute right-0 top-12 max-h-[80vh] w-72 overflow-y-auto rounded-md border border-slate-200 bg-white p-3 shadow-xl">
            {mobileNav.map((item) => (
              <Link key={item.href} href={item.href} className="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                {item.label === "Scholarships" && <GraduationCap size={16} />}
                {item.label === "Jobs" && <BriefcaseBusiness size={16} />}
                {item.label === "Study Abroad" && <Plane size={16} />}
                {item.label === "Language Training" && <BookOpenText size={16} />}
                {item.label === "Visa Help" && <IdCard size={16} />}
                {item.label === "Blog" && <Newspaper size={16} />}
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

function NavDropdown({ label, items }: { label: string; items: Array<{ href: string; label: string }> }) {
  return (
    <details className="relative">
      <summary className="list-none rounded-md px-2.5 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 hover:text-navy">
        {label}
      </summary>
      <div className="absolute left-0 top-10 w-64 rounded-md border border-slate-200 bg-white p-2 shadow-xl">
        {items.map((item) => (
          <Link key={item.href} href={item.href} className="block rounded-md px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 hover:text-navy">
            {item.label}
          </Link>
        ))}
      </div>
    </details>
  );
}

function AccountLink({ href, label, icon }: { href: string; label: string; icon: React.ReactNode }) {
  return (
    <Link href={href} className="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 hover:text-navy">
      {icon}
      {label}
    </Link>
  );
}
