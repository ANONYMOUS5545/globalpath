# Global Path Africa

Modernized Next.js migration of the existing Global Path Africa XAMPP/PHP platform.

## Stack

- Next.js 16 App Router
- React 19
- TypeScript
- Tailwind CSS 4
- Prisma 7
- PostgreSQL
- Vercel-ready API routes

The legacy PHP files are intentionally left in place as migration reference material. The production app is served by the new Next.js `app/` architecture.

## What Is Included

- Premium navy/gold UI system with self-hosted Inter, Montserrat and Cinzel fonts
- Active scholarship filtering by current deadline
- Expanded, balanced European scholarship data from official university and institution sources
- Free, Premium and Premium Plus access tiers
- Account registration and login with hashed passwords and HTTP-only sessions
- Direct scholarship and job applications
- CV, passport, certificates and recommendation letter upload handling
- User dashboard and application tracking
- Admin operations dashboard
- Jobs split by remote, onsite and hybrid work modes, with sector filtering
- Trusted job resource directory for remote, onsite, sea, caregiver and licensing routes
- Restored PathBot support launcher with WhatsApp handoff
- Prisma PostgreSQL schema and seed script
- Legacy `.php` route redirects
- Vercel config and scheduled expired-opportunity cleanup route

## Local Setup

1. Install dependencies:

```bash
npm install
```

2. Copy environment variables:

```bash
copy .env.example .env
```

3. Set at minimum:

```bash
DATABASE_URL="postgresql://USER:PASSWORD@HOST:5432/globalpath?schema=public"
AUTH_SECRET="replace-with-at-least-32-random-characters"
ADMIN_EMAIL="admin@globalpathafrica.org"
ADMIN_PASSWORD="ChangeMe@2026"
```

4. Generate Prisma Client:

```bash
npm run db:generate
```

5. Run migrations and seed data:

```bash
npm run db:migrate
npm run db:seed
```

6. Start development server:

```bash
npm run dev
```

Open `http://localhost:3000`.

## Verification

```bash
npm run lint
npm run typecheck
npm run build
```

All three commands pass in this workspace.

## Vercel Deployment

1. Create a Vercel project from the GitHub repository.
2. Add all required environment variables from `.env.example`.
3. Provision PostgreSQL, then set `DATABASE_URL`.
4. Run the deployment build with:

```bash
npm run build
```

5. Run production migrations before or during deployment:

```bash
npm run db:deploy
```

6. Seed production only when intentionally bootstrapping:

```bash
npm run db:seed
```

## File Uploads

Local uploads are stored under `UPLOAD_DIR` and ignored by Git. For Vercel production, connect `/api/applications` to durable private object storage such as Vercel Blob, S3 or Cloudflare R2 before accepting large live document uploads.

## Security Notes

- Passwords use bcrypt.
- Sessions use HTTP-only, SameSite cookies.
- Mutating routes enforce same-origin checks.
- API routes include in-memory rate limiting.
- Inputs are validated with Zod and sanitized before persistence.
- Expired opportunities can be deactivated by the scheduled Vercel cron route.

## Legacy Compatibility

Common PHP routes such as `/scholarships.php`, `/jobs.php`, `/membership.php`, `/login.php` and `/dashboard.php` redirect to their modern Next.js equivalents.
