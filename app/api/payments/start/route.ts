import { NextRequest, NextResponse } from "next/server";
import { getCurrentUser } from "@/lib/auth";
import { hasDatabaseUrl, prisma } from "@/lib/prisma";
import { clientIp, rateLimit, sanitizeText } from "@/lib/security";

export const runtime = "nodejs";

const planMap: Record<string, { amount: number; name: string }> = {
  premium_monthly: { amount: 9.99, name: "Premium Monthly" },
  premium_plus_monthly: { amount: 19.99, name: "Premium Plus Monthly" },
  scholarship_support: { amount: 49.99, name: "Scholarship Support" },
  visa_support: { amount: 79.99, name: "Visa Support" }
};

export async function GET(request: NextRequest) {
  const limited = rateLimit(`payment:${clientIp(request)}`, 12, 60_000);
  if (!limited.ok) {
    return NextResponse.redirect(new URL("/membership?error=limited", request.url));
  }

  const user = await getCurrentUser();
  if (!user) {
    return NextResponse.redirect(new URL("/login?redirect=/membership", request.url));
  }

  const plan = sanitizeText(request.nextUrl.searchParams.get("plan"), 80);
  const item = planMap[plan];
  if (!item || !hasDatabaseUrl()) {
    return NextResponse.redirect(new URL("/membership?payment=configuration-required", request.url));
  }

  await prisma.payment.create({
    data: {
      userId: user.id,
      transactionId: `pending-${user.id}-${Date.now()}`,
      gateway: "BANK_TRANSFER",
      amount: item.amount,
      currency: "USD",
      plan,
      status: "PENDING",
      metadata: { requestedPlanName: item.name, note: "Payment provider integration pending environment keys." }
    }
  });

  return NextResponse.redirect(new URL("/dashboard?payment=pending", request.url));
}
