import bcrypt from "bcryptjs";
import { PrismaClient } from "@prisma/client";
import { PrismaPg } from "@prisma/adapter-pg";
import pg from "pg";
import {
  africanCountries,
  fallbackBlogPosts,
  fallbackJobResources,
  fallbackJobs,
  fallbackScholarships
} from "../lib/seed-data";
import { slugify } from "../lib/format";

if (!process.env.DATABASE_URL) {
  throw new Error("DATABASE_URL is required to seed the database.");
}

const databaseUrl = new URL(process.env.DATABASE_URL);
databaseUrl.searchParams.delete("sslmode");

const pool = new pg.Pool({
  connectionString: databaseUrl.toString(),
  ssl: {
    rejectUnauthorized: false
  }
});

const prisma = new PrismaClient({ adapter: new PrismaPg(pool) });

async function main() {
  const adminEmail = process.env.ADMIN_EMAIL ?? "admin@globalpathafrica.org";
  const adminPassword = process.env.ADMIN_PASSWORD ?? "ChangeMe@2026";
  const adminPasswordHash = await bcrypt.hash(adminPassword, 12);

  await prisma.admin.upsert({
    where: { email: adminEmail.toLowerCase() },
    update: {},
    create: {
      name: "Super Admin",
      email: adminEmail.toLowerCase(),
      passwordHash: adminPasswordHash,
      role: "SUPER_ADMIN"
    }
  });

  await prisma.user.upsert({
    where: { email: adminEmail.toLowerCase() },
    update: {
      passwordHash: adminPasswordHash,
      status: "ACTIVE",
      membershipType: "PREMIUM_PLUS",
      scholarshipAccess: true
    },
    create: {
      firstName: "Global Path",
      lastName: "Admin",
      email: adminEmail.toLowerCase(),
      passwordHash: adminPasswordHash,
      country: "Kenya",
      membershipType: "PREMIUM_PLUS",
      scholarshipAccess: true,
      status: "ACTIVE",
      emailVerified: true
    }
  });

  for (const country of africanCountries) {
    await prisma.africanCountry.upsert({
      where: { name: country.name },
      update: country,
      create: country
    });
  }

  for (const scholarship of fallbackScholarships) {
    await prisma.scholarship.upsert({
      where: { slug: scholarship.slug },
      update: scholarship,
      create: scholarship
    });
  }

  for (const job of fallbackJobs) {
    await prisma.job.upsert({
      where: { slug: job.slug },
      update: job,
      create: job
    });
  }

  for (const post of fallbackBlogPosts) {
    await prisma.blogPost.upsert({
      where: { slug: post.slug },
      update: post,
      create: post
    });
  }

  for (const resource of fallbackJobResources) {
    await prisma.jobResource.upsert({
      where: { resourceKey: resource.resourceKey },
      update: resource,
      create: resource
    });
  }

  console.log("Seeded Global Path Africa with active opportunities and admin account.");
}

main()
  .catch((error) => {
    console.error(error);
    process.exit(1);
  })
  .finally(async () => {
    await prisma.$disconnect();
  });
