import { mkdir, writeFile } from "fs/promises";
import path from "path";
import { NextRequest, NextResponse } from "next/server";
import { z } from "zod";
import { canAccessTier } from "@/lib/access";
import { getCurrentUser } from "@/lib/auth";
import { getJob, getScholarship } from "@/lib/data";
import { isActiveDeadline, slugify } from "@/lib/format";
import { hasDatabaseUrl, prisma } from "@/lib/prisma";
import { assertSameOrigin, clientIp, rateLimit, sanitizeText } from "@/lib/security";
import type { ApplicationType } from "@/lib/types";

export const runtime = "nodejs";

const schema = z.object({
  type: z.enum(["SCHOLARSHIP", "JOB", "VISA"]),
  referenceId: z.string().min(2).max(180),
  notes: z.string().max(4000).optional()
});

const fileFields = ["cv", "passport", "certificates", "recommendation"];
const allowedTypes = new Set([
  "application/pdf",
  "application/msword",
  "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
  "image/jpeg",
  "image/png"
]);

export async function POST(request: NextRequest) {
  if (!assertSameOrigin(request)) {
    return NextResponse.redirect(new URL("/dashboard?error=security", request.url));
  }

  const limited = rateLimit(`apply:${clientIp(request)}`, 12, 60_000);
  if (!limited.ok) {
    return NextResponse.redirect(new URL("/dashboard?error=limited", request.url));
  }

  const user = await getCurrentUser();
  if (!user) {
    return NextResponse.redirect(new URL("/login?redirect=/dashboard", request.url));
  }

  if (!hasDatabaseUrl()) {
    return NextResponse.redirect(new URL("/dashboard?error=database", request.url));
  }

  const form = await request.formData();
  const parsed = schema.safeParse({
    type: sanitizeText(form.get("type"), 40),
    referenceId: sanitizeText(form.get("referenceId"), 180),
    notes: sanitizeText(form.get("notes"), 4000)
  });

  if (!parsed.success) {
    return NextResponse.redirect(new URL("/dashboard?error=invalid-application", request.url));
  }

  const opportunity = await resolveOpportunity(parsed.data.type, parsed.data.referenceId);
  if (!opportunity) {
    return NextResponse.redirect(new URL("/dashboard?error=opportunity-unavailable", request.url));
  }

  if (!canAccessTier(user, opportunity.accessTier) || !isActiveDeadline(opportunity.deadline)) {
    return NextResponse.redirect(new URL("/membership?error=upgrade-required", request.url));
  }

  const application = await prisma.application.upsert({
    where: {
      userId_type_referenceId: {
        userId: user.id,
        type: parsed.data.type,
        referenceId: opportunity.id
      }
    },
    update: {
      notes: parsed.data.notes
    },
    create: {
      userId: user.id,
      type: parsed.data.type,
      referenceId: opportunity.id,
      scholarshipId: parsed.data.type === "SCHOLARSHIP" ? opportunity.id : undefined,
      jobId: parsed.data.type === "JOB" ? opportunity.id : undefined,
      notes: parsed.data.notes
    }
  });

  await storeFiles(form, application.id);

  return NextResponse.redirect(new URL("/applications?submitted=1", request.url));
}

async function resolveOpportunity(type: ApplicationType, referenceId: string) {
  if (type === "SCHOLARSHIP") {
    const bySlug = await getScholarship(referenceId);
    if (bySlug) return bySlug;
    if (hasDatabaseUrl()) {
      const byId = await prisma.scholarship.findUnique({ where: { id: referenceId } });
      return byId;
    }
  }

  if (type === "JOB") {
    const bySlug = await getJob(referenceId);
    if (bySlug) return bySlug;
    if (hasDatabaseUrl()) {
      const byId = await prisma.job.findUnique({ where: { id: referenceId } });
      return byId;
    }
  }

  return null;
}

async function storeFiles(form: FormData, applicationId: string) {
  const uploadDir = process.env.UPLOAD_DIR ?? "./uploads";
  const maxUploadMb = Number(process.env.MAX_UPLOAD_MB ?? 8);
  const absoluteDir = path.resolve(uploadDir, applicationId);
  await mkdir(absoluteDir, { recursive: true });

  for (const field of fileFields) {
    const file = form.get(field);
    if (!(file instanceof File) || file.size === 0) continue;
    if (file.size > maxUploadMb * 1024 * 1024) continue;
    if (!allowedTypes.has(file.type)) continue;

    const safeName = `${field}-${Date.now()}-${slugify(file.name)}${path.extname(file.name)}`;
    const storageKey = path.join(applicationId, safeName);
    const bytes = Buffer.from(await file.arrayBuffer());
    await writeFile(path.join(absoluteDir, safeName), bytes);

    await prisma.applicationDocument.create({
      data: {
        applicationId,
        label: field,
        fileName: file.name,
        mimeType: file.type,
        size: file.size,
        storageKey
      }
    });
  }
}
