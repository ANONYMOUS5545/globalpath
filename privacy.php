<?php $pageTitle='Privacy Policy'; require_once 'includes/config.php'; startSecureSession(); require_once 'includes/header.php'; ?>
<div class="page-header"><div class="container"><h1>Privacy Policy</h1><p>Last updated: <?= date('d F Y') ?></p></div></div>
<section><div class="container" style="max-width:800px;">
<div style="background:white;border-radius:var(--radius);padding:2.5rem;border:1px solid var(--border);line-height:1.8;color:var(--text-muted);">
<h2 style="font-family:var(--font-display);color:var(--text);margin-bottom:1rem;">1. Information We Collect</h2>
<p>We collect information you provide during registration (name, email, phone, country), payment metadata (transaction ID, amount, status — no raw card data), usage data, and country/IP information for personalisation.</p>
<h2 style="font-family:var(--font-display);color:var(--text);margin:1.5rem 0 1rem;">2. How We Use Your Information</h2>
<p>We use your information to: provide our services, personalise scholarship and job recommendations by country, process payments securely, send relevant updates and alerts (if subscribed), and improve our platform.</p>
<h2 style="font-family:var(--font-display);color:var(--text);margin:1.5rem 0 1rem;">3. Data Security</h2>
<p>We implement industry-standard security measures. Passwords are hashed using bcrypt. Payment processing is handled by certified third-party gateways. We never store credit card numbers, CVV codes, or banking credentials.</p>
<h2 style="font-family:var(--font-display);color:var(--text);margin:1.5rem 0 1rem;">4. Data Sharing</h2>
<p>We do not sell your personal data. We may share data with payment processors (Stripe, PayPal, Flutterwave, M-Pesa) to complete transactions. We may share anonymised, aggregate data for analytical purposes.</p>
<h2 style="font-family:var(--font-display);color:var(--text);margin:1.5rem 0 1rem;">5. Your Rights</h2>
<p>You may request access to, correction of, or deletion of your personal data at any time by contacting us at <?= SITE_EMAIL ?>.</p>
<h2 style="font-family:var(--font-display);color:var(--text);margin:1.5rem 0 1rem;">6. Cookies</h2>
<p>We use session cookies to maintain your login state and country preference. We do not use third-party tracking cookies.</p>
<h2 style="font-family:var(--font-display);color:var(--text);margin:1.5rem 0 1rem;">7. Contact</h2>
<p>For privacy concerns: <?= SITE_EMAIL ?> | WhatsApp: <?= WHATSAPP_NUMBER ?></p>
</div>
</div></section>
<?php require_once 'includes/footer.php'; ?>
