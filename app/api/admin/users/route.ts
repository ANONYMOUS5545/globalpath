import { NextRequest, NextResponse } from "next/server";
import { z } from "zod";
import { requireAdminUser } from "@/lib/auth";
import { hasDatabaseUrl, prisma } from "@/lib/prisma";
import { assertSameOrigin, sanitizeText } from "@/lib/security";

export const runtime = "nodejs";

const schema = z.object({
  id: z.string().min(2),
  membershipType: z.enum(["FREE", "PREMIUM", "PREMIUM_PLUS"]),
  status: z.enum(["ACTIVE", "SUSPENDED", "PENDING"]),
  scholarshipAccess: z.boolean()
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
    membershipType: sanitizeText(form.get("membershipType"), 40),
    status: sanitizeText(form.get("status"), 40),
    scholarshipAccess: form.get("scholarshipAccess") === "on"
  });

  if (!parsed.success) {
    return NextResponse.redirect(new URL("/admin?error=invalid-user-update", request.url));
  }

  await prisma.user.update({
    where: { id: parsed.data.id },
    data: {
      membershipType: parsed.data.membershipType,
      status: parsed.data.status,
      scholarshipAccess: parsed.data.scholarshipAccess
    }
  });

  return NextResponse.redirect(new URL("/admin?updated=user#users", request.url));
}
