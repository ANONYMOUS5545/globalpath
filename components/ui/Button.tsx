import Link from "next/link";
import { cx } from "@/lib/format";

type ButtonProps = {
  children: React.ReactNode;
  href?: string;
  variant?: "primary" | "gold" | "outline" | "ghost";
  className?: string;
  type?: "button" | "submit";
};

export function Button({ children, href, variant = "primary", className, type = "button" }: ButtonProps) {
  const classes = cx(
    "focus-ring inline-flex min-h-12 items-center justify-center gap-2 whitespace-nowrap rounded-md px-4 py-2.5 text-sm font-bold transition",
    variant === "primary" && "bg-navy text-white hover:bg-navy-900",
    variant === "gold" && "bg-gold text-navy hover:bg-[#c7972f]",
    variant === "outline" && "border border-navy/20 bg-white text-navy hover:border-navy hover:bg-navy/5",
    variant === "ghost" && "text-navy hover:bg-navy/5",
    className
  );

  if (href) {
    return (
      <Link href={href} className={classes}>
        {children}
      </Link>
    );
  }

  return (
    <button type={type} className={classes}>
      {children}
    </button>
  );
}
