import { PrismaClient } from "@prisma/client";
import { PrismaPg } from "@prisma/adapter-pg";
import pg from "pg";

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

  const databaseUrl = new URL(process.env.DATABASE_URL);
  databaseUrl.searchParams.delete("sslmode");

  const pool = new pg.Pool({
    connectionString: databaseUrl.toString(),
    ssl: {
      rejectUnauthorized: false
    }
  });

  return new PrismaClient({ adapter: new PrismaPg(pool) });
}

export const prisma = globalForPrisma.prisma ?? createPrismaClient();

if (process.env.NODE_ENV !== "production") {
  globalForPrisma.prisma = prisma;
}
