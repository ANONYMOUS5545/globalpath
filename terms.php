<?php $pageTitle='Terms of Service'; require_once 'includes/config.php'; startSecureSession(); require_once 'includes/header.php'; ?>
<div class="page-header"><div class="container"><h1>Terms of Service</h1><p>Last updated: <?= date('d F Y') ?></p></div></div>
<section><div class="container" style="max-width:800px;">
<div style="background:white;border-radius:var(--radius);padding:2.5rem;border:1px solid var(--border);line-height:1.8;color:var(--text-muted);">
<h2 style="font-family:var(--font-display);color:var(--text);margin-bottom:1rem;">1. Acceptance of Terms</h2>
<p>By accessing Global Path Africa, you agree to these Terms of Service. If you do not agree, please do not use our platform.</p>
<h2 style="font-family:var(--font-display);color:var(--text);margin:1.5rem 0 1rem;">2. Services</h2>
<p>Global Path Africa provides information about international scholarships, job opportunities, and visa guidance. We do not guarantee acceptance to any scholarship or job programme. Our scholarship application support service provides guidance only.</p>
<h2 style="font-family:var(--font-display);color:var(--text);margin:1.5rem 0 1rem;">3. Membership & Payments</h2>
<p>Membership fees are charged in advance. All sales are final unless otherwise agreed. We reserve the right to suspend accounts that violate these terms. Payment data handled by third-party gateways (Stripe, PayPal, Flutterwave, M-Pesa) — we do not store raw card data.</p>
<h2 style="font-family:var(--font-display);color:var(--text);margin:1.5rem 0 1rem;">4. User Accounts</h2>
<p>You are responsible for maintaining the security of your account. You must not share your account credentials. You must provide accurate information during registration.</p>
<h2 style="font-family:var(--font-display);color:var(--text);margin:1.5rem 0 1rem;">5. Intellectual Property</h2>
<p>All content on Global Path Africa is the property of Global Path Africa Ltd or licensed from third parties. Scholarship data is sourced from official providers (Erasmus+, DAAD, Chevening, Fulbright, etc.).</p>
<h2 style="font-family:var(--font-display);color:var(--text);margin:1.5rem 0 1rem;">6. Limitation of Liability</h2>
<p>Global Path Africa is not liable for any indirect or consequential damages. We do not guarantee that scholarships or jobs listed will result in successful applications.</p>
<h2 style="font-family:var(--font-display);color:var(--text);margin:1.5rem 0 1rem;">7. Contact</h2>
<p>For questions, contact us at <?= SITE_EMAIL ?> or WhatsApp <?= WHATSAPP_NUMBER ?>.</p>
</div>
</div></section>
<?php require_once 'includes/footer.php'; ?>
