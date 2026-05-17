<?php
$pageTitle = 'Membership Plans';
require_once 'includes/config.php';
startSecureSession();
$user = getCurrentUser();
require_once 'includes/header.php';
?>

<div class="page-header">
    <div class="container text-center">
        <h1>Choose Your Membership Plan</h1>
        <p>Unlock scholarships, jobs, visa support and language tutoring tailored for African professionals</p>
    </div>
</div>

<section class="pricing-section">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-badge">Pricing</span>
            <h2 class="section-title">Simple, Transparent Pricing</h2>
            <p class="section-subtitle">All plans include full access to browse. Upgrade to apply and get support.</p>
        </div>

        <div style="text-align:center;margin-bottom:2.5rem;">
            <div style="display:inline-flex;background:#f3f4f6;border-radius:50px;padding:4px;gap:4px;">
                <button id="btnMonthly" onclick="setPeriod('monthly')" class="btn btn-sm" style="border-radius:50px;background:white;box-shadow:var(--shadow-sm);">Monthly</button>
                <button id="btnAnnual" onclick="setPeriod('annual')" class="btn btn-sm" style="border-radius:50px;background:none;border:none;">Annual <span style="background:var(--primary);color:white;padding:2px 8px;border-radius:10px;font-size:0.7rem;margin-left:4px;">Save 25%</span></button>
            </div>
        </div>

        <div class="pricing-grid" id="pricingGrid">
            <div class="pricing-card reveal">
                <div class="pricing-name">Free</div>
                <div class="pricing-price">
                    <span class="pricing-amount">$0</span>
                </div>
                <div class="pricing-period">Forever free</div>
                <ul class="pricing-features">
                    <li><i class="fas fa-check-circle"></i> Browse all scholarships</li>
                    <li><i class="fas fa-check-circle"></i> Browse all jobs</li>
                    <li><i class="fas fa-check-circle"></i> AI chatbot access</li>
                    <li><i class="fas fa-check-circle"></i> Country personalisation</li>
                    <li><i class="fas fa-check-circle"></i> Visa resource guides</li>
                    <li><i class="fas fa-check-circle"></i> Language class information</li>
                    <li class="disabled"><i class="fas fa-times-circle"></i> Apply to jobs</li>
                    <li class="disabled"><i class="fas fa-times-circle"></i> Scholarship help</li>
                    <li class="disabled"><i class="fas fa-times-circle"></i> Priority support</li>
                </ul>
                <?php if ($user): ?>
                    <?php if ($user['membership_type'] === 'free'): ?>
                    <span class="pricing-cta pricing-cta-outline" style="display:block;text-align:center;">Current Plan</span>
                    <?php else: ?>
                    <span class="pricing-cta pricing-cta-outline" style="display:block;text-align:center;cursor:default;">Included</span>
                    <?php endif; ?>
                <?php else: ?>
                <a href="register.php" class="pricing-cta pricing-cta-outline">Get Started Free</a>
                <?php endif; ?>
            </div>

            <div class="pricing-card featured reveal" id="premium">
                <div class="pricing-popular">Most Popular</div>
                <div class="pricing-name">Premium</div>
                <div class="pricing-price">
                    <span class="pricing-currency">$</span>
                    <span class="pricing-amount" id="premiumPrice"><?= PRICE_PREMIUM_MONTHLY ?></span>
                </div>
                <div class="pricing-period" id="premiumPeriod">per month</div>
                <ul class="pricing-features">
                    <li><i class="fas fa-check-circle"></i> Everything in Free</li>
                    <li><i class="fas fa-check-circle"></i> <strong>Apply to jobs abroad</strong></li>
                    <li><i class="fas fa-check-circle"></i> Priority application support</li>
                    <li><i class="fas fa-check-circle"></i> Application tracker</li>
                    <li><i class="fas fa-check-circle"></i> Email & chat support</li>
                    <li><i class="fas fa-check-circle"></i> Detailed visa guides</li>
                    <li><i class="fas fa-check-circle"></i> Language class recommendations</li>
                    <li><i class="fas fa-check-circle"></i> Scholarship alerts</li>
                    <li class="disabled"><i class="fas fa-times-circle"></i> First job access</li>
                </ul>
                <?php if ($user && $user['membership_type'] === 'premium'): ?>
                <span class="pricing-cta pricing-cta-green" style="display:block;text-align:center;">Your Plan</span>
                <?php else: ?>
                <button onclick="openPayment('premium')" class="pricing-cta pricing-cta-green">Get Premium</button>
                <?php endif; ?>
            </div>

            <div class="pricing-card premium-plus reveal" id="premium-plus">
                <div class="pricing-name" style="color:var(--gold);">Premium Plus</div>
                <div class="pricing-price">
                    <span class="pricing-currency">$</span>
                    <span class="pricing-amount" id="premiumPlusPrice"><?= PRICE_PREMIUM_PLUS_MONTHLY ?></span>
                </div>
                <div class="pricing-period" id="premiumPlusPeriod">per month</div>
                <ul class="pricing-features">
                    <li><i class="fas fa-check-circle" style="color:var(--gold)"></i> Everything in Premium</li>
                    <li><i class="fas fa-check-circle" style="color:var(--gold)"></i> <strong>First access to new jobs</strong></li>
                    <li><i class="fas fa-check-circle" style="color:var(--gold)"></i> Scholarship application help</li>
                    <li><i class="fas fa-check-circle" style="color:var(--gold)"></i> Paid visa application support option</li>
                    <li><i class="fas fa-check-circle" style="color:var(--gold)"></i> WhatsApp direct support</li>
                    <li><i class="fas fa-check-circle" style="color:var(--gold)"></i> CV & cover letter review</li>
                    <li><i class="fas fa-check-circle" style="color:var(--gold)"></i> Dedicated account manager</li>
                    <li><i class="fas fa-check-circle" style="color:var(--gold)"></i> Weekly job & scholarship alerts</li>
                    <li><i class="fas fa-check-circle" style="color:var(--gold)"></i> Interview preparation tips</li>
                </ul>
                <?php if ($user && $user['membership_type'] === 'premium_plus'): ?>
                <span class="pricing-cta pricing-cta-gold" style="display:block;text-align:center;">Your Plan</span>
                <?php else: ?>
                <button onclick="openPayment('premium_plus')" class="pricing-cta pricing-cta-gold">Get Premium Plus</button>
                <?php endif; ?>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:1.5rem;margin-top:3rem;">
            <div id="scholarship-support" style="padding:2rem;background:white;border:2px dashed var(--gold);border-radius:var(--radius-lg);text-align:center;" class="reveal">
                <div style="font-size:2.5rem;margin-bottom:0.75rem;"><i class="fas fa-book-open" style="color:var(--gold);"></i></div>
                <h3 style="font-family:var(--font-display);font-size:1.5rem;margin-bottom:0.75rem;">Scholarship Application Support</h3>
                <p style="color:var(--text-muted);margin-bottom:1rem;">One-time fee. Our expert team helps you with:</p>
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:0.75rem;margin-bottom:1.5rem;text-align:left;">
                    <div><i class="fas fa-check" style="color:var(--primary)"></i> Statement of Purpose writing</div>
                    <div><i class="fas fa-check" style="color:var(--primary)"></i> Document review & feedback</div>
                    <div><i class="fas fa-check" style="color:var(--primary)"></i> Reference letter guidance</div>
                    <div><i class="fas fa-check" style="color:var(--primary)"></i> Application form filling</div>
                    <div><i class="fas fa-check" style="color:var(--primary)"></i> Deadline management</div>
                    <div><i class="fas fa-check" style="color:var(--primary)"></i> WhatsApp direct support</div>
                </div>
                <div style="margin-bottom:1.25rem;">
                    <span style="font-family:var(--font-display);font-size:3rem;color:var(--primary);">$<?= PRICE_SCHOLARSHIP_SUPPORT ?></span>
                    <span style="color:var(--text-muted);"> one-time payment</span>
                </div>
                <p style="font-size:0.82rem;color:var(--text-muted);margin-bottom:1.25rem;">Payment via: Bank Transfer, M-Pesa, Card (Stripe/Flutterwave), or PayPal</p>
                <button onclick="openPayment('scholarship_support')" class="btn btn-primary btn-lg">
                    <i class="fas fa-hands-helping"></i> Get Scholarship Support
                </button>
            </div>

            <div id="visa-support" style="padding:2rem;background:white;border:2px dashed var(--primary);border-radius:var(--radius-lg);text-align:center;" class="reveal">
                <div style="font-size:2.5rem;margin-bottom:0.75rem;"><i class="fas fa-passport" style="color:var(--primary);"></i></div>
                <h3 style="font-family:var(--font-display);font-size:1.5rem;margin-bottom:0.75rem;">Visa Application Support</h3>
                <p style="color:var(--text-muted);margin-bottom:1rem;">One-time support focused on stronger, cleaner visa files and interview readiness.</p>
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:0.75rem;margin-bottom:1.5rem;text-align:left;">
                    <div><i class="fas fa-check" style="color:var(--primary)"></i> Country-specific checklist review</div>
                    <div><i class="fas fa-check" style="color:var(--primary)"></i> Financial proof and document ordering</div>
                    <div><i class="fas fa-check" style="color:var(--primary)"></i> SOP and cover letter guidance</div>
                    <div><i class="fas fa-check" style="color:var(--primary)"></i> Interview preparation and mock questions</div>
                    <div><i class="fas fa-check" style="color:var(--primary)"></i> Appointment and submission support</div>
                    <div><i class="fas fa-check" style="color:var(--primary)"></i> WhatsApp follow-up assistance</div>
                </div>
                <div style="margin-bottom:1.25rem;">
                    <span style="font-family:var(--font-display);font-size:3rem;color:var(--primary);">$<?= PRICE_VISA_SUPPORT ?></span>
                    <span style="color:var(--text-muted);"> one-time payment</span>
                </div>
                <p style="font-size:0.82rem;color:var(--text-muted);margin-bottom:1.25rem;">Best for students and professionals applying to Germany, the UK, the USA, France, Canada, the Netherlands, Belgium and Sweden.</p>
                <button onclick="openPayment('visa_support')" class="btn btn-green btn-lg">
                    <i class="fas fa-passport"></i> Get Visa Support
                </button>
            </div>
        </div>

        <div style="max-width:820px;margin:1.75rem auto 0;padding:1.5rem 1.75rem;background:#f8fafc;border:1px solid var(--border);border-radius:var(--radius-lg);" class="reveal">
            <div style="display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap;">
                <div>
                    <h3 style="font-family:var(--font-display);font-size:1.35rem;margin-bottom:0.35rem;">Online Foreign Language Tutoring</h3>
                    <p style="color:var(--text-muted);margin:0;">Join live online tutoring in French, German, English exam prep, Dutch and Swedish for study, work and visa confidence.</p>
                </div>
                <a href="language-classes.php" class="btn btn-outline">
                    <i class="fas fa-language"></i> View Language Classes
                </a>
            </div>
        </div>
    </div>
</section>

<section class="bg-light" style="padding:3rem 0;">
    <div class="container text-center">
        <h3 style="font-family:var(--font-ui);margin-bottom:1.5rem;">Accepted Payment Methods</h3>
        <div style="display:flex;justify-content:center;gap:2rem;flex-wrap:wrap;align-items:center;">
            <div style="text-align:center;">
                <i class="fab fa-stripe" style="font-size:2.5rem;color:#635bff;"></i>
                <div style="font-size:0.8rem;color:var(--text-muted);margin-top:0.3rem;">Stripe (Card)</div>
            </div>
            <div style="text-align:center;">
                <i class="fab fa-paypal" style="font-size:2.5rem;color:#003087;"></i>
                <div style="font-size:0.8rem;color:var(--text-muted);margin-top:0.3rem;">PayPal</div>
            </div>
            <div style="text-align:center;">
                <span style="display:inline-flex;align-items:center;justify-content:center;width:48px;height:48px;border-radius:50%;background:#f5a623;color:white;font-weight:700;">FW</span>
                <div style="font-size:0.8rem;color:var(--text-muted);margin-top:0.3rem;">Flutterwave</div>
            </div>
            <div style="text-align:center;">
                <span style="background:#4ade80;color:#065f46;padding:0.5rem 1rem;border-radius:var(--radius-sm);font-size:1rem;font-weight:700;">M-PESA</span>
                <div style="font-size:0.8rem;color:var(--text-muted);margin-top:0.3rem;">Kenya M-Pesa</div>
            </div>
            <div style="text-align:center;">
                <i class="fas fa-university" style="font-size:2rem;color:var(--primary)"></i>
                <div style="font-size:0.8rem;color:var(--text-muted);margin-top:0.3rem;">Bank Transfer</div>
            </div>
        </div>
        <p style="color:var(--text-muted);font-size:0.8rem;margin-top:1.25rem;">
            <i class="fas fa-shield-alt" style="color:var(--primary)"></i>
            Secure encrypted payments. We never store credit card or CVV data.
        </p>
    </div>
</section>

<div id="paymentModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:2000;overflow-y:auto;" class="modal-backdrop">
    <div style="max-width:520px;margin:4rem auto;background:white;border-radius:var(--radius-lg);padding:2rem;position:relative;">
        <button onclick="document.getElementById('paymentModal').style.display='none'" style="position:absolute;top:1rem;right:1rem;background:none;border:none;font-size:1.25rem;cursor:pointer;color:var(--text-muted);">x</button>

        <h3 style="font-family:var(--font-display);font-size:1.4rem;margin-bottom:0.25rem;">Complete Your Purchase</h3>
        <p id="paymentDesc" style="color:var(--text-muted);font-size:0.9rem;margin-bottom:1.5rem;"></p>

        <div style="background:#f0faf5;padding:1rem;border-radius:var(--radius-sm);margin-bottom:1.5rem;display:flex;justify-content:space-between;">
            <span id="paymentPlanName" style="font-weight:600;"></span>
            <span id="paymentAmount" style="font-family:var(--font-display);font-size:1.25rem;color:var(--primary);font-weight:700;"></span>
        </div>

        <?php if (!$user): ?>
        <div style="background:#fef3c7;border:1px solid #fbbf24;padding:1rem;border-radius:var(--radius-sm);margin-bottom:1.5rem;">
            <i class="fas fa-info-circle" style="color:#b45309"></i>
            <strong>Account Required</strong> - Please <a href="login.php" style="color:var(--primary)">sign in</a> or <a href="register.php" style="color:var(--primary)">create a free account</a> to complete payment.
        </div>
        <?php else: ?>

        <h4 style="font-family:var(--font-ui);font-size:0.85rem;text-transform:uppercase;letter-spacing:1px;color:var(--text-muted);margin-bottom:0.75rem;">Select Payment Method</h4>

        <div class="payment-methods">
            <div class="payment-option" data-method="stripe" onclick="selectPayment('stripe')">
                <i class="fab fa-stripe" style="color:#635bff;font-size:2rem;"></i>
                <span>Credit/Debit Card</span>
            </div>
            <div class="payment-option" data-method="paypal" onclick="selectPayment('paypal')">
                <i class="fab fa-paypal" style="color:#003087;font-size:2rem;"></i>
                <span>PayPal</span>
            </div>
            <div class="payment-option" data-method="flutterwave" onclick="selectPayment('flutterwave')">
                <span style="display:inline-flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:50%;background:#f5a623;color:white;font-weight:700;">FW</span>
                <span>Flutterwave</span>
            </div>
            <div class="payment-option" data-method="mpesa" onclick="selectPayment('mpesa')">
                <span style="background:#4ade80;color:#065f46;padding:2px 6px;border-radius:4px;font-weight:700;font-size:0.8rem;">M-PESA</span>
                <span>M-Pesa (KES)</span>
            </div>
            <div class="payment-option" data-method="bank_transfer" onclick="selectPayment('bank_transfer')">
                <i class="fas fa-university" style="color:var(--primary);font-size:2rem;"></i>
                <span>Bank Transfer</span>
            </div>
        </div>

        <div id="form-stripe" class="payment-form" style="display:none;">
            <div id="stripe-card-element" style="border:2px solid var(--border);padding:0.9rem 1rem;border-radius:var(--radius-sm);margin-bottom:1rem;"></div>
            <p style="font-size:0.78rem;color:var(--text-muted);margin-bottom:1rem;"><i class="fas fa-shield-alt" style="color:var(--primary)"></i> Your card is processed securely via Stripe. We never store card numbers.</p>
            <button onclick="processStripePayment()" class="btn btn-green btn-block btn-lg">Pay Now <span id="stripeAmount"></span></button>
        </div>

        <div id="form-paypal" class="payment-form" style="display:none;">
            <div id="paypal-button-container"></div>
        </div>

        <div id="form-flutterwave" class="payment-form" style="display:none;">
            <button onclick="processFlutterwave()" class="btn btn-block btn-lg" style="background:#f5a623;color:white;">Pay with Flutterwave</button>
        </div>

        <div id="form-mpesa" class="payment-form" style="display:none;">
            <div class="form-group">
                <label class="form-label">M-Pesa Phone Number (Safaricom Kenya)</label>
                <input type="tel" id="mpesaPhone" class="form-control" placeholder="e.g. 0712345678 or 254712345678" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                <div class="form-hint">You will receive an M-Pesa push notification to authorize payment.</div>
            </div>
            <div style="background:#f0faf5;padding:0.9rem;border-radius:var(--radius-sm);margin-bottom:1rem;font-size:0.82rem;">
                <strong>Amount:</strong> <span id="mpesaAmount"></span>
            </div>
            <button onclick="processMpesa()" class="btn btn-block btn-lg" style="background:#4ade80;color:#065f46;font-weight:700;">
                <i class="fas fa-mobile-alt"></i> Send M-Pesa Request
            </button>
        </div>

        <div id="form-bank_transfer" class="payment-form" style="display:none;">
            <div style="background:#f8fafc;padding:1.25rem;border-radius:var(--radius-sm);margin-bottom:1rem;font-size:0.875rem;line-height:1.8;">
                <strong>Bank Transfer Details:</strong><br>
                Bank Name: <strong>Equity Bank Kenya</strong><br>
                Account Name: <strong>Global Path Africa Ltd</strong><br>
                Account Number: <strong>1234567890</strong><br>
                SWIFT/BIC: <strong>EQBLKENA</strong><br>
                Reference: <strong>GPAf-<?= $user['id'] ?>-{PLAN}</strong>
            </div>
            <p style="font-size:0.8rem;color:var(--text-muted);margin-bottom:1rem;">After transfer, send proof to: <strong><?= SITE_EMAIL ?></strong> or WhatsApp: <a href="<?= WHATSAPP_LINK ?>" target="_blank">+254 792 579 974</a>. Activation within 24 hours on business days.</p>
            <button onclick="confirmBankTransfer()" class="btn btn-green btn-block">I've Made the Transfer</button>
        </div>

        <?php endif; ?>
    </div>
</div>

<script>
let selectedPlan = '';
let planData = {
    premium_monthly: { name: 'Premium Monthly', amount: <?= PRICE_PREMIUM_MONTHLY ?>, currency: 'USD', period: '/month', mpesaAmount: 1350 },
    premium_annual: { name: 'Premium Annual', amount: <?= PRICE_PREMIUM_ANNUAL ?>, currency: 'USD', period: '/year', mpesaAmount: 12150 },
    premium_plus_monthly: { name: 'Premium Plus Monthly', amount: <?= PRICE_PREMIUM_PLUS_MONTHLY ?>, currency: 'USD', period: '/month', mpesaAmount: 2700 },
    premium_plus_annual: { name: 'Premium Plus Annual', amount: <?= PRICE_PREMIUM_PLUS_ANNUAL ?>, currency: 'USD', period: '/year', mpesaAmount: 24300 },
    scholarship_support: { name: 'Scholarship Application Support', amount: <?= PRICE_SCHOLARSHIP_SUPPORT ?>, currency: 'USD', period: ' one-time', mpesaAmount: 6750 },
    visa_support: { name: 'Visa Application Support', amount: <?= PRICE_VISA_SUPPORT ?>, currency: 'USD', period: ' one-time', mpesaAmount: 10800 }
};
let currentPeriod = 'monthly';

function setPeriod(period) {
    currentPeriod = period;
    if (period === 'annual') {
        document.getElementById('premiumPrice').textContent = '<?= PRICE_PREMIUM_ANNUAL ?>';
        document.getElementById('premiumPeriod').textContent = 'per year (save 25%)';
        document.getElementById('premiumPlusPrice').textContent = '<?= PRICE_PREMIUM_PLUS_ANNUAL ?>';
        document.getElementById('premiumPlusPeriod').textContent = 'per year (save 25%)';
        document.getElementById('btnAnnual').style.background = 'white';
        document.getElementById('btnAnnual').style.boxShadow = 'var(--shadow-sm)';
        document.getElementById('btnMonthly').style.background = 'none';
        document.getElementById('btnMonthly').style.boxShadow = 'none';
    } else {
        document.getElementById('premiumPrice').textContent = '<?= PRICE_PREMIUM_MONTHLY ?>';
        document.getElementById('premiumPeriod').textContent = 'per month';
        document.getElementById('premiumPlusPrice').textContent = '<?= PRICE_PREMIUM_PLUS_MONTHLY ?>';
        document.getElementById('premiumPlusPeriod').textContent = 'per month';
        document.getElementById('btnMonthly').style.background = 'white';
        document.getElementById('btnMonthly').style.boxShadow = 'var(--shadow-sm)';
        document.getElementById('btnAnnual').style.background = 'none';
        document.getElementById('btnAnnual').style.boxShadow = 'none';
    }
}

function openPayment(plan) {
    if (plan === 'premium' || plan === 'premium_plus') {
        plan = currentPeriod === 'annual' ? plan + '_annual' : plan + '_monthly';
    }

    selectedPlan = plan;
    const data = planData[plan];
    if (!data) {
        showToast('This payment option is not available right now.', 'error');
        return;
    }

    document.getElementById('paymentPlanName').textContent = data.name;
    document.getElementById('paymentAmount').textContent = '$' + data.amount + data.period;
    document.getElementById('paymentDesc').textContent = 'You are purchasing: ' + data.name;
    document.getElementById('stripeAmount').textContent = '$' + data.amount;
    document.getElementById('mpesaAmount').textContent = data.mpesaAmount + ' KES';
    document.getElementById('paymentModal').style.display = 'flex';
}

function processMpesa() {
    const phone = document.getElementById('mpesaPhone').value.trim();
    if (!phone) { showToast('Please enter your M-Pesa phone number', 'error'); return; }

    fetch('<?= SITE_URL ?>/api/mpesa_initiate.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ phone, plan: selectedPlan, csrf: '<?= generateCSRF() ?>' })
    }).then(r => r.json()).then(d => {
        if (d.success) {
            showToast('M-Pesa push sent! Check your phone and enter your PIN.', 'success');
            document.getElementById('paymentModal').style.display = 'none';
        } else {
            showToast(d.message || 'M-Pesa error. Try again.', 'error');
        }
    });
}

function processFlutterwave() {
    FlutterwaveCheckout({
        public_key: "<?= FLUTTERWAVE_PUBLIC_KEY ?>",
        tx_ref: "GPAf-" + Date.now(),
        amount: planData[selectedPlan]?.amount,
        currency: "USD",
        payment_options: "card,banktransfer,ussd",
        customer: { email: "<?= $user ? htmlspecialchars($user['email']) : '' ?>", name: "<?= $user ? htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) : '' ?>" },
        customizations: { title: "Global Path Africa", description: planData[selectedPlan]?.name },
        callback: function(data) {
            fetch('<?= SITE_URL ?>/api/flutterwave_verify.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ tx_id: data.transaction_id, plan: selectedPlan, csrf: '<?= generateCSRF() ?>' })
            }).then(r => r.json()).then(d => {
                showToast(d.success ? 'Payment successful! Your purchase is now active.' : 'Verification failed.', d.success ? 'success' : 'error');
                if (d.success) setTimeout(() => location.reload(), 2000);
            });
        }
    });
}

function confirmBankTransfer() {
    fetch('<?= SITE_URL ?>/api/bank_pending.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ plan: selectedPlan, csrf: '<?= generateCSRF() ?>' })
    }).then(r => r.json()).then(d => {
        if (d.success) {
            showToast('Transfer noted! We will verify within 24 hours.', 'success');
            document.getElementById('paymentModal').style.display = 'none';
        } else {
            showToast(d.message || 'Could not save your bank transfer request.', 'error');
        }
    });
}
</script>

<script src="https://checkout.flutterwave.com/v3.js"></script>

<?php require_once 'includes/footer.php'; ?>
