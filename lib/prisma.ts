import { PrismaClient } from "@prisma/client";
import { PrismaPg } from "@prisma/adapter-pg";

const globalForPrisma = globalThis as unknown as { prisma?: PrismaClient };

export function getDatabaseUrl() {
  return process.env.DATABASE_URL?.trim() || "";
}

export function hasDatabaseUrl() {
  return Boolean(getDatabaseUrl());
}

function createPrismaClient() {
  const connectionString = getDatabaseUrl();
  if (!connectionString) {
    return new Proxy(
      {},
      {
        get() {
          throw new Error("DATABASE_URL is not configured.");
        }
      }
    ) as PrismaClient;
  }

  const adapter = new PrismaPg({
    connectionString
  });

  return new PrismaClient({ adapter });
}

export const prisma = globalForPrisma.prisma ?? createPrismaClient();

if (process.env.NODE_ENV !== "production") {
  globalForPrisma.prisma = prisma;
}
