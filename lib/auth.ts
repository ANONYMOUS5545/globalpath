import bcrypt from "bcryptjs";
import crypto from "crypto";
import { cookies } from "next/headers";
import { redirect } from "next/navigation";
import { hasDatabaseUrl, prisma } from "./prisma";
import type { AppUser } from "./types";

const cookieName = process.env.SESSION_COOKIE_NAME ?? "gpa_session";
const sessionDays = 7;

function hashToken(token: string) {
  return crypto.createHash("sha256").update(token).digest("hex");
}

function toAppUser(user: {
  id: string;
  firstName: string;
  lastName: string;
  email: string;
  phone: string | null;
  country: string | null;
  membershipType: "FREE" | "PREMIUM" | "PREMIUM_PLUS";
  membershipExpires: Date | null;
  scholarshipAccess: boolean;
}): AppUser {
  return {
    id: user.id,
    firstName: user.firstName,
    lastName: user.lastName,
    email: user.email,
    phone: user.phone,
    country: user.country,
    membershipType: user.membershipType,
    membershipExpires: user.membershipExpires,
    scholarshipAccess: user.scholarshipAccess
  };
}

export async function hashPassword(password: string) {
  return bcrypt.hash(password, 12);
}

export async function verifyPassword(password: string, hash: string) {
  return bcrypt.compare(password, hash);
}

export async function createSession(userId: string) {
  if (!hasDatabaseUrl()) return null;

  const token = crypto.randomBytes(32).toString("hex");
  const expiresAt = new Date(Date.now() + sessionDays * 24 * 60 * 60 * 1000);
  await prisma.session.create({
    data: {
      userId,
      tokenHash: hashToken(token),
      expiresAt
    }
  });

  const cookieStore = await cookies();
  cookieStore.set(cookieName, token, {
    httpOnly: true,
    sameSite: "lax",
    secure: process.env.NODE_ENV === "production",
    path: "/",
    expires: expiresAt
  });

  return token;
}

export async function destroySession() {
  const cookieStore = await cookies();
  const token = cookieStore.get(cookieName)?.value;
  if (token && hasDatabaseUrl()) {
    await prisma.session.deleteMany({ where: { tokenHash: hashToken(token) } });
  }
  cookieStore.delete(cookieName);
}

export async function getCurrentUser(): Promise<AppUser | null> {
  if (!hasDatabaseUrl()) return null;
  const cookieStore = await cookies();
  const token = cookieStore.get(cookieName)?.value;
  if (!token) return null;

  const session = await prisma.session.findUnique({
    where: { tokenHash: hashToken(token) },
    include: { user: true }
  });

  if (!session || session.expiresAt < new Date() || session.user.status !== "ACTIVE") {
    return null;
  }

  return toAppUser(session.user);
}

export async function requireUser() {
  const user = await getCurrentUser();
  if (!user) redirect("/login");
  return user;
}

export async function requireAdminUser() {
  const user = await getCurrentUser();
  if (!user) redirect("/admin/login");
  const adminEmail = (process.env.ADMIN_EMAIL ?? "admin@globalpathafrica.org").toLowerCase();
  if (user.email.toLowerCase() !== adminEmail) redirect("/admin/login");
  return user;
}
