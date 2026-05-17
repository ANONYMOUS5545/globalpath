import { NextRequest, NextResponse } from "next/server";
import { z } from "zod";
import { createSession, hashPassword, verifyPassword } from "@/lib/auth";
import { hasDatabaseUrl, prisma } from "@/lib/prisma";
import { assertSameOrigin, clientIp, rateLimit, sanitizeText } from "@/lib/security";

export const runtime = "nodejs";

const schema = z.object({
  email: z.string().email(),
  password: z.string().min(8).max(200)
});

export async function POST(request: NextRequest) {
  if (!assertSameOrigin(request) || !hasDatabaseUrl()) {
    return NextResponse.redirect(new URL("/admin/login?error=security", request.url));
  }

  const limited = rateLimit(`admin-login:${clientIp(request)}`, 6, 60_000);
  if (!limited.ok) {
    return NextResponse.redirect(new URL("/admin/login?error=limited", request.url));
  }

  const form = await request.formData();
  const parsed = schema.safeParse({
    email: sanitizeText(form.get("email"), 180).toLowerCase(),
    password: String(form.get("password") ?? "")
  });

  const adminEmail = (process.env.ADMIN_EMAIL ?? "admin@globalpathafrica.org").toLowerCase();
  const adminPassword = process.env.ADMIN_PASSWORD ?? "";

  if (!parsed.success || parsed.data.email !== adminEmail || !adminPassword || parsed.data.password !== adminPassword) {
    return NextResponse.redirect(new URL("/admin/login?error=invalid", request.url));
  }

  const user = await prisma.user.upsert({
    where: { email: adminEmail },
    update: {
      status: "ACTIVE",
      membershipType: "PREMIUM_PLUS",
      scholarshipAccess: true,
      lastLogin: new Date()
    },
    create: {
      firstName: "Super",
      lastName: "Admin",
      email: adminEmail,
      passwordHash: await hashPassword(adminPassword),
      membershipType: "PREMIUM_PLUS",
      scholarshipAccess: true,
      emailVerified: true,
      status: "ACTIVE",
      lastLogin: new Date()
    }
  });

  if (!(await verifyPassword(parsed.data.password, user.passwordHash))) {
    await prisma.user.update({ where: { id: user.id }, data: { passwordHash: await hashPassword(adminPassword) } });
  }

  await prisma.admin.upsert({
    where: { email: adminEmail },
    update: { lastLogin: new Date() },
    create: {
      name: "Super Admin",
      email: adminEmail,
      passwordHash: await hashPassword(adminPassword),
      role: "SUPER_ADMIN",
      lastLogin: new Date()
    }
  });

  await createSession(user.id);
  return NextResponse.redirect(new URL("/admin", request.url));
}
