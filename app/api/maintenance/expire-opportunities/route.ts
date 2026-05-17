import { NextRequest, NextResponse } from "next/server";
import { hasDatabaseUrl, prisma } from "@/lib/prisma";

export const runtime = "nodejs";

export async function GET(request: NextRequest) {
  const secret = process.env.CRON_SECRET;
  if (secret && request.headers.get("authorization") !== `Bearer ${secret}`) {
    return NextResponse.json({ error: "Unauthorized" }, { status: 401 });
  }

  if (!hasDatabaseUrl()) {
    return NextResponse.json({ ok: true, skipped: "DATABASE_URL not configured" });
  }

  const now = new Date();
  const [scholarships, jobs] = await Promise.all([
    prisma.scholarship.updateMany({
      where: { isActive: true, deadline: { lt: now } },
      data: { isActive: false }
    }),
    prisma.job.updateMany({
      where: { isActive: true, deadline: { lt: now } },
      data: { isActive: false }
    })
  ]);

  return NextResponse.json({
    ok: true,
    scholarshipsExpired: scholarships.count,
    jobsExpired: jobs.count
  });
}
