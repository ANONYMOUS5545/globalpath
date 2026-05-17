import type { Metadata } from "next";
import { PageHeader } from "@/components/PageHeader";
import { getBlogPosts } from "@/lib/data";
import { formatDate } from "@/lib/format";

export const metadata: Metadata = {
  title: "Blog"
};

export default async function BlogPage() {
  const posts = await getBlogPosts();

  return (
    <>
      <PageHeader eyebrow="Blog" title="Application guidance and opportunity strategy" description="Practical articles for scholarship, job and study-abroad preparation." />
      <section className="py-12">
        <div className="container-page grid gap-5 md:grid-cols-2">
          {posts.map((post) => (
            <article key={post.id} className="rounded-lg border border-slate-200 bg-white p-6">
              <p className="font-accent text-xs font-bold uppercase tracking-[0.24em] text-gold">{post.category}</p>
              <h2 className="mt-2 font-heading text-xl font-extrabold text-navy">{post.title}</h2>
              <p className="mt-2 text-sm leading-6 text-slate-600">{post.excerpt}</p>
              <div className="mt-5 text-xs font-semibold text-slate-500">{formatDate(post.publishedAt)} · {post.readingTimeMinutes} min read</div>
            </article>
          ))}
        </div>
      </section>
    </>
  );
}
