import { NextRequest, NextResponse } from "next/server";
import { createSession, verifyPassword } from "@/lib/auth";
import { hasDatabaseUrl, prisma } from "@/lib/prisma";
import { assertSameOrigin, clientIp, isSafeRedirect, rateLimit, sanitizeEmail, sanitizeText } from "@/lib/security";

export const runtime = "nodejs";

export async function POST(request: NextRequest) {
  if (!assertSameOrigin(request)) {
    return NextResponse.redirect(new URL("/login?error=Security%20check%20failed", request.url));
  }

  const limited = rateLimit(`login:${clientIp(request)}`, 8, 60_000);
  if (!limited.ok) {
    return NextResponse.redirect(new URL("/login?error=Too%20many%20attempts", request.url));
  }

  if (!hasDatabaseUrl()) {
    return NextResponse.redirect(new URL("/login?error=Database%20is%20not%20configured", request.url));
  }

  const form = await request.formData();
  const email = sanitizeEmail(form.get("email"));
  const password = String(form.get("password") ?? "");
  const redirectTo = sanitizeText(form.get("redirect"), 300);

  const user = await prisma.user.findUnique({ where: { email } });
  if (!user || !(await verifyPassword(password, user.passwordHash)) || user.status !== "ACTIVE") {
    return NextResponse.redirect(new URL(`/login?error=${encodeURIComponent("Invalid email or password")}`, request.url));
  }

  await prisma.user.update({ where: { id: user.id }, data: { lastLogin: new Date() } });
  await createSession(user.id);

  return NextResponse.redirect(new URL(isSafeRedirect(redirectTo) ? redirectTo : "/dashboard", request.url));
}
