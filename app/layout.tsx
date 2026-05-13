import type { Metadata } from "next";
import "@fontsource/inter/400.css";
import "@fontsource/inter/500.css";
import "@fontsource/inter/600.css";
import "@fontsource/inter/700.css";
import "@fontsource/inter/800.css";
import "@fontsource/montserrat/600.css";
import "@fontsource/montserrat/700.css";
import "@fontsource/montserrat/800.css";
import "@fontsource/cinzel/500.css";
import "@fontsource/cinzel/600.css";
import "@fontsource/cinzel/700.css";
import "./globals.css";
import { Header } from "@/components/Header";
import { Footer } from "@/components/Footer";
import { PathBot } from "@/components/PathBot";
import { getCurrentUser } from "@/lib/auth";

export const metadata: Metadata = {
  metadataBase: new URL(process.env.NEXT_PUBLIC_SITE_URL ?? "http://localhost:3000"),
  title: {
    default: "Global Path Africa",
    template: "%s | Global Path Africa"
  },
  description:
    "A premium platform for African students and professionals to discover scholarships, international jobs, study abroad support and guided application help.",
  keywords: [
    "Africa scholarships",
    "study abroad Africa",
    "international jobs",
    "scholarship application support",
    "Global Path Africa"
  ],
  openGraph: {
    title: "Global Path Africa",
    description: "Scholarships, jobs abroad and study pathways for African students and professionals.",
    siteName: "Global Path Africa",
    type: "website"
  }
};

export default async function RootLayout({ children }: Readonly<{ children: React.ReactNode }>) {
  const user = await getCurrentUser();

  return (
    <html lang="en">
      <body>
        <Header user={user} />
        <main>{children}</main>
        <Footer />
        <PathBot />
      </body>
    </html>
  );
}
