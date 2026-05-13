<?php
$pageTitle = 'Your Gateway to Global Education & Career Opportunities';
require_once 'includes/config.php';
require_once 'includes/opportunity_sync.php';
require_once 'includes/content_bootstrap.php';
startSecureSession();

// Fetch featured content
$currentUser = getCurrentUser();
$membershipType = $currentUser['membership_type'] ?? 'free';
$accessibleTiers = getAccessibleJobTiers($membershipType);
$db = getDB();
$scholarshipSync = bootOpportunitySync($db, 'scholarship');
$jobSync = bootOpportunitySync($db, 'job');
bootSiteContent($db);
$scholarships = $db->query("SELECT * FROM scholarships WHERE is_active=1 ORDER BY COALESCE(published_at, updated_at, created_at) DESC, id DESC LIMIT 3")->fetchAll();
$jobTierPlaceholders = implode(',', array_fill(0, count($accessibleTiers), '?'));
$jobStmt = $db->prepare("SELECT * FROM jobs WHERE is_active=1 AND access_tier IN ({$jobTierPlaceholders}) ORDER BY " . getJobOrderBySql() . " LIMIT 3");
$jobStmt->execute($accessibleTiers);
$jobs = $jobStmt->fetchAll();
$blogPosts = fetchRecentBlogPosts($db, 3);
$remoteBoards = array_slice(getRemoteJobBoards(), 0, 3);
$totalScholarships = $db->query("SELECT COUNT(*) FROM scholarships WHERE is_active=1")->fetchColumn();
$jobCountStmt = $db->prepare("SELECT COUNT(*) FROM jobs WHERE is_active=1 AND access_tier IN ({$jobTierPlaceholders})");
$jobCountStmt->execute($accessibleTiers);
$totalJobs = (int)$jobCountStmt->fetchColumn();
require_once 'includes/header.php';
?>

<!-- HERO -->
<section class="hero">
    <div class="hero-particles" id="particles"></div>
    <div class="container">
        <div class="hero-content">
            <div class="hero-badge">
                <i class="fas fa-globe-africa"></i>
                Empowering Africa's Future Leaders
            </div>
            <h1>Your <span class="highlight">Global Path</span> to Scholarships, Jobs, Visas & Languages</h1>
            <p>Access top European scholarships (Erasmus+, DAAD, Chevening), international jobs at the UN & World Bank, and expert visa guidance — all tailored for African students and professionals.</p>
            <div class="hero-actions">
                <a href="scholarships.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-graduation-cap"></i> Browse Scholarships
                </a>
                <a href="membership.php" class="btn btn-outline-white btn-lg">
                    <i class="fas fa-star"></i> Get Premium Access
                </a>
            </div>
            <div class="hero-stats">
                <div class="hero-stat">
                    <span class="number" data-count="<?= $totalScholarships ?>" data-suffix="+">0+</span>
                    <span class="label">Scholarships Listed</span>
                </div>
                <div class="hero-stat">
                    <span class="number" data-count="<?= $totalJobs ?>" data-suffix="+">0+</span>
                    <span class="label">Jobs Available</span>
                </div>
                <div class="hero-stat">
                    <span class="number" data-count="54" data-suffix="">0</span>
                    <span class="label">African Countries</span>
                </div>
                <div class="hero-stat">
                    <span class="number" data-count="2500" data-suffix="+">0+</span>
                    <span class="label">Members Helped</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SCHOLARSHIP SOURCES -->
<section class="sources-section">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-badge">Trusted Sources</span>
            <h2 class="section-title">Scholarships from World-Leading Institutions</h2>
            <p class="section-subtitle">We blend trusted scholarship sources with official hiring boards so visitors see fresher opportunities when they arrive.</p>
        </div>
        <div class="sources-grid reveal">
            <a href="scholarships.php?source=erasmus" class="source-item">
                <div class="source-icon">🇪🇺</div>
                <div class="source-name">Erasmus+</div>
                <div class="source-type">European Union</div>
            </a>
            <a href="scholarships.php?source=daad" class="source-item">
                <div class="source-icon">🇩🇪</div>
                <div class="source-name">DAAD</div>
                <div class="source-type">Germany</div>
            </a>
            <a href="scholarships.php?source=chevening" class="source-item">
                <div class="source-icon">🇬🇧</div>
                <div class="source-name">Chevening</div>
                <div class="source-type">United Kingdom</div>
            </a>
            <a href="scholarships.php?source=fulbright" class="source-item">
                <div class="source-icon">🇺🇸</div>
                <div class="source-name">Fulbright</div>
                <div class="source-type">United States</div>
            </a>
            <a href="scholarships.php?source=worldbank" class="source-item">
                <div class="source-icon">🌍</div>
                <div class="source-name">World Bank</div>
                <div class="source-type">Global</div>
            </a>
            <a href="scholarships.php?source=commonwealth" class="source-item">
                <div class="source-icon">🌐</div>
                <div class="source-name">Commonwealth</div>
                <div class="source-type">UK & Global</div>
            </a>
            <a href="jobs.php?source=un" class="source-item">
                <div class="source-icon">🕊️</div>
                <div class="source-name">UN Jobs</div>
                <div class="source-type">United Nations</div>
            </a>
            <a href="scholarships.php?source=aga-khan" class="source-item">
                <div class="source-icon">💎</div>
                <div class="source-name">Aga Khan</div>
                <div class="source-type">Foundation</div>
            </a>
        </div>
    </div>
</section>

<!-- FEATURED SCHOLARSHIPS -->
<section class="bg-light">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-badge">Scholarships</span>
            <h2 class="section-title">Latest Scholarship Opportunities</h2>
            <p class="section-subtitle">The newest active scholarship records appear first so visitors see the freshest openings immediately.</p>
        </div>
        <div class="grid-3">
            <?php foreach ($scholarships as $s): ?>
            <div class="card reveal">
                <div class="card-image" style="position:relative;">
                    <span style="font-size:3rem;"><?= getScholarshipEmoji($s['country']) ?></span>
                    <?php if ($s['is_featured']): ?>
                    <span class="badge badge-gold" style="position:absolute;top:1rem;right:1rem;"><i class="fas fa-star"></i> Featured</span>
                    <?php endif; ?>
                    <?php if (($s['listing_origin'] ?? 'manual') === 'imported'): ?>
                    <span class="badge badge-green" style="position:absolute;bottom:1rem;left:1rem;"><i class="fas fa-signal"></i> Live</span>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <div class="card-meta">
                        <span class="badge badge-green"><?= htmlspecialchars($s['type']) ?></span>
                        <span class="badge badge-blue"><?= htmlspecialchars($s['level']) ?></span>
                        <span class="badge badge-gray"><i class="fas fa-flag"></i> <?= htmlspecialchars($s['country']) ?></span>
                    </div>
                    <h3><a href="scholarship-detail.php?id=<?= $s['id'] ?>"><?= htmlspecialchars($s['title']) ?></a></h3>
                    <?php if (!empty($s['source_org'])): ?><p style="font-size:.8rem;font-weight:600;color:var(--primary);margin-bottom:.45rem;"><?= htmlspecialchars($s['source_org']) ?></p><?php endif; ?>
                    <p><?= htmlspecialchars(substr($s['description'], 0, 130)) ?>...</p>
                    <div class="card-footer">
                        <?php if ($s['deadline']): ?>
                        <span class="card-deadline"><i class="fas fa-clock"></i> <?= date('d M Y', strtotime($s['deadline'])) ?></span>
                        <?php elseif (!empty($s['published_at'])): ?>
                        <span class="card-deadline"><i class="fas fa-clock"></i> Updated <?= htmlspecialchars(timeAgo($s['published_at'])) ?></span>
                        <?php endif; ?>
                        <a href="scholarship-detail.php?id=<?= $s['id'] ?>" class="btn btn-green btn-sm">Learn More</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="scholarships.php" class="btn btn-outline btn-lg">
                View All Scholarships <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<!-- HOW IT WORKS -->
<section class="bg-white">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-badge">How It Works</span>
            <h2 class="section-title">Your Journey to Global Opportunities</h2>
            <p class="section-subtitle">Four simple steps to start your international education or career journey.</p>
        </div>
        <div class="steps-grid">
            <?php
            $steps = [
                ['1','Create Account','fas fa-user-plus','Sign up free and tell us about your academic background and career goals.'],
                ['2','Explore Opportunities','fas fa-search','Browse scholarships, international jobs, visa resources and language class options filtered for your goals.'],
                ['3','Upgrade for Support','fas fa-star','Get Premium or Premium Plus for priority job access, paid visa support and expert scholarship application assistance.'],
                ['4','Apply & Succeed','fas fa-trophy','Submit your applications with our guided support and track your progress on the dashboard.'],
            ];
            foreach ($steps as $i => $step):
            ?>
            <div class="step-card reveal" style="animation-delay:<?= $i * 0.1 ?>s">
                <div class="step-number"><?= $step[0] ?></div>
                <div class="step-icon"><i class="<?= $step[1] ?>"></i></div>
                <h3><?= $step[1] ?></h3>
                <p><?= $step[3] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- MEMBERSHIP PLANS -->
<section class="pricing-section">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-badge">Membership</span>
            <h2 class="section-title">Choose Your Plan</h2>
            <p class="section-subtitle">Start free, upgrade when you need premium support and priority access.</p>
        </div>
        <div class="pricing-grid">
            <!-- Free -->
            <div class="pricing-card reveal">
                <div class="pricing-name">Free</div>
                <div class="pricing-price">
                    <span class="pricing-amount">$0</span>
                </div>
                <div class="pricing-period">Forever free</div>
                <ul class="pricing-features">
                    <li><i class="fas fa-check-circle"></i> Browse all scholarships</li>
                    <li><i class="fas fa-check-circle"></i> Browse job listings</li>
                    <li><i class="fas fa-check-circle"></i> Country selection</li>
                    <li><i class="fas fa-check-circle"></i> AI chatbot support</li>
                    <li><i class="fas fa-check-circle"></i> Explore language classes</li>
                    <li class="disabled"><i class="fas fa-times-circle"></i> Job applications</li>
                    <li class="disabled"><i class="fas fa-times-circle"></i> Scholarship application help</li>
                    <li class="disabled"><i class="fas fa-times-circle"></i> Priority access</li>
                </ul>
                <a href="register.php" class="pricing-cta pricing-cta-outline">Get Started Free</a>
            </div>
            
            <!-- Premium -->
            <div class="pricing-card featured reveal">
                <div class="pricing-popular">Most Popular</div>
                <div class="pricing-name">Premium</div>
                <div class="pricing-price">
                    <span class="pricing-currency">$</span>
                    <span class="pricing-amount"><?= PRICE_PREMIUM_MONTHLY ?></span>
                </div>
                <div class="pricing-period">per month &mdash; or $<?= PRICE_PREMIUM_ANNUAL ?>/year</div>
                <ul class="pricing-features">
                    <li><i class="fas fa-check-circle"></i> Everything in Free</li>
                    <li><i class="fas fa-check-circle"></i> Apply to jobs abroad</li>
                    <li><i class="fas fa-check-circle"></i> Priority application support</li>
                    <li><i class="fas fa-check-circle"></i> Application tracker dashboard</li>
                    <li><i class="fas fa-check-circle"></i> Visa guides & resources</li>
                    <li><i class="fas fa-check-circle"></i> Email support</li>
                    <li><i class="fas fa-check-circle"></i> Language class recommendations</li>
                    <li class="disabled"><i class="fas fa-times-circle"></i> Scholarship application help</li>
                </ul>
                <a href="membership.php#premium" class="pricing-cta pricing-cta-green">Get Premium</a>
            </div>
            
            <!-- Premium Plus -->
            <div class="pricing-card premium-plus reveal">
                <div class="pricing-name" style="color:var(--gold);">Premium Plus</div>
                <div class="pricing-price">
                    <span class="pricing-currency">$</span>
                    <span class="pricing-amount"><?= PRICE_PREMIUM_PLUS_MONTHLY ?></span>
                </div>
                <div class="pricing-period">per month &mdash; or $<?= PRICE_PREMIUM_PLUS_ANNUAL ?>/year</div>
                <ul class="pricing-features">
                    <li><i class="fas fa-check-circle" style="color:var(--gold)"></i> Everything in Premium</li>
                    <li><i class="fas fa-check-circle" style="color:var(--gold)"></i> <strong>First access</strong> to new jobs</li>
                    <li><i class="fas fa-check-circle" style="color:var(--gold)"></i> Scholarship application assistance ($<?= PRICE_SCHOLARSHIP_SUPPORT ?> one-time add-on)</li>
                    <li><i class="fas fa-check-circle" style="color:var(--gold)"></i> Visa application support ($<?= PRICE_VISA_SUPPORT ?> one-time add-on)</li>
                    <li><i class="fas fa-check-circle" style="color:var(--gold)"></i> WhatsApp direct support</li>
                    <li><i class="fas fa-check-circle" style="color:var(--gold)"></i> CV & cover letter review</li>
                    <li><i class="fas fa-check-circle" style="color:var(--gold)"></i> Dedicated account manager</li>
                    <li><i class="fas fa-check-circle" style="color:var(--gold)"></i> Weekly opportunity alerts</li>
                </ul>
                <a href="membership.php#premium-plus" class="pricing-cta pricing-cta-gold">Get Premium Plus</a>
            </div>
        </div>
        
        <!-- Scholarship Support Add-on -->
        <div class="text-center mt-4 reveal">
            <div style="display:inline-block;background:white;border:2px dashed var(--gold);border-radius:var(--radius);padding:1.5rem 2.5rem;max-width:500px;">
                <div style="font-size:2rem;margin-bottom:0.5rem;">📚</div>
                <h3 style="font-family:var(--font-display);margin-bottom:0.5rem;">Scholarship Application Support</h3>
                <p style="color:var(--text-muted);font-size:0.9rem;margin-bottom:1rem;">One-time fee for guided, hands-on scholarship application assistance. SOP writing, document review, and submission support.</p>
                <span style="font-family:var(--font-display);font-size:2rem;color:var(--primary);">$<?= PRICE_SCHOLARSHIP_SUPPORT ?></span>
                <span style="color:var(--text-muted);"> one-time</span>
                <br><a href="scholarship-support.php" class="btn btn-primary mt-2">Learn More</a>
            </div>
        </div>
    </div>
</section>

<!-- FEATURED JOBS -->
<section class="bg-light">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-badge">Jobs Abroad</span>
            <h2 class="section-title">Latest Jobs from Official Hiring Platforms</h2>
            <p class="section-subtitle">Recent openings pulled directly from official employer career pages and verified company hiring platforms.</p>
        </div>
        <div class="grid-3">
            <?php foreach ($jobs as $j): ?>
            <div class="card reveal">
                <div class="card-body" style="padding-top:1.75rem;">
                    <?php if ($j['is_premium_only']): ?>
                    <div class="badge badge-premium mb-2"><i class="fas fa-star"></i> Premium Plus First Access</div>
                    <?php endif; ?>
                    <?php if (($j['listing_origin'] ?? 'manual') === 'imported'): ?>
                    <div class="badge badge-green mb-2"><i class="fas fa-signal"></i> Live Feed</div>
                    <?php endif; ?>
                    <div class="card-meta">
                        <span class="badge badge-blue"><?= htmlspecialchars($j['job_type']) ?></span>
                        <span class="badge badge-gray"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($j['country']) ?></span>
                    </div>
                    <h3><a href="job-detail.php?id=<?= $j['id'] ?>"><?= htmlspecialchars($j['title']) ?></a></h3>
                    <p style="font-weight:600;color:var(--primary);font-size:0.85rem;margin-bottom:0.5rem;"><?= htmlspecialchars($j['organization']) ?></p>
                    <?php if (!empty($j['source_org'])): ?><p style="font-size:.78rem;color:var(--text-muted);margin-bottom:.45rem;"><i class="fas fa-rss"></i> <?= htmlspecialchars($j['source_org']) ?></p><?php endif; ?>
                    <p><?= htmlspecialchars(substr($j['description'], 0, 120)) ?>...</p>
                    <div class="card-footer">
                        <?php if ($j['deadline']): ?>
                        <span class="card-deadline"><i class="fas fa-clock"></i> <?= date('d M Y', strtotime($j['deadline'])) ?></span>
                        <?php elseif (!empty($j['published_at'])): ?>
                        <span class="card-deadline"><i class="fas fa-clock"></i> Updated <?= htmlspecialchars(timeAgo($j['published_at'])) ?></span>
                        <?php endif; ?>
                        <a href="job-detail.php?id=<?= $j['id'] ?>" class="btn btn-green btn-sm">View Job</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="jobs.php" class="btn btn-outline btn-lg">View All Jobs <i class="fas fa-arrow-right"></i></a>
        </div>
    </div>
</section>

<!-- JOB APPLICATION RESOURCES -->
<section class="bg-white">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-badge">Apply Direct</span>
            <h2 class="section-title">Trusted Job Application Resources</h2>
            <p class="section-subtitle">Direct employer and regulator links for sea jobs, caregiver hiring and Middle East licensing steps, each marked as free or paid.</p>
        </div>
        <div class="resource-grid">
            <?php foreach ($featuredResources as $resource): $cost = jobResourceCostMeta($resource['application_cost_type']); ?>
            <article class="resource-card reveal">
                <div class="resource-card-top">
                    <div>
                        <div class="resource-kicker"><?= htmlspecialchars(resourceTypeLabel($resource['resource_type'])) ?></div>
                        <h3><?= htmlspecialchars($resource['title']) ?></h3>
                    </div>
                    <span class="cost-pill cost-pill-<?= htmlspecialchars($cost['class']) ?>"><?= htmlspecialchars($cost['label']) ?></span>
                </div>
                <div class="resource-meta-row">
                    <span><i class="fas fa-building"></i> <?= htmlspecialchars($resource['organization']) ?></span>
                    <span><i class="fas fa-layer-group"></i> <?= htmlspecialchars(resourceCategoryLabel($resource['category'])) ?></span>
                    <span><i class="fas fa-location-dot"></i> <?= htmlspecialchars($resource['country'] ?: $resource['region']) ?></span>
                </div>
                <p><?= htmlspecialchars($resource['summary']) ?></p>
                <div class="resource-note">
                    <strong>Cost note:</strong> <?= htmlspecialchars($resource['cost_notes']) ?>
                </div>
                <div class="resource-actions">
                    <a href="<?= htmlspecialchars($resource['apply_url']) ?>" target="_blank" class="btn btn-green btn-sm">Open Official Site</a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="job-resources.php" class="btn btn-outline btn-lg">Browse All Job Resources <i class="fas fa-arrow-right"></i></a>
        </div>
    </div>
</section>

<!-- BLOG -->
<section class="bg-light">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-badge">Blog</span>
            <h2 class="section-title">Fresh Guidance for Applying Abroad</h2>
            <p class="section-subtitle">Short, practical reads on direct applications, fee scams, sea jobs and caregiver hiring across trusted employers.</p>
        </div>
        <div class="blog-grid">
            <?php foreach ($blogPosts as $post): ?>
            <article class="blog-card reveal">
                <div class="blog-icon"><i class="<?= htmlspecialchars($post['cover_icon']) ?>"></i></div>
                <div class="blog-card-body">
                    <div class="blog-meta">
                        <span class="blog-chip"><?= htmlspecialchars(blogCategoryLabel($post['category'])) ?></span>
                        <span><i class="fas fa-clock"></i> <?= (int)$post['reading_time_minutes'] ?> min read</span>
                    </div>
                    <h3><a href="blog-detail.php?slug=<?= urlencode($post['slug']) ?>"><?= htmlspecialchars($post['title']) ?></a></h3>
                    <p><?= htmlspecialchars($post['excerpt']) ?></p>
                </div>
                <div class="blog-card-footer">
                    <span><?= date('d M Y', strtotime($post['published_at'] ?: $post['created_at'])) ?></span>
                    <a href="blog-detail.php?slug=<?= urlencode($post['slug']) ?>" class="btn btn-outline btn-sm">Read Article</a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="blog.php" class="btn btn-outline btn-lg">Visit the Blog <i class="fas fa-arrow-right"></i></a>
        </div>
    </div>
</section>

<!-- TESTIMONIALS -->
<section class="testimonials-section">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-badge">Success Stories</span>
            <h2 class="section-title">African Voices, Global Success</h2>
            <p class="section-subtitle">Real stories from members who secured scholarships and jobs through Global Path Africa.</p>
        </div>
        <div class="grid-3">
            <?php
            $testimonials = [
                ['A.','Amara K.','Ghana — Erasmus+ Scholar in Netherlands','The Premium Plus support made my Erasmus+ application seamless. The team reviewed my SOP three times and I got the scholarship! Highly recommend Global Path Africa.'],
                ['F.','Fatima N.','Nigeria — UN Programme Officer, Geneva','I found my UN job posting here 2 days before it went public. That Premium Plus first access was worth every cent. I\'m now in Geneva working my dream job.'],
                ['D.','David M.','Kenya — DAAD Scholar, Germany','I was confused about the DAAD application process. PathBot AI and the scholarship support team guided me step by step. Now I\'m doing my Masters in Berlin!'],
            ];
            foreach ($testimonials as $t):
            ?>
            <div class="testimonial-card reveal">
                <div class="star-rating">★★★★★</div>
                <p class="testimonial-text">"<?= $t[3] ?>"</p>
                <div class="testimonial-author">
                    <div class="author-avatar"><?= $t[0] ?></div>
                    <div>
                        <div class="author-name"><?= $t[1] ?></div>
                        <div class="author-detail"><?= $t[2] ?></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA SECTION -->
<section style="background:linear-gradient(135deg,var(--primary-dark),var(--primary));text-align:center;padding:5rem 0;">
    <div class="container reveal">
        <div class="hero-badge" style="margin:0 auto 1.5rem;">
            <i class="fab fa-whatsapp"></i> Get Instant Help
        </div>
        <h2 style="font-family:var(--font-display);color:white;font-size:2.5rem;margin-bottom:1rem;">Ready to Start Your Global Journey?</h2>
        <p style="color:rgba(255,255,255,0.75);font-size:1.1rem;max-width:550px;margin:0 auto 2rem;">Join thousands of African students and professionals who have used Global Path Africa to access world-class opportunities.</p>
        <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
            <a href="register.php" class="btn btn-primary btn-lg">Create Free Account</a>
            <a href="<?= WHATSAPP_LINK ?>?text=Hello%20Global%20Path%20Africa" target="_blank" class="btn btn-outline-white btn-lg">
                <i class="fab fa-whatsapp"></i> WhatsApp Us Now
            </a>
        </div>
    </div>
</section>

<?php
function getScholarshipEmoji($country) {
    $map = ['Germany'=>'🇩🇪','United Kingdom'=>'🇬🇧','United States'=>'🇺🇸','France'=>'🇫🇷','Netherlands'=>'🇳🇱','European Union'=>'🇪🇺'];
    return $map[$country] ?? '🎓';
}
require_once 'includes/footer.php';
?>
