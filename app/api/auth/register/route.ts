import { NextRequest, NextResponse } from "next/server";
import { z } from "zod";
import { createSession, hashPassword } from "@/lib/auth";
import { hasDatabaseUrl, prisma } from "@/lib/prisma";
import { assertSameOrigin, clientIp, rateLimit, sanitizeEmail, sanitizeText } from "@/lib/security";

export const runtime = "nodejs";

const schema = z.object({
  firstName: z.string().min(2).max(80),
  lastName: z.string().min(2).max(80),
  email: z.string().email(),
  phone: z.string().max(40).optional(),
  country: z.string().min(2).max(120),
  password: z.string().min(8).max(128),
  confirmPassword: z.string().min(8).max(128),
  terms: z.literal("on")
}).refine((data) => data.password === data.confirmPassword, {
  message: "Passwords do not match",
  path: ["confirmPassword"]
});

export async function POST(request: NextRequest) {
  if (!assertSameOrigin(request)) {
    return NextResponse.redirect(new URL("/register?error=Security%20check%20failed", request.url));
  }

  const limited = rateLimit(`register:${clientIp(request)}`, 6, 60_000);
  if (!limited.ok) {
    return NextResponse.redirect(new URL("/register?error=Too%20many%20attempts", request.url));
  }

  if (!hasDatabaseUrl()) {
    return NextResponse.redirect(new URL("/register?error=Database%20is%20not%20configured", request.url));
  }

  const form = await request.formData();
  const parsed = schema.safeParse({
    firstName: sanitizeText(form.get("firstName"), 80),
    lastName: sanitizeText(form.get("lastName"), 80),
    email: sanitizeEmail(form.get("email")),
    phone: sanitizeText(form.get("phone"), 40),
    country: sanitizeText(form.get("country"), 120),
    password: String(form.get("password") ?? ""),
    confirmPassword: String(form.get("confirmPassword") ?? ""),
    terms: form.get("terms")
  });

  if (!parsed.success) {
    return NextResponse.redirect(new URL(`/register?error=${encodeURIComponent(parsed.error.issues[0]?.message ?? "Invalid form")}`, request.url));
  }

  const existing = await prisma.user.findUnique({ where: { email: parsed.data.email } });
  if (existing) {
    return NextResponse.redirect(new URL("/register?error=Account%20already%20exists", request.url));
  }

  const user = await prisma.user.create({
    data: {
      firstName: parsed.data.firstName,
      lastName: parsed.data.lastName,
      email: parsed.data.email,
      phone: parsed.data.phone,
      country: parsed.data.country,
      nationality: parsed.data.country,
      passwordHash: await hashPassword(parsed.data.password),
      ipAddress: clientIp(request)
    }
  });

  await createSession(user.id);
  return NextResponse.redirect(new URL("/dashboard", request.url));
}
