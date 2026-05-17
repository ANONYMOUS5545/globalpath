<?php
$pageTitle = 'Contact Us';
require_once 'includes/config.php';
startSecureSession();
require_once 'includes/header.php';
?>
<div class="page-header"><div class="container text-center"><h1>Contact Us</h1><p>We're here to help you on your global journey</p></div></div>
<section>
<div class="container" style="max-width:800px;">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:2rem;margin-bottom:3rem;">
        <div style="background:white;border-radius:var(--radius);padding:2rem;border:1px solid var(--border);text-align:center;">
            <div style="font-size:3rem;margin-bottom:1rem;">💬</div>
            <h3 style="font-family:var(--font-display);margin-bottom:.5rem;">WhatsApp Support</h3>
            <p style="color:var(--text-muted);font-size:.875rem;margin-bottom:1.25rem;">Fastest response — usually within 2 hours</p>
            <a href="<?= WHATSAPP_LINK ?>?text=Hello+Global+Path+Africa%2C+I+need+help" target="_blank" class="btn" style="background:#25d366;color:white;justify-content:center;width:100%;"><i class="fab fa-whatsapp"></i> +254 792 579 974</a>
        </div>
        <div style="background:white;border-radius:var(--radius);padding:2rem;border:1px solid var(--border);text-align:center;">
            <div style="font-size:3rem;margin-bottom:1rem;">📧</div>
            <h3 style="font-family:var(--font-display);margin-bottom:.5rem;">Email Us</h3>
            <p style="color:var(--text-muted);font-size:.875rem;margin-bottom:1.25rem;">We respond within 24 hours on business days</p>
            <a href="mailto:<?= SITE_EMAIL ?>" class="btn btn-outline" style="justify-content:center;width:100%;"><i class="fas fa-envelope"></i> <?= SITE_EMAIL ?></a>
        </div>
    </div>
    <div style="background:white;border-radius:var(--radius);padding:2rem;border:1px solid var(--border);">
        <h3 style="font-family:var(--font-display);font-size:1.5rem;margin-bottom:1.5rem;">Send a Message</h3>
        <div id="contactMsg"></div>
        <form id="contactForm">
            <div class="form-row">
                <div class="form-group"><label class="form-label">Full Name</label><input type="text" id="cName" class="form-control" placeholder="Your name" required></div>
                <div class="form-group"><label class="form-label">Email</label><input type="email" id="cEmail" class="form-control" placeholder="your@email.com" required></div>
            </div>
            <div class="form-group"><label class="form-label">Message</label><textarea id="cMsg" class="form-control" rows="5" placeholder="How can we help you?" required></textarea></div>
            <button type="submit" class="btn btn-green"><i class="fas fa-paper-plane"></i> Send Message</button>
        </form>
    </div>
</div>
</section>
<script>
document.getElementById('contactForm').addEventListener('submit',function(e){
    e.preventDefault();
    const div=document.getElementById('contactMsg');
    div.innerHTML='<div style="background:#dcfce7;color:#166534;padding:.9rem 1.25rem;border-radius:8px;margin-bottom:1rem;">✅ Message sent! We will get back to you soon via email or WhatsApp.</div>';
    this.reset();
    setTimeout(()=>div.innerHTML='',5000);
});
</script>
<?php require_once 'includes/footer.php'; ?>
