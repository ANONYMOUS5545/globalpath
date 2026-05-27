"use client";

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
  Ship,
  Stethoscope,
  ChevronDown,
  UserPen,
  UserRound
} from "lucide-react";
import { useRef } from "react";
import { Button } from "./ui/Button";
import type { AppUser } from "@/lib/types";
import { brand } from "@/lib/seed-data";
import { membershipLabel } from "@/lib/format";

const nav = [
  { href: "/", label: "Home" },
  { href: "/membership", label: "Membership" },
  { href: "/blog", label: "Blog" }
];

const scholarshipGroups = [
  {
    heading: "All Scholarships",
    href: "/scholarships",
    items: [
      { href: "/scholarships?level=UNDERGRADUATE", label: "Undergraduate" },
      { href: "/scholarships?level=POSTGRADUATE", label: "Postgraduate" },
      { href: "/scholarships?level=PHD", label: "PhD Funding" },
      { href: "/scholarships?coverage=FULL", label: "Fully Funded" },
      { href: "/scholarships?tier=PREMIUM", label: "Premium Scholarships" }
    ]
  },
  {
    heading: "Scholarship Support",
    href: "/scholarship-support",
    items: [
      { href: "/scholarship-support", label: "Application Support" },
      { href: "/membership#scholarship-support", label: "Paid Review Support" }
    ]
  }
];

const jobGroups = [
  {
    heading: "All Jobs",
    href: "/jobs",
    items: [
      { href: "/jobs?category=remote", label: "Remote Jobs" },
      { href: "/jobs?category=onsite", label: "Onsite Jobs" },
      { href: "/jobs?workplace=HYBRID", label: "Hybrid Jobs" }
    ]
  },
  {
    heading: "Job Categories",
    items: [
      { href: "/jobs?sector=Technology", label: "Technology" },
      { href: "/jobs?sector=Healthcare", label: "Healthcare and Caregiver" },
      { href: "/jobs?sector=International%20Development", label: "International Development" },
      { href: "/jobs?sector=Maritime", label: "Sea and Cruise Jobs" },
      { href: "/jobs?sector=Education", label: "Education" }
    ]
  },
  {
    heading: "Resources",
    href: "/job-resources",
    items: [
      { href: "/job-resources", label: "Application Resources" },
      { href: "/jobs?tier=PREMIUM", label: "Premium Job Tracks" }
    ]
  }
];

const educationGroups = [
  {
    heading: "Education",
    href: "/study-abroad",
    items: [
      { href: "/study-abroad", label: "Study Abroad" },
      { href: "/language-classes", label: "Language Classes" },
      { href: "/visas", label: "Visa Help" },
      { href: "/membership#visa-support", label: "Visa Support" }
    ]
  }
];

const mobileGroups = [
  { label: "Scholarships", groups: scholarshipGroups },
  { label: "Jobs Abroad", groups: jobGroups },
  { label: "Education", groups: educationGroups }
];

export function Header({ user, isAdmin }: { user: AppUser | null; isAdmin: boolean }) {
  const headerRef = useRef<HTMLElement>(null);
  const closeMenus = () => {
    headerRef.current?.querySelectorAll("details[open]").forEach((item) => {
      item.removeAttribute("open");
    });
  };

  return (
    <header ref={headerRef} className="sticky top-0 z-30 border-b border-slate-200/80 bg-white/95 backdrop-blur">
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
            Curated listings, active deadlines and secure applications
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
          {nav.slice(0, 1).map((item) => (
            <Link key={item.href} href={item.href} className="rounded-md px-2.5 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 hover:text-navy">
              {item.label}
            </Link>
          ))}
          <GroupedNavDropdown label="Scholarships" groups={scholarshipGroups} onNavigate={closeMenus} />
          <GroupedNavDropdown label="Jobs Abroad" groups={jobGroups} onNavigate={closeMenus} />
          <GroupedNavDropdown label="Education" groups={educationGroups} onNavigate={closeMenus} />
          {nav.slice(1).map((item) => (
            <Link key={item.href} href={item.href} onClick={closeMenus} className="rounded-md px-2.5 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 hover:text-navy">
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
                <Link key={href} href={href} onClick={closeMenus} className="block rounded-md px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 hover:text-navy">
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
                  <AccountLink href="/dashboard" label="Dashboard" icon={<LayoutDashboard size={16} />} onNavigate={closeMenus} />
                  <AccountLink href="/profile" label="Profile" icon={<UserPen size={16} />} onNavigate={closeMenus} />
                  <AccountLink href="/applications" label="Applications" icon={<FileText size={16} />} onNavigate={closeMenus} />
                  <AccountLink href="/payments" label="Payments" icon={<CreditCard size={16} />} onNavigate={closeMenus} />
                  {isAdmin ? <AccountLink href="/admin" label="Admin Dashboard" icon={<ShieldCheck size={16} />} onNavigate={closeMenus} /> : null}
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

        <details className="relative lg:hidden">
          <summary className="list-none rounded-md border border-slate-200 p-2 text-navy">
            <Menu size={22} />
          </summary>
          <div className="absolute right-0 top-12 max-h-[80vh] w-72 overflow-y-auto rounded-md border border-slate-200 bg-white p-3 shadow-xl">
            <MobileLink href="/" label="Home" onNavigate={closeMenus} />
            {mobileGroups.map((section) => (
              <details key={section.label} className="border-b border-slate-100 py-1">
                <summary className="flex list-none items-center justify-between rounded-md px-3 py-2 text-sm font-extrabold text-navy">
                  {section.label}
                  <ChevronDown size={15} />
                </summary>
                {section.groups.map((group) => (
                  <details key={group.heading} className="ml-2 py-1">
                    <summary className="flex list-none items-center justify-between rounded-md px-3 py-2 text-sm font-bold text-slate-700 hover:bg-slate-100">
                      {group.heading}
                      <ChevronDown size={14} />
                    </summary>
                    {group.href ? <MobileLink href={group.href} label="View all" onNavigate={closeMenus} inset /> : null}
                    {group.items.map((item) => <MobileLink key={item.href} href={item.href} label={item.label} onNavigate={closeMenus} inset />)}
                  </details>
                ))}
              </details>
            ))}
            {[...nav.slice(1), { href: "/about", label: "About" }, { href: "/contact", label: "Contact" }, { href: "/dashboard", label: "Dashboard" }].map((item) => (
              <MobileLink key={item.href} href={item.href} label={item.label} onNavigate={closeMenus} />
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

function GroupedNavDropdown({
  label,
  groups,
  onNavigate
}: {
  label: string;
  groups: Array<{ heading: string; href?: string; items: Array<{ href: string; label: string }> }>;
  onNavigate: () => void;
}) {
  return (
    <details className="relative">
      <summary className="list-none rounded-md px-2.5 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 hover:text-navy">
        {label}
      </summary>
      <div className="absolute left-0 top-10 w-80 rounded-md border border-slate-200 bg-white p-2 shadow-xl">
        {groups.map((group) => (
          <details key={group.heading} className="border-b border-slate-100 py-1 last:border-0" open={groups.length === 1}>
            <summary className="flex cursor-pointer list-none items-center justify-between rounded-md px-3 py-2 text-sm font-extrabold text-navy hover:bg-slate-50">
              <span>{group.heading}</span>
              <ChevronDown size={15} />
            </summary>
            {group.href ? (
              <Link href={group.href} onClick={onNavigate} className="ml-3 mt-1 block rounded-md px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 hover:text-navy">
                View all
              </Link>
            ) : null}
            {group.items.map((item) => (
              <Link key={item.href} href={item.href} onClick={onNavigate} className="ml-3 block rounded-md px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 hover:text-navy">
                {item.label}
              </Link>
            ))}
          </details>
        ))}
      </div>
    </details>
  );
}

function AccountLink({ href, label, icon, onNavigate }: { href: string; label: string; icon: React.ReactNode; onNavigate: () => void }) {
  return (
    <Link href={href} onClick={onNavigate} className="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 hover:text-navy">
      {icon}
      {label}
    </Link>
  );
}

function MobileLink({ href, label, onNavigate, inset = false }: { href: string; label: string; onNavigate: () => void; inset?: boolean }) {
  return (
    <Link href={href} onClick={onNavigate} className={`flex items-center gap-2 rounded-md px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 ${inset ? "ml-4" : ""}`}>
      {label.includes("Scholarship") && <GraduationCap size={16} />}
      {label.includes("Job") && <BriefcaseBusiness size={16} />}
      {label.includes("Sea") && <Ship size={16} />}
      {label.includes("Healthcare") && <Stethoscope size={16} />}
      {label === "Study Abroad" && <Plane size={16} />}
      {label === "Language Classes" && <BookOpenText size={16} />}
      {label === "Visa Help" && <IdCard size={16} />}
      {label === "Blog" && <Newspaper size={16} />}
      {label === "Dashboard" && <LayoutDashboard size={16} />}
      {label}
    </Link>
  );
}
