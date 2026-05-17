import { NextRequest, NextResponse } from "next/server";
import { z } from "zod";
import { hasDatabaseUrl, prisma } from "@/lib/prisma";
import { assertSameOrigin, clientIp, rateLimit, sanitizeEmail } from "@/lib/security";

export const runtime = "nodejs";

const schema = z.object({ email: z.string().email() });

export async function POST(request: NextRequest) {
  if (!assertSameOrigin(request)) {
    return NextResponse.redirect(new URL("/?newsletter=security", request.url));
  }
  const limited = rateLimit(`newsletter:${clientIp(request)}`, 10, 60_000);
  if (!limited.ok) {
    return NextResponse.redirect(new URL("/?newsletter=limited", request.url));
  }

  const form = await request.formData();
  const parsed = schema.safeParse({ email: sanitizeEmail(form.get("email")) });
  if (!parsed.success || !hasDatabaseUrl()) {
    return NextResponse.redirect(new URL("/?newsletter=pending", request.url));
  }

  await prisma.subscriber.upsert({
    where: { email: parsed.data.email },
    update: { isActive: true },
    create: { email: parsed.data.email }
  });

  return NextResponse.redirect(new URL("/?newsletter=success", request.url));
}
