import { NextRequest, NextResponse } from "next/server";
import { z } from "zod";
import { requireAdminUser } from "@/lib/auth";
import { hasDatabaseUrl, prisma } from "@/lib/prisma";
import { assertSameOrigin, sanitizeText } from "@/lib/security";

export const runtime = "nodejs";

const schema = z.object({
  id: z.string().min(2),
  type: z.enum(["scholarship", "job"]),
  accessTier: z.enum(["FREE", "PREMIUM", "PREMIUM_PLUS"]),
  isActive: z.boolean(),
  isFeatured: z.boolean()
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
    type: sanitizeText(form.get("type"), 40),
    accessTier: sanitizeText(form.get("accessTier"), 40),
    isActive: form.get("isActive") === "on",
    isFeatured: form.get("isFeatured") === "on"
  });

  if (!parsed.success) {
    return NextResponse.redirect(new URL("/admin?error=invalid-opportunity-update", request.url));
  }

  const data = {
    accessTier: parsed.data.accessTier,
    isActive: parsed.data.isActive,
    isFeatured: parsed.data.isFeatured
  };

  if (parsed.data.type === "scholarship") {
    await prisma.scholarship.update({ where: { id: parsed.data.id }, data });
  } else {
    await prisma.job.update({ where: { id: parsed.data.id }, data });
  }

  return NextResponse.redirect(new URL("/admin?updated=opportunity#scholarships", request.url));
}
