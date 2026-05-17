<?php
$pageTitle = 'Scholarship Application Support';
require_once 'includes/config.php';
startSecureSession();
$user = getCurrentUser();
require_once 'includes/header.php';
?>
<div class="page-header">
    <div class="container text-center">
        <h1>📚 Scholarship Application Support</h1>
        <p>Expert-guided assistance to maximise your scholarship application success rate</p>
    </div>
</div>
<section>
<div class="container">
    <!-- Hero pricing -->
    <div style="max-width:700px;margin:0 auto 4rem;text-align:center;" class="reveal">
        <div style="background:white;border:3px solid var(--gold);border-radius:var(--radius-lg);padding:3rem 2rem;">
            <div style="font-size:3rem;margin-bottom:1rem;">🏆</div>
            <h2 style="font-family:var(--font-display);font-size:2rem;margin-bottom:.5rem;">One-Time Application Support</h2>
            <p style="color:var(--text-muted);margin-bottom:1.5rem;">Pay once. Get expert help for your full scholarship application cycle.</p>
            <div style="font-family:var(--font-display);font-size:4rem;color:var(--primary);font-weight:700;">$<?= PRICE_SCHOLARSHIP_SUPPORT ?></div>
            <p style="color:var(--text-muted);margin-bottom:2rem;">one-time payment — covers one full scholarship application</p>
            <?php if($user): ?>
            <button onclick="openSupportPayment()" class="btn btn-primary btn-lg"><i class="fas fa-hands-helping"></i> Get Started Now</button>
            <?php else: ?>
            <a href="register.php" class="btn btn-primary btn-lg"><i class="fas fa-user-plus"></i> Create Account to Apply</a>
            <?php endif; ?>
            <p style="font-size:.8rem;color:var(--text-muted);margin-top:1rem;">Secure payment via Stripe, PayPal, Flutterwave, M-Pesa or Bank Transfer</p>
        </div>
    </div>

    <!-- What's included -->
    <div class="section-header reveal"><span class="section-badge">What's Included</span><h2 class="section-title">Everything You Need to Succeed</h2></div>
    <div class="grid-3" style="margin-bottom:4rem;">
        <?php
        $services=[
            ['✍️','Statement of Purpose Writing','We help you craft a compelling, personalised SOP that highlights your strengths, goals, and fit for the scholarship.'],
            ['📄','Document Review & Feedback','Expert review of all your application documents with detailed feedback and improvement suggestions.'],
            ['📝','Application Form Filling','We guide you through every field of the application form, ensuring accuracy and completeness.'],
            ['🔤','Reference Letter Guidance','Templates and coaching for your referees to write strong, impactful recommendation letters.'],
            ['📅','Deadline Management','We track deadlines for you and send timely reminders so you never miss a submission window.'],
            ['💬','WhatsApp Direct Support','Dedicated WhatsApp support throughout the application process for quick answers and guidance.'],
        ];
        foreach($services as $s):
        ?>
        <div class="card reveal" style="padding:2rem;">
            <div style="font-size:2.5rem;margin-bottom:1rem;"><?= $s[0] ?></div>
            <h3 style="font-family:var(--font-ui);font-size:1rem;margin-bottom:.5rem;"><?= $s[1] ?></h3>
            <p style="font-size:.875rem;color:var(--text-muted);"><?= $s[2] ?></p>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Scholarships we support -->
    <div style="background:white;border-radius:var(--radius);padding:2.5rem;border:1px solid var(--border);margin-bottom:3rem;" class="reveal">
        <h3 style="font-family:var(--font-display);font-size:1.5rem;margin-bottom:1.5rem;text-align:center;">Scholarships We Help You Apply For</h3>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:1rem;">
            <?php foreach(['🇪🇺 Erasmus+','🇩🇪 DAAD Germany','🇬🇧 Chevening UK','🇺🇸 Fulbright USA','🌍 World Bank','🌐 Commonwealth','💎 Aga Khan Fund','🎓 Gates Cambridge','🇨🇭 Swiss Government','🇫🇷 Eiffel Scholarship','🇳🇱 Holland Scholarship','🇨🇦 Vanier Canada'] as $s): ?>
            <div style="background:#f0faf5;padding:.75rem 1rem;border-radius:var(--radius-sm);font-size:.875rem;font-weight:500;"><?= $s ?></div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Process -->
    <div class="steps-grid" style="margin-bottom:4rem;">
        <?php foreach([
            ['1','Submit Payment','Pay the one-time fee via your preferred method. Instant confirmation sent to your email.'],
            ['2','Initial Consultation','Our team contacts you within 24 hours to discuss your profile, goals, and target scholarship.'],
            ['3','Document Preparation','We work with you to prepare all required documents, SOP, and application materials.'],
            ['4','Review & Submit','Final review of everything before submission. We ensure completeness and quality.'],
        ] as $s): ?>
        <div class="step-card reveal">
            <div class="step-number"><?= $s[0] ?></div>
            <h3><?= $s[1] ?></h3>
            <p><?= $s[2] ?></p>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- FAQ -->
    <div style="max-width:750px;margin:0 auto;" class="reveal">
        <h3 style="font-family:var(--font-display);font-size:1.5rem;margin-bottom:1.5rem;text-align:center;">Frequently Asked Questions</h3>
        <?php
        $faqs=[
            ['How long does the support process take?','Typically 2–4 weeks depending on the scholarship deadline and your responsiveness. We recommend starting at least 2 months before the deadline.'],
            ['Does this guarantee I will get the scholarship?','No service can guarantee an outcome. However, our guided process significantly improves your application quality and competitiveness.'],
            ['Can I get support for multiple scholarships?','Each payment covers one full application. For multiple applications, a separate payment is required — though we offer discounts for returning clients.'],
            ['What if my application is rejected?','We offer a free review and improvement session for your next application if you are rejected while using our service.'],
            ['How do I get in touch during the process?','You will have a dedicated WhatsApp contact for direct communication with your assigned consultant throughout the process.'],
        ];
        foreach($faqs as $faq):
        ?>
        <div style="border:1px solid var(--border);border-radius:var(--radius-sm);margin-bottom:.75rem;overflow:hidden;">
            <details>
                <summary style="padding:1rem 1.25rem;font-weight:600;cursor:pointer;font-family:var(--font-ui);font-size:.9rem;list-style:none;display:flex;justify-content:space-between;align-items:center;">
                    <?= $faq[0] ?> <i class="fas fa-chevron-down" style="font-size:.75rem;color:var(--text-muted)"></i>
                </summary>
                <div style="padding:.75rem 1.25rem 1.25rem;color:var(--text-muted);font-size:.875rem;border-top:1px solid var(--border);"><?= $faq[1] ?></div>
            </details>
        </div>
        <?php endforeach; ?>
    </div>
</div>
</section>

<!-- Payment Modal (reuse membership modal logic) -->
<div id="supportModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:2000;overflow-y:auto;" class="modal-backdrop">
    <div style="max-width:480px;margin:4rem auto;background:white;border-radius:var(--radius-lg);padding:2rem;position:relative;">
        <button onclick="document.getElementById('supportModal').style.display='none'" style="position:absolute;top:1rem;right:1rem;background:none;border:none;font-size:1.25rem;cursor:pointer;color:var(--text-muted);">✕</button>
        <h3 style="font-family:var(--font-display);margin-bottom:.5rem;">Scholarship Support Payment</h3>
        <div style="background:#f0faf5;padding:1rem;border-radius:var(--radius-sm);margin-bottom:1.5rem;display:flex;justify-content:space-between;">
            <span>Scholarship Application Support</span>
            <strong style="color:var(--primary);">$<?= PRICE_SCHOLARSHIP_SUPPORT ?></strong>
        </div>
        <?php if($user): ?>
        <p style="color:var(--text-muted);font-size:.875rem;margin-bottom:1rem;">Contact us via WhatsApp to arrange payment and get started immediately:</p>
        <a href="<?= WHATSAPP_LINK ?>?text=I+want+to+pay+for+Scholarship+Application+Support+($<?= PRICE_SCHOLARSHIP_SUPPORT ?>)" target="_blank" class="btn btn-block btn-lg" style="background:#25d366;color:white;justify-content:center;margin-bottom:1rem;"><i class="fab fa-whatsapp"></i> Pay via WhatsApp</a>
        <p style="text-align:center;color:var(--text-muted);font-size:.8rem;">Or email: <a href="mailto:<?= SITE_EMAIL ?>"><?= SITE_EMAIL ?></a></p>
        <?php else: ?>
        <p>Please <a href="login.php" style="color:var(--primary)">sign in</a> or <a href="register.php" style="color:var(--primary)">create an account</a> first.</p>
        <?php endif; ?>
    </div>
</div>
<script>function openSupportPayment(){document.getElementById('supportModal').style.display='flex';}</script>
<?php require_once 'includes/footer.php'; ?>
