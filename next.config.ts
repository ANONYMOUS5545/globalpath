import type { NextConfig } from "next";

const nextConfig: NextConfig = {
  reactStrictMode: true,
  poweredByHeader: false,
  images: {
    remotePatterns: [
      {
        protocol: "https",
        hostname: "images.unsplash.com"
      },
      {
        protocol: "https",
        hostname: "images.pexels.com"
      }
    ]
  },
  async redirects() {
    return [
      { source: "/index.php", destination: "/", permanent: true },
      { source: "/scholarships.php", destination: "/scholarships", permanent: true },
      { source: "/scholarship-detail.php", destination: "/scholarships", permanent: false },
      { source: "/jobs.php", destination: "/jobs", permanent: true },
      { source: "/job-detail.php", destination: "/jobs", permanent: false },
      { source: "/job-resources.php", destination: "/job-resources", permanent: true },
      { source: "/membership.php", destination: "/membership", permanent: true },
      { source: "/login.php", destination: "/login", permanent: true },
      { source: "/register.php", destination: "/register", permanent: true },
      { source: "/dashboard.php", destination: "/dashboard", permanent: true },
      { source: "/applications.php", destination: "/applications", permanent: true },
      { source: "/about.php", destination: "/about", permanent: true },
      { source: "/contact.php", destination: "/contact", permanent: true },
      { source: "/faq.php", destination: "/faq", permanent: true },
      { source: "/privacy.php", destination: "/privacy", permanent: true },
      { source: "/terms.php", destination: "/terms", permanent: true },
      { source: "/visas.php", destination: "/visas", permanent: true },
      { source: "/language-classes.php", destination: "/language-classes", permanent: true },
      { source: "/study-abroad.php", destination: "/study-abroad", permanent: true },
      { source: "/scholarship-support.php", destination: "/scholarship-support", permanent: true },
      { source: "/blog.php", destination: "/blog", permanent: true },
      { source: "/payments.php", destination: "/payments", permanent: true },
      { source: "/profile.php", destination: "/profile", permanent: true },
      { source: "/admin/login.php", destination: "/admin/login", permanent: true },
      { source: "/admin/dashboard.php", destination: "/admin", permanent: true },
      { source: "/admin/users.php", destination: "/admin/users", permanent: true },
      { source: "/admin/scholarships.php", destination: "/admin/scholarships", permanent: true },
      { source: "/admin/jobs.php", destination: "/admin/jobs", permanent: true },
      { source: "/admin/applications.php", destination: "/admin/applications", permanent: true },
      { source: "/admin/payments.php", destination: "/admin/payments", permanent: true },
      { source: "/admin/messages.php", destination: "/admin/messages", permanent: true },
      { source: "/admin/subscribers.php", destination: "/admin/subscribers", permanent: true },
      { source: "/admin/blog.php", destination: "/admin/blog", permanent: true },
      { source: "/admin/job-resources.php", destination: "/admin/job-resources", permanent: true }
    ];
  },
  async headers() {
    return [
      {
        source: "/(.*)",
        headers: [
          { key: "X-DNS-Prefetch-Control", value: "on" },
          { key: "X-Frame-Options", value: "SAMEORIGIN" },
          { key: "X-Content-Type-Options", value: "nosniff" },
          { key: "Referrer-Policy", value: "strict-origin-when-cross-origin" },
          {
            key: "Permissions-Policy",
            value: "camera=(), microphone=(), geolocation=(self), payment=(self)"
          }
        ]
      }
    ];
  }
};

export default nextConfig;
