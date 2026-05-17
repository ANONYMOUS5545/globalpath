import { NextRequest, NextResponse } from "next/server";
import { destroySession } from "@/lib/auth";
import { assertSameOrigin } from "@/lib/security";

export const runtime = "nodejs";

export async function POST(request: NextRequest) {
  if (!assertSameOrigin(request)) {
    return NextResponse.redirect(new URL("/", request.url));
  }
  await destroySession();
  return NextResponse.redirect(new URL("/", request.url));
}
