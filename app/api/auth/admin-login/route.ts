import { NextRequest, NextResponse } from "next/server";
import { createSession, hashPassword } from "@/lib/auth";
import { hasDatabaseUrl, prisma } from "@/lib/prisma";
import { assertSameOrigin, clientIp, rateLimit, sanitizeEmail } from "@/lib/security";

export const runtime = "nodejs";

export async function POST(request: NextRequest) {
  if (!assertSameOrigin(request)) {
    return NextResponse.redirect(new URL("/admin/login?error=Security%20check%20failed", request.url));
  }

  const limited = rateLimit(`admin-login:${clientIp(request)}`, 6, 60_000);
  if (!limited.ok) {
    return NextResponse.redirect(new URL("/admin/login?error=Too%20many%20attempts", request.url));
  }

  if (!hasDatabaseUrl()) {
    return NextResponse.redirect(new URL("/admin/login?error=Database%20is%20not%20configured", request.url));
  }

  const form = await request.formData();
  const email = sanitizeEmail(form.get("email"));
  const password = String(form.get("password") ?? "");
  const adminEmail = sanitizeEmail(process.env.ADMIN_EMAIL ?? "globalpathafrica@gmail.com");
  const adminPassword = process.env.ADMIN_PASSWORD ?? "";

  if (!adminPassword || email !== adminEmail || password !== adminPassword) {
    return NextResponse.redirect(new URL("/admin/login?error=Invalid%20admin%20credentials", request.url));
  }

  const user = await prisma.user.upsert({
    where: { email: adminEmail },
    update: {
      passwordHash: await hashPassword(adminPassword),
      status: "ACTIVE",
      membershipType: "PREMIUM_PLUS",
      scholarshipAccess: true,
      emailVerified: true,
      lastLogin: new Date()
    },
    create: {
      firstName: "Admin",
      lastName: "User",
      email: adminEmail,
      passwordHash: await hashPassword(adminPassword),
      membershipType: "PREMIUM_PLUS",
      scholarshipAccess: true,
      emailVerified: true,
      status: "ACTIVE",
      lastLogin: new Date()
    }
  });

  await createSession(user.id);
  return NextResponse.redirect(new URL("/admin", request.url));
}
