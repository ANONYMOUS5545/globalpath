type PageHeaderProps = {
  eyebrow?: string;
  title: string;
  description: string;
};

export function PageHeader({ eyebrow, title, description }: PageHeaderProps) {
  return (
    <section className="bg-navy py-14 text-white">
      <div className="container-page">
        {eyebrow ? <p className="mb-3 font-accent text-xs font-bold uppercase tracking-[0.28em] text-gold">{eyebrow}</p> : null}
        <h1 className="max-w-3xl text-balance font-heading text-4xl font-extrabold leading-tight md:text-5xl">{title}</h1>
        <p className="mt-4 max-w-2xl text-base leading-7 text-white/75">{description}</p>
      </div>
    </section>
  );
}
