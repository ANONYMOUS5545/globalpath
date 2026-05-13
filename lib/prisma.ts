import { PrismaClient } from "@prisma/client";
import { PrismaPg } from "@prisma/adapter-pg";

const globalForPrisma = globalThis as unknown as { prisma?: PrismaClient };

export function hasDatabaseUrl() {
  return Boolean(process.env.DATABASE_URL);
}

function createPrismaClient() {
  if (!process.env.DATABASE_URL) {
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
    connectionString: process.env.DATABASE_URL
  });

  return new PrismaClient({ adapter });
}

export const prisma = globalForPrisma.prisma ?? createPrismaClient();

if (process.env.NODE_ENV !== "production") {
  globalForPrisma.prisma = prisma;
}
