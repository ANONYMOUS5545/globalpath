import { spawnSync } from "node:child_process";

const databaseUrl =
  process.env.DIRECT_URL?.trim() ||
  process.env.POSTGRES_URL_NON_POOLING?.trim() ||
  process.env.DATABASE_URL?.trim() ||
  process.env.POSTGRES_PRISMA_URL?.trim() ||
  process.env.POSTGRES_URL?.trim();

if (!databaseUrl) {
  console.log("No hosted database URL found; skipping prisma db push.");
  process.exit(0);
}

function isSupabasePooler(url) {
  try {
    const parsed = new URL(url);
    return parsed.hostname.includes("pooler.supabase.com") || parsed.port === "6543";
  } catch {
    return false;
  }
}

if (isSupabasePooler(databaseUrl)) {
  console.log("Supabase pooler database URL detected; skipping prisma db push during build.");
  console.log("Set DIRECT_URL or POSTGRES_URL_NON_POOLING to run prisma db push from Vercel.");
  process.exit(0);
}

const result = spawnSync("npx", ["prisma", "db", "push"], {
  stdio: "inherit",
  shell: true,
  env: {
    ...process.env,
    DATABASE_URL: databaseUrl
  }
});

process.exit(result.status ?? 1);
