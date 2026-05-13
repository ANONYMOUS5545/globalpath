<?php
$pageTitle = 'Visa Assistance';
require_once 'includes/config.php';
startSecureSession();
require_once 'includes/header.php';

$destinations = [
    ['country' => 'Germany', 'visa' => 'National student visa', 'summary' => 'Blocked-account planning and embassy checklist are key.'],
    ['country' => 'United Kingdom', 'visa' => 'Student visa', 'summary' => 'CAS, proof of funds and timely biometrics matter most.'],
    ['country' => 'United States', 'visa' => 'F-1 student visa', 'summary' => 'SEVIS record and interview preparation are central.'],
    ['country' => 'France', 'visa' => 'Long-stay student visa', 'summary' => 'Campus France steps and local country tariffs apply.'],
    ['country' => 'Canada', 'visa' => 'Study permit', 'summary' => 'LOA validation, PAL/TAL where required, and biometrics.'],
    ['country' => 'Netherlands', 'visa' => 'MVV and residence permit', 'summary' => 'Your institution often leads parts of the residence process.'],
    ['country' => 'Belgium', 'visa' => 'Type D student visa', 'summary' => 'Regional tuition rules and document ordering matter.'],
    ['country' => 'Sweden', 'visa' => 'Residence permit for studies', 'summary' => 'Admissions fee, tuition proof and maintenance funds are checked.'],
];

$studyRates = [
    [
        'country' => 'Germany',
        'visa_fee' => 'EUR 75 national visa',
        'undergraduate' => 'EUR 0 at most public universities; some states or programmes charge fees',
        'postgraduate' => 'EUR 0 at most public universities; Baden-Wuerttemberg commonly charges EUR 1,500 per semester for many non-EU students',
        'phd' => 'Usually tuition-free at public universities',
        'note' => 'DAAD guidance says public bachelor and most master programmes are generally tuition-free, with state and programme exceptions.',
        'link' => 'https://www2.daad.de/deutschland/studienangebote/international-programmes/en/detail/8310/',
        'label' => 'DAAD tuition example'
    ],
    [
        'country' => 'United Kingdom',
        'visa_fee' => 'GBP 558 student visa (from 8 Apr 2026)',
        'undergraduate' => 'GBP 11,400 to GBP 38,000 per year',
        'postgraduate' => 'GBP 9,000 to GBP 30,000 per year',
        'phd' => 'University-set; many research doctorates are funded',
        'note' => 'The UK publishes the visa fee centrally, but tuition remains university-set. Use the range as a planning baseline.',
        'link' => 'https://www.gov.uk/government/publications/visa-regulations-revised-table/home-office-immigration-and-nationality-fees-8-april-2026',
        'label' => 'GOV.UK visa fees'
    ],
    [
        'country' => 'United States',
        'visa_fee' => 'USD 185 visa application fee',
        'undergraduate' => 'USD 31,880 public out-of-state average or about USD 45,000 private nonprofit average',
        'postgraduate' => 'Institution-set; build from each university budget page',
        'phd' => 'Often funded through assistantships; tuition varies when unfunded',
        'note' => 'EducationUSA does not publish one national graduate tuition figure, so graduate and PhD budgets should be verified by institution.',
        'link' => 'https://educationusa.state.gov/your-5-steps-us-study/finance-your-studies',
        'label' => 'EducationUSA finance guide'
    ],
    [
        'country' => 'France',
        'visa_fee' => 'Country tariff page on France-Visas',
        'undergraduate' => 'EUR 2,895 per year in public institutions for many new non-EU entrants',
        'postgraduate' => 'EUR 3,941 per year in public institutions for many new non-EU entrants',
        'phd' => 'EUR 397 per year in public institutions',
        'note' => 'French public tuition is centrally published. Private schools can be much higher.',
        'link' => 'https://www.campusfrance.org/en/tuition-fees-France',
        'label' => 'Campus France'
    ],
    [
        'country' => 'Canada',
        'visa_fee' => 'CAD 150 study permit',
        'undergraduate' => 'CAD 41,746 average annual tuition for international students',
        'postgraduate' => 'CAD 24,028 average annual tuition for international students',
        'phd' => 'Doctoral fees vary by university; use graduate averages as a planning baseline and check funding',
        'note' => 'Statistics Canada publishes current weighted national averages; exact doctoral fees depend on institution and field.',
        'link' => 'https://www150.statcan.gc.ca/n1/daily-quotidien/250910/dq250910d-eng.htm',
        'label' => 'Statistics Canada'
    ],
    [
        'country' => 'Netherlands',
        'visa_fee' => 'EUR 254 study residence permit application',
        'undergraduate' => 'EUR 9,000 to EUR 20,000 per year for many non-EEA students',
        'postgraduate' => 'EUR 12,000 to EUR 30,000 per year for many non-EEA students',
        'phd' => 'Most PhD tracks are paid employment, not regular tuition study',
        'note' => 'Use institutional fee pages for exact programme cost because Dutch universities set their own non-EEA tuition.',
        'link' => 'https://ind.nl/en/fees-costs-of-an-application',
        'label' => 'IND fees'
    ],
    [
        'country' => 'Belgium',
        'visa_fee' => 'EUR 180 visa D fee; extra contribution fees may apply by case',
        'undergraduate' => 'About EUR 2,300 to EUR 9,500 per year for 60 ECTS in Flanders',
        'postgraduate' => 'About EUR 2,300 to EUR 9,500 per year for 60 ECTS in Flanders',
        'phd' => 'University-set; research doctorates are often funded or low-fee',
        'note' => 'Belgian tuition varies by region and institution, so the Flemish public-university guide is used as the clearest benchmark.',
        'link' => 'https://www.studyinflanders.be/practical-information/tuition-fees',
        'label' => 'Study in Flanders'
    ],
    [
        'country' => 'Sweden',
        'visa_fee' => 'SEK 1,500 residence permit application',
        'undergraduate' => 'About SEK 80,000 to SEK 295,000 per year',
        'postgraduate' => 'About SEK 80,000 to SEK 295,000 per year',
        'phd' => 'No tuition for doctoral studies; PhD positions are usually salaried',
        'note' => 'Swedish universities set tuition individually; the national planning range above is commonly used for non-EU students.',
        'link' => 'https://www.migrationsverket.se/en/you-want-to-apply/study/higher-education.html',
        'label' => 'Swedish Migration Agency'
    ],
];
?>

<div class="page-header">
    <div class="container">
        <h1><i class="fas fa-passport"></i> Visa Assistance & Study Cost Guide</h1>
        <p>Step-by-step visa guidance, paid application support and current study-cost snapshots for major destinations</p>
    </div>
</div>

<section>
<div class="container">
    <div class="section-header reveal">
        <span class="section-badge">Visa Help</span>
        <h2 class="section-title">Popular Study Visa Destinations</h2>
        <p class="section-subtitle">Use these country guides to plan your documents, timelines and budget before you book your embassy appointment.</p>
    </div>

    <div class="grid-4" style="margin-bottom:3rem;">
        <?php foreach ($destinations as $destination): ?>
        <div class="card reveal" style="text-align:center;padding:1.5rem;">
            <div style="width:72px;height:72px;border-radius:50%;margin:0 auto 0.85rem;background:linear-gradient(135deg,var(--primary),var(--primary-light));display:flex;align-items:center;justify-content:center;color:white;font-size:1.45rem;">
                <i class="fas fa-plane-departure"></i>
            </div>
            <h3 style="font-size:1rem;margin-bottom:0.35rem;"><?= htmlspecialchars($destination['country']) ?></h3>
            <div class="badge badge-blue" style="margin-bottom:0.55rem;"><?= htmlspecialchars($destination['visa']) ?></div>
            <p style="font-size:0.82rem;color:var(--text-muted);"><?= htmlspecialchars($destination['summary']) ?></p>
        </div>
        <?php endforeach; ?>
    </div>

    <div style="display:grid;grid-template-columns:1.1fr 0.9fr;gap:1.5rem;margin-bottom:3rem;align-items:stretch;">
        <div style="background:white;border-radius:var(--radius);padding:2rem;border:1px solid var(--border);" class="reveal">
            <span class="section-badge">Paid Support</span>
            <h3 style="font-family:var(--font-display);font-size:1.7rem;margin:0.9rem 0 0.7rem;">Visa Application Support</h3>
            <p style="color:var(--text-muted);line-height:1.8;margin-bottom:1rem;">Applicants can now pay for hands-on help with stronger visa files. We review document order, financial evidence, study plans, embassy forms and interview preparation so your application is cleaner and better organised.</p>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:0.75rem;margin-bottom:1.25rem;">
                <div style="background:#f8fafc;border:1px solid var(--border);border-radius:var(--radius-sm);padding:0.9rem;"><i class="fas fa-check-circle" style="color:var(--primary);"></i> Country-specific document checklist</div>
                <div style="background:#f8fafc;border:1px solid var(--border);border-radius:var(--radius-sm);padding:0.9rem;"><i class="fas fa-check-circle" style="color:var(--primary);"></i> Proof-of-funds review</div>
                <div style="background:#f8fafc;border:1px solid var(--border);border-radius:var(--radius-sm);padding:0.9rem;"><i class="fas fa-check-circle" style="color:var(--primary);"></i> SOP and explanation-letter guidance</div>
                <div style="background:#f8fafc;border:1px solid var(--border);border-radius:var(--radius-sm);padding:0.9rem;"><i class="fas fa-check-circle" style="color:var(--primary);"></i> Embassy interview coaching</div>
            </div>
            <div style="display:flex;gap:1rem;align-items:center;flex-wrap:wrap;">
                <span style="font-family:var(--font-display);font-size:2.2rem;color:var(--primary);">$<?= PRICE_VISA_SUPPORT ?></span>
                <span style="color:var(--text-muted);">one-time support fee</span>
            </div>
            <p style="font-size:0.82rem;color:var(--text-muted);margin:0.9rem 0 1.35rem;">This service improves preparation quality, but final visa decisions always remain with the embassy or immigration authority.</p>
            <div style="display:flex;gap:0.9rem;flex-wrap:wrap;">
                <a href="membership.php#visa-support" class="btn btn-primary">Pay for Visa Support</a>
                <a href="<?= WHATSAPP_LINK ?>?text=I+want+paid+visa+application+support" target="_blank" class="btn" style="background:#25d366;color:white;"><i class="fab fa-whatsapp"></i> Ask on WhatsApp</a>
            </div>
        </div>

        <div style="background:linear-gradient(135deg,var(--primary-dark),var(--primary));color:white;border-radius:var(--radius);padding:2rem;" class="reveal">
            <span style="display:inline-flex;align-items:center;gap:0.4rem;background:rgba(255,255,255,0.15);padding:0.35rem 0.8rem;border-radius:999px;font-size:0.82rem;">Current snapshot</span>
            <h3 style="font-family:var(--font-display);font-size:1.6rem;margin:1rem 0 0.75rem;">Updated Study Cost Planning</h3>
            <p style="opacity:0.88;line-height:1.8;margin-bottom:1rem;">The rates below were refreshed using the latest official country pages available as of <?= date('d M Y') ?>. Where a country does not publish one national tuition figure, the guide shows the current official range or planning rule.</p>
            <ul style="margin:0;padding-left:1.1rem;line-height:1.9;opacity:0.92;">
                <li>Bachelor, postgraduate and PhD columns are all included.</li>
                <li>Visa or residence application fees are listed where publicly published.</li>
                <li>Always confirm exact programme cost on the final university offer before paying.</li>
            </ul>
        </div>
    </div>

    <div style="background:white;border-radius:var(--radius);padding:2.5rem;border:1px solid var(--border);margin-bottom:3rem;" class="reveal">
        <h3 style="font-family:var(--font-display);font-size:1.75rem;margin-bottom:2rem;text-align:center;">General Visa Application Process</h3>
        <div class="steps-grid">
            <?php
            $steps = [
                ['1', 'Get Admission Letter', 'fas fa-university', 'Secure your university or employer offer before you start the visa process.'],
                ['2', 'Prepare Your Budget', 'fas fa-wallet', 'Match tuition, visa fee and living-cost proof to the destination country before submission.'],
                ['3', 'Gather Documents', 'fas fa-folder-open', 'Prepare passport, photos, financial evidence, insurance and academic records.'],
                ['4', 'Pay Fees & Book Biometrics', 'fas fa-credit-card', 'Pay the published visa or residence fee and secure the earliest appointment slot available.'],
                ['5', 'Attend Interview or Submission', 'fas fa-comments', 'Bring originals, answer clearly and keep your story consistent with your documents.'],
                ['6', 'Track Decision', 'fas fa-envelope-open-text', 'Processing times vary, so keep checking your application portal and email for updates.'],
            ];
            foreach ($steps as $step):
            ?>
            <div class="step-card reveal">
                <div class="step-number"><?= $step[0] ?></div>
                <div class="step-icon"><i class="<?= $step[2] ?>"></i></div>
                <h3><?= htmlspecialchars($step[1]) ?></h3>
                <p><?= htmlspecialchars($step[3]) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:2rem;margin-bottom:3rem;">
        <div style="background:white;border-radius:var(--radius);padding:2rem;border:1px solid var(--border);" class="reveal">
            <h4 style="font-family:var(--font-display);margin-bottom:1.25rem;color:var(--primary);">Student Visa Documents</h4>
            <?php foreach ([
                'Valid passport',
                'University acceptance or offer letter',
                'Proof of funds or sponsor support',
                'Academic transcripts and certificates',
                'Language test or waiver where required',
                'Passport photographs',
                'Travel or health insurance where required',
                'Completed visa or residence application form',
                'Visa fee payment receipt',
                'Scholarship or sponsor letter if applicable'
            ] as $item): ?>
            <div style="display:flex;align-items:center;gap:0.5rem;padding:0.4rem 0;border-bottom:1px solid var(--border);font-size:0.875rem;"><i class="fas fa-check-circle" style="color:var(--primary)"></i> <?= htmlspecialchars($item) ?></div>
            <?php endforeach; ?>
        </div>
        <div style="background:white;border-radius:var(--radius);padding:2rem;border:1px solid var(--border);" class="reveal">
            <h4 style="font-family:var(--font-display);margin-bottom:1.25rem;color:var(--primary);">Work Visa Documents</h4>
            <?php foreach ([
                'Valid passport with sufficient validity',
                'Job offer or employment contract',
                'Employer sponsorship documents',
                'CV and professional qualifications',
                'Police clearance where required',
                'Medical documents where required',
                'Accommodation details',
                'Bank statements and salary records',
                'Completed work visa application form',
                'Work permit approval where applicable'
            ] as $item): ?>
            <div style="display:flex;align-items:center;gap:0.5rem;padding:0.4rem 0;border-bottom:1px solid var(--border);font-size:0.875rem;"><i class="fas fa-check-circle" style="color:var(--primary)"></i> <?= htmlspecialchars($item) ?></div>
            <?php endforeach; ?>
        </div>
    </div>

    <div style="background:white;border-radius:var(--radius);padding:2rem;border:1px solid var(--border);margin-bottom:3rem;" class="reveal">
        <div style="display:flex;justify-content:space-between;align-items:flex-end;gap:1rem;flex-wrap:wrap;margin-bottom:1rem;">
            <div>
                <span class="section-badge">Current Rates</span>
                <h3 style="font-family:var(--font-display);font-size:1.7rem;margin:0.85rem 0 0.35rem;">Country Rates by Level of Study</h3>
                <p style="color:var(--text-muted);margin:0;">Current planning guide for the countries listed above, with bachelor, postgraduate and PhD views side by side.</p>
            </div>
            <div style="font-size:0.82rem;color:var(--text-muted);">Updated <?= date('d M Y') ?></div>
        </div>

        <div style="overflow-x:auto;">
            <table class="data-table" style="min-width:1080px;">
                <thead>
                    <tr>
                        <th>Country</th>
                        <th>Visa / Permit Fee</th>
                        <th>Bachelor</th>
                        <th>Postgraduate</th>
                        <th>PhD</th>
                        <th>Notes</th>
                        <th>Official Source</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($studyRates as $rate): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($rate['country']) ?></strong></td>
                        <td><?= htmlspecialchars($rate['visa_fee']) ?></td>
                        <td><?= htmlspecialchars($rate['undergraduate']) ?></td>
                        <td><?= htmlspecialchars($rate['postgraduate']) ?></td>
                        <td><?= htmlspecialchars($rate['phd']) ?></td>
                        <td style="min-width:240px;"><?= htmlspecialchars($rate['note']) ?></td>
                        <td><a href="<?= htmlspecialchars($rate['link']) ?>" target="_blank" rel="noopener"><?= htmlspecialchars($rate['label']) ?></a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <p style="font-size:0.82rem;color:var(--text-muted);margin:1rem 0 0;">Important: tuition figures are planning baselines. Final payable amounts can still change by programme, institution, exchange status, scholarships and local immigration updates.</p>
    </div>

    <div style="background:linear-gradient(135deg,var(--primary-dark),var(--primary));color:white;padding:2.5rem;border-radius:var(--radius);text-align:center;" class="reveal">
        <h3 style="font-family:var(--font-display);font-size:1.75rem;margin-bottom:0.75rem;">Need Help With Visas or Language Preparation?</h3>
        <p style="opacity:0.86;margin-bottom:1.5rem;">Combine visa support with our online foreign language tutoring so you can prepare documents, interviews and destination-language basics in one place.</p>
        <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
            <a href="membership.php#visa-support" class="btn btn-primary">Pay for Visa Support</a>
            <a href="language-classes.php" class="btn btn-outline-white">Explore Language Classes</a>
            <a href="<?= WHATSAPP_LINK ?>?text=I+need+visa+and+language+support" target="_blank" class="btn" style="background:#25d366;color:white;"><i class="fab fa-whatsapp"></i> WhatsApp Us</a>
        </div>
    </div>
</div>
</section>

<?php require_once 'includes/footer.php'; ?>
