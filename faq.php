<?php
$pageTitle = 'FAQ';
require_once 'includes/config.php';
startSecureSession();
require_once 'includes/header.php';
$faqs = [
    'Scholarships' => [
        ['What scholarships does Global Path Africa list?','We list scholarships from Erasmus+, DAAD Germany, Chevening UK, Fulbright USA, World Bank, Commonwealth, Aga Khan Foundation, Gates Cambridge, and many more. All scholarships are sourced directly from official providers.'],
        ['Are the scholarships open to all African countries?','Yes. We accommodate all 54 African countries and filter opportunities based on your selected country when possible.'],
        ['How do I apply for a scholarship?','Browse scholarships, click "Learn More" for full details, then use the official link to apply. For premium members, our Scholarship Application Support service provides guided help with your SOP, documents, and form filling.'],
        ['What is Scholarship Application Support?','For a one-time fee of $49.99, our expert team helps you prepare a complete, polished application — including SOP writing, document review, reference letter guidance, and deadline management.'],
    ],
    'Membership & Payments' => [
        ['What are the membership plans?','Free (browse only), Premium ($9.99/month or $89.99/year — apply to jobs, priority support), Premium Plus ($19.99/month or $179.99/year — first access to new jobs, CV review, dedicated manager).'],
        ['What payment methods are accepted?','We accept Stripe (credit/debit card), PayPal, Flutterwave, M-Pesa (Kenya), and Bank Transfer. All payments are processed securely.'],
        ['Is my payment information stored?','We only store safe metadata: transaction ID, amount, status, date, and user ID. We never store credit card numbers or CVV codes.'],
        ['How do I upgrade or cancel my membership?','Contact us via WhatsApp (+254 792 579 974) or email to manage your subscription. For bank transfers, send proof of payment to receive activation within 24 hours.'],
    ],
    'Jobs Abroad' => [
        ['Who can apply for jobs on Global Path Africa?','Premium and Premium Plus members can apply for international jobs listed on our platform. Free members can browse but not apply.'],
        ['What is Premium Plus "First Access"?','Premium Plus members get access to newly posted jobs 48 hours before they are visible to Premium and Free members.'],
        ['What type of jobs are listed?','International development, healthcare, research, technology, NGO roles, and more — at organisations like the UN, World Bank, WHO, African Development Bank, and leading NGOs.'],
    ],
    'Account & Technical' => [
        ['How do I create an account?','Click "Join Free" at the top of any page. Registration takes under 2 minutes and is completely free.'],
        ['Can I change my country?','Yes. Click the country selector in the top bar to update your country at any time. Logged-in members can also update via their profile settings.'],
        ['How do I contact support?','WhatsApp: +254 792 579 974 | Email: info@globalpathAfrica.org | Use the PathBot AI chatbot on any page for instant answers.'],
    ],
];
?>
<div class="page-header"><div class="container text-center"><h1>Frequently Asked Questions</h1><p>Everything you need to know about Global Path Africa</p></div></div>
<section>
<div class="container" style="max-width:800px;">
    <?php foreach($faqs as $section=>$qs): ?>
    <h2 style="font-family:var(--font-display);font-size:1.5rem;margin:2.5rem 0 1rem;color:var(--primary);"><?= $section ?></h2>
    <?php foreach($qs as $q): ?>
    <div style="border:1px solid var(--border);border-radius:var(--radius-sm);margin-bottom:.75rem;overflow:hidden;">
        <details>
            <summary style="padding:1rem 1.25rem;font-weight:600;cursor:pointer;font-family:var(--font-ui);font-size:.92rem;list-style:none;display:flex;justify-content:space-between;align-items:center;">
                <?= $q[0] ?> <i class="fas fa-chevron-down" style="font-size:.75rem;color:var(--text-muted);flex-shrink:0;"></i>
            </summary>
            <div style="padding:.75rem 1.25rem 1.25rem;color:var(--text-muted);font-size:.875rem;border-top:1px solid var(--border);line-height:1.7;"><?= $q[1] ?></div>
        </details>
    </div>
    <?php endforeach; ?>
    <?php endforeach; ?>
    <div style="background:linear-gradient(135deg,var(--primary-dark),var(--primary));color:white;padding:2rem;border-radius:var(--radius);text-align:center;margin-top:3rem;">
        <h3 style="font-family:var(--font-display);margin-bottom:.75rem;">Still have questions?</h3>
        <p style="opacity:.8;margin-bottom:1.25rem;">Our team is here to help. Reach out via WhatsApp for the fastest response.</p>
        <a href="<?= WHATSAPP_LINK ?>?text=I+have+a+question+about+Global+Path+Africa" target="_blank" class="btn btn-primary btn-lg"><i class="fab fa-whatsapp"></i> WhatsApp Us</a>
    </div>
</div>
</section>
<?php require_once 'includes/footer.php'; ?>
