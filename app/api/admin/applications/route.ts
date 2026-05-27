import { NextRequest, NextResponse } from "next/server";
import { z } from "zod";
import { requireAdminUser } from "@/lib/auth";
import { hasDatabaseUrl, prisma } from "@/lib/prisma";
import { assertSameOrigin, sanitizeText } from "@/lib/security";

export const runtime = "nodejs";

const schema = z.object({
  id: z.string().min(2),
  status: z.enum(["SUBMITTED", "UNDER_REVIEW", "ACCEPTED", "REJECTED", "WITHDRAWN"]),
  adminNotes: z.string().max(4000)
});

export async function POST(request: NextRequest) {
  if (!assertSameOrigin(request)) {
    return NextResponse.redirect(new URL("/admin?error=security", request.url));
  }

  await requireAdminUser();
  if (!hasDatabaseUrl()) {
    return NextResponse.redirect(new URL("/admin?error=database", request.url));
  }

  const form = await request.formData();
  const parsed = schema.safeParse({
    id: sanitizeText(form.get("id"), 120),
    status: sanitizeText(form.get("status"), 40),
    adminNotes: sanitizeText(form.get("adminNotes"), 4000)
  });

  if (!parsed.success) {
    return NextResponse.redirect(new URL("/admin?error=invalid-application-update", request.url));
  }

  await prisma.application.update({
    where: { id: parsed.data.id },
    data: {
      status: parsed.data.status,
      adminNotes: parsed.data.adminNotes
    }
  });

  return NextResponse.redirect(new URL("/admin?updated=application#applications", request.url));
}
