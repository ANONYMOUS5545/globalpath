<?php
$pageTitle = 'Foreign Language Classes';
require_once 'includes/config.php';
startSecureSession();
require_once 'includes/header.php';

$languages = [
    ['name' => 'French', 'level' => 'Beginner to advanced', 'focus' => 'Study in France or Belgium, interviews, daily communication and academic writing.'],
    ['name' => 'German', 'level' => 'A1 to B2', 'focus' => 'University life, blocked-account interviews, relocation vocabulary and workplace basics.'],
    ['name' => 'English Exam Prep', 'level' => 'IELTS, TOEFL and interview readiness', 'focus' => 'Academic speaking, writing correction, listening drills and confidence coaching.'],
    ['name' => 'Dutch', 'level' => 'Starter to conversational', 'focus' => 'Useful for the Netherlands and Belgium, with practical study and settlement phrases.'],
    ['name' => 'Swedish', 'level' => 'Starter classes', 'focus' => 'Arrival phrases, student life vocabulary and early adaptation support.'],
];

$formats = [
    ['title' => 'One-on-One Online Tutoring', 'text' => 'Private live classes built around your study plan, embassy interview timeline or target exam.'],
    ['title' => 'Small Group Batches', 'text' => 'Affordable peer learning with weekly speaking sessions and guided homework.'],
    ['title' => 'Weekend Intensive Sessions', 'text' => 'Short focused blocks for applicants preparing for travel, interviews or language-test deadlines.'],
];
?>

<div class="page-header">
    <div class="container">
        <h1><i class="fas fa-language"></i> Online Foreign Language Tutoring</h1>
        <p>Live online classes for students and professionals preparing for scholarships, jobs abroad, visas and relocation</p>
    </div>
</div>

<section>
<div class="container">
    <div class="section-header reveal">
        <span class="section-badge">New Service</span>
        <h2 class="section-title">Language Classes That Match Your Global Plans</h2>
        <p class="section-subtitle">Build speaking confidence, improve academic communication and prepare for destination-country interviews from anywhere in Africa.</p>
    </div>

    <div style="display:grid;grid-template-columns:1.1fr 0.9fr;gap:1.5rem;align-items:stretch;margin-bottom:3rem;">
        <div style="background:white;border:1px solid var(--border);border-radius:var(--radius);padding:2rem;" class="reveal">
            <h3 style="font-family:var(--font-display);font-size:1.75rem;margin-bottom:0.75rem;">Why learners join</h3>
            <p style="color:var(--text-muted);line-height:1.8;margin-bottom:1rem;">These classes are designed for practical outcomes: stronger scholarship interviews, better visa confidence, clearer university communication and smoother first weeks after arrival.</p>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:0.85rem;">
                <div style="background:#f8fafc;border:1px solid var(--border);border-radius:var(--radius-sm);padding:1rem;"><i class="fas fa-check-circle" style="color:var(--primary);"></i> Speaking confidence for interviews</div>
                <div style="background:#f8fafc;border:1px solid var(--border);border-radius:var(--radius-sm);padding:1rem;"><i class="fas fa-check-circle" style="color:var(--primary);"></i> Academic writing support</div>
                <div style="background:#f8fafc;border:1px solid var(--border);border-radius:var(--radius-sm);padding:1rem;"><i class="fas fa-check-circle" style="color:var(--primary);"></i> Relocation and classroom vocabulary</div>
                <div style="background:#f8fafc;border:1px solid var(--border);border-radius:var(--radius-sm);padding:1rem;"><i class="fas fa-check-circle" style="color:var(--primary);"></i> Flexible online schedules</div>
            </div>
        </div>

        <div style="background:linear-gradient(135deg,var(--primary-dark),var(--primary));color:white;border-radius:var(--radius);padding:2rem;" class="reveal">
            <span style="display:inline-flex;align-items:center;gap:0.4rem;background:rgba(255,255,255,0.15);padding:0.35rem 0.8rem;border-radius:999px;font-size:0.82rem;">Live online</span>
            <h3 style="font-family:var(--font-display);font-size:1.65rem;margin:1rem 0 0.75rem;">Study, work and visa preparation in one learning path</h3>
            <p style="opacity:0.88;line-height:1.8;margin-bottom:1rem;">Tell us your target country, language level and deadline. We will point you to the right class format and help you prepare for real application situations, not just grammar exercises.</p>
            <div style="display:flex;gap:0.9rem;flex-wrap:wrap;">
                <a href="<?= WHATSAPP_LINK ?>?text=I+want+to+join+online+language+classes" target="_blank" class="btn" style="background:#25d366;color:white;"><i class="fab fa-whatsapp"></i> Join via WhatsApp</a>
                <a href="membership.php#visa-support" class="btn btn-outline-white">Pair with Visa Support</a>
            </div>
        </div>
    </div>

    <div class="grid-3" style="margin-bottom:3rem;">
        <?php foreach ($languages as $language): ?>
        <div class="card reveal">
            <div class="card-body" style="padding-top:1.75rem;">
                <div class="badge badge-blue" style="margin-bottom:0.8rem;"><?= htmlspecialchars($language['level']) ?></div>
                <h3><?= htmlspecialchars($language['name']) ?></h3>
                <p><?= htmlspecialchars($language['focus']) ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div style="background:white;border:1px solid var(--border);border-radius:var(--radius);padding:2rem;margin-bottom:3rem;" class="reveal">
        <h3 style="font-family:var(--font-display);font-size:1.7rem;margin-bottom:1.2rem;text-align:center;">Class Formats</h3>
        <div class="grid-3">
            <?php foreach ($formats as $format): ?>
            <div class="card" style="box-shadow:none;border:1px solid var(--border);">
                <div class="card-body">
                    <h3><?= htmlspecialchars($format['title']) ?></h3>
                    <p><?= htmlspecialchars($format['text']) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div style="background:linear-gradient(135deg,#f0faf5,white);border:2px solid var(--primary);border-radius:var(--radius);padding:2rem;text-align:center;" class="reveal">
        <h3 style="font-family:var(--font-display);margin-bottom:0.75rem;">Need a custom class plan?</h3>
        <p style="color:var(--text-muted);margin-bottom:1.25rem;">Message us with your target language, current level and country. We will recommend the right tutor format for your timeline.</p>
        <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
            <a href="<?= WHATSAPP_LINK ?>?text=I+need+a+custom+language+class+plan" target="_blank" class="btn" style="background:#25d366;color:white;"><i class="fab fa-whatsapp"></i> WhatsApp Enrolment</a>
            <a href="visas.php" class="btn btn-outline">See Visa Cost Guide</a>
        </div>
    </div>
</div>
</section>

<?php require_once 'includes/footer.php'; ?>
