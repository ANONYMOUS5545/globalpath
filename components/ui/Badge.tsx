import { cx } from "@/lib/format";

type BadgeProps = {
  children: React.ReactNode;
  tone?: "navy" | "gold" | "green" | "gray" | "red" | "blue";
  className?: string;
};

export function Badge({ children, tone = "gray", className }: BadgeProps) {
  const tones = {
    navy: "bg-navy text-white",
    gold: "bg-gold/15 text-[#8a6416] ring-1 ring-gold/25",
    green: "bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100",
    gray: "bg-slate-100 text-slate-700 ring-1 ring-slate-200",
    red: "bg-red-50 text-red-700 ring-1 ring-red-100",
    blue: "bg-blue-50 text-blue-700 ring-1 ring-blue-100"
  };

  return (
    <span className={cx("inline-flex items-center gap-1 rounded-md px-2.5 py-1 text-xs font-semibold", tones[tone], className)}>
      {children}
    </span>
  );
}
