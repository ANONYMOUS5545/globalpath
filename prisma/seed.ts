import bcrypt from "bcryptjs";
import { PrismaClient } from "@prisma/client";
import {
  africanCountries,
  fallbackBlogPosts,
  fallbackJobResources,
  fallbackJobs,
  fallbackScholarships
} from "../lib/seed-data";
import { slugify } from "../lib/format";

const prisma = new PrismaClient();

async function main() {
  const adminEmail = process.env.ADMIN_EMAIL ?? "admin@globalpathafrica.org";
  const adminPassword = process.env.ADMIN_PASSWORD ?? "ChangeMe@2026";

  await prisma.admin.upsert({
    where: { email: adminEmail.toLowerCase() },
    update: {},
    create: {
      name: "Super Admin",
      email: adminEmail.toLowerCase(),
      passwordHash: await bcrypt.hash(adminPassword, 12),
      role: "SUPER_ADMIN"
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
