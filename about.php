<?php
$pageTitle = 'About Us';
require_once 'includes/config.php';
startSecureSession();
require_once 'includes/header.php';
?>
<div class="page-header">
    <div class="container text-center">
        <h1>About Global Path Africa</h1>
        <p>Empowering Africa's future leaders with access to world-class education and career opportunities</p>
    </div>
</div>
<section class="bg-white">
<div class="container">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:4rem;align-items:center;margin-bottom:4rem;" class="reveal">
        <div>
            <span class="section-badge">Our Mission</span>
            <h2 style="font-family:var(--font-display);font-size:2.2rem;margin:1rem 0 1.25rem;">Breaking Barriers for African Students & Professionals</h2>
            <p style="color:var(--text-muted);line-height:1.8;margin-bottom:1rem;">Global Path Africa was founded with a singular mission: to make international education and career opportunities accessible to every African student and professional, regardless of their background or resources.</p>
            <p style="color:var(--text-muted);line-height:1.8;margin-bottom:1.5rem;">We aggregate scholarships from Erasmus+, DAAD, Chevening, Fulbright, the World Bank, and dozens of other prestigious institutions — then pair them with expert application support to help our members succeed.</p>
            <div style="display:flex;gap:2rem;flex-wrap:wrap;">
                <div style="text-align:center;"><div style="font-family:var(--font-display);font-size:2.5rem;font-weight:700;color:var(--primary);">54</div><div style="font-size:.8rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:1px;">African Countries</div></div>
                <div style="text-align:center;"><div style="font-family:var(--font-display);font-size:2.5rem;font-weight:700;color:var(--primary);">2,500+</div><div style="font-size:.8rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:1px;">Members Helped</div></div>
                <div style="text-align:center;"><div style="font-family:var(--font-display);font-size:2.5rem;font-weight:700;color:var(--primary);">50+</div><div style="font-size:.8rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:1px;">Scholarships Listed</div></div>
            </div>
        </div>
        <div style="background:linear-gradient(135deg,var(--primary-dark),var(--primary));border-radius:var(--radius-lg);padding:3rem;color:white;text-align:center;">
            <div style="font-size:5rem;margin-bottom:1.5rem;">🌍</div>
            <h3 style="font-family:var(--font-display);font-size:1.5rem;margin-bottom:1rem;">Our Vision</h3>
            <p style="opacity:.85;line-height:1.7;">A continent where every talented African has the pathway, knowledge, and support to pursue their dreams globally — and bring those skills back home to drive Africa's development.</p>
        </div>
    </div>

    <!-- Values -->
    <div class="section-header reveal"><span class="section-badge">Our Values</span><h2 class="section-title">What Drives Us</h2></div>
    <div class="grid-4" style="margin-bottom:4rem;">
        <?php foreach([
            ['🎯','Accessibility','We believe every African deserves access to world-class opportunities, not just the privileged few.'],
            ['🤝','Trust','We partner only with verified, reputable institutions and scholarship bodies worldwide.'],
            ['💡','Empowerment','Beyond listings — we provide guidance, support, and expertise to help members actually succeed.'],
            ['🌱','Impact','Every scholarship secured, every job found — we measure our success by yours.'],
        ] as $v): ?>
        <div class="card reveal" style="text-align:center;padding:2rem;">
            <div style="font-size:2.5rem;margin-bottom:1rem;"><?= $v[0] ?></div>
            <h3 style="font-size:1rem;margin-bottom:.5rem;"><?= $v[1] ?></h3>
            <p style="font-size:.875rem;color:var(--text-muted);"><?= $v[2] ?></p>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Contact -->
    <div style="background:linear-gradient(135deg,var(--primary-dark),var(--primary));color:white;padding:3rem;border-radius:var(--radius-lg);text-align:center;" class="reveal">
        <h3 style="font-family:var(--font-display);font-size:2rem;margin-bottom:1rem;">Get in Touch</h3>
        <p style="opacity:.8;margin-bottom:2rem;">Have questions? Our team is ready to help you on your global path.</p>
        <div style="display:flex;gap:2rem;justify-content:center;flex-wrap:wrap;margin-bottom:1.5rem;">
            <div><i class="fab fa-whatsapp" style="color:#4ade80;font-size:1.5rem"></i><br><a href="<?= WHATSAPP_LINK ?>" target="_blank" style="color:#4ade80;font-weight:600;text-decoration:none;">+254 792 579 974</a></div>
            <div><i class="fas fa-envelope" style="color:var(--gold-light);font-size:1.5rem"></i><br><a href="mailto:<?= SITE_EMAIL ?>" style="color:var(--gold-light);font-weight:600;text-decoration:none;"><?= SITE_EMAIL ?></a></div>
        </div>
        <a href="<?= WHATSAPP_LINK ?>?text=Hello+Global+Path+Africa" target="_blank" class="btn btn-primary btn-lg"><i class="fab fa-whatsapp"></i> WhatsApp Us Now</a>
    </div>
</div>
</section>
<?php require_once 'includes/footer.php'; ?>
