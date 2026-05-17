<?php if (!isset($hideWhatsApp) || !$hideWhatsApp): ?>
<!-- WhatsApp Floating Button -->
<a href="<?= WHATSAPP_LINK ?>?text=Hello%20Global%20Path%20Africa%2C%20I%20need%20help%20with..." 
   target="_blank" class="whatsapp-float" title="Chat with us on WhatsApp">
    <i class="fab fa-whatsapp"></i>
    <span class="whatsapp-pulse"></span>
</a>
<?php endif; ?>

<!-- AI Chatbot Widget -->
<div id="chatbotContainer" class="chatbot-container">
    <div id="chatbotWindow" class="chatbot-window" style="display:none;">
        <div class="chatbot-header">
            <div class="chatbot-avatar">🤖</div>
            <div class="chatbot-info">
                <strong>PathBot AI</strong>
                <span class="chatbot-status"><i class="fas fa-circle"></i> Online</span>
            </div>
            <button onclick="toggleChatbot()" class="chatbot-close"><i class="fas fa-times"></i></button>
        </div>
        <div class="chatbot-messages" id="chatbotMessages">
            <div class="bot-message">
                <div class="bot-bubble">
                    👋 Hi! I'm PathBot, your AI guide for scholarships, jobs abroad, and visas. How can I help you today?
                </div>
                <div class="quick-replies">
                    <button onclick="quickReply('Tell me about Erasmus+ scholarships')">Erasmus+ Scholarships</button>
                    <button onclick="quickReply('How do I apply for a job abroad?')">Jobs Abroad</button>
                    <button onclick="quickReply('What are the membership plans?')">Membership Plans</button>
                    <button onclick="quickReply('Help me with visa requirements')">Visa Help</button>
                    <button onclick="quickReply('Tell me about the online language classes')">Language Classes</button>
                </div>
            </div>
        </div>
        <div class="chatbot-input-area">
            <input type="text" id="chatbotInput" placeholder="Type your question..." 
                   onkeypress="if(event.key==='Enter') sendChatMessage()">
            <button onclick="sendChatMessage()" class="chatbot-send"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>
    <button class="chatbot-toggle" id="chatbotToggle" onclick="toggleChatbot()" title="Chat with PathBot AI">
        <i class="fas fa-comments" id="chatIcon"></i>
        <span class="chat-badge" id="chatBadge" style="display:none;">1</span>
    </button>
</div>

<!-- Footer -->
<footer class="footer">
    <div class="footer-wave">
        <svg viewBox="0 0 1440 80" preserveAspectRatio="none">
            <path d="M0,40 C360,80 1080,0 1440,40 L1440,80 L0,80 Z" fill="var(--footer-bg)"/>
        </svg>
    </div>
    <div class="footer-main">
        <div class="container">
            <div class="footer-grid">
                <!-- Brand -->
                <div class="footer-brand">
                    <div class="footer-logo">
                        <span class="footer-logo-icon">🌍</span>
                        <div>
                            <span class="footer-logo-main">Global Path</span>
                            <span class="footer-logo-sub">Africa</span>
                        </div>
                    </div>
                    <p>Empowering African students and professionals to access world-class education, career opportunities and practical preparation globally.</p>
                    <div class="social-links">
                        <a href="#" title="Facebook"><i class="fab fa-facebook"></i></a>
                        <a href="#" title="Twitter/X"><i class="fab fa-x-twitter"></i></a>
                        <a href="#" title="LinkedIn"><i class="fab fa-linkedin"></i></a>
                        <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="<?= WHATSAPP_LINK ?>" target="_blank" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                    </div>
                    <!-- WhatsApp Contact Box -->
                    <div class="whatsapp-contact-box">
                        <i class="fab fa-whatsapp"></i>
                        <div>
                            <strong>Direct WhatsApp Support</strong>
                            <a href="<?= WHATSAPP_LINK ?>?text=Hello%20Global%20Path%20Africa" target="_blank">+254 792 579 974</a>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div class="footer-col">
                    <h4>Opportunities</h4>
                    <ul>
                        <li><a href="<?= SITE_URL ?>/scholarships.php">All Scholarships</a></li>
                        <li><a href="<?= SITE_URL ?>/scholarships.php?provider=erasmus">Erasmus+</a></li>
                        <li><a href="<?= SITE_URL ?>/scholarships.php?provider=daad">DAAD Germany</a></li>
                        <li><a href="<?= SITE_URL ?>/scholarships.php?provider=chevening">Chevening UK</a></li>
                        <li><a href="<?= SITE_URL ?>/scholarships.php?provider=fulbright">Fulbright USA</a></li>
                        <li><a href="<?= SITE_URL ?>/jobs.php">Jobs Abroad</a></li>
                        <li><a href="<?= SITE_URL ?>/job-resources.php">Job Resources</a></li>
                        <li><a href="<?= SITE_URL ?>/blog.php">Blog</a></li>
                        <li><a href="<?= SITE_URL ?>/visas.php">Visa Assistance</a></li>
                        <li><a href="<?= SITE_URL ?>/language-classes.php">Language Classes</a></li>
                    </ul>
                </div>
                
                <!-- Membership -->
                <div class="footer-col">
                    <h4>Membership</h4>
                    <ul>
                        <li><a href="<?= SITE_URL ?>/membership.php">Free Plan</a></li>
                        <li><a href="<?= SITE_URL ?>/membership.php#premium">Premium Plan</a></li>
                        <li><a href="<?= SITE_URL ?>/membership.php#premium-plus">Premium Plus</a></li>
                        <li><a href="<?= SITE_URL ?>/scholarship-support.php">Scholarship Support</a></li>
                        <li><a href="<?= SITE_URL ?>/membership.php#visa-support">Visa Support</a></li>
                        <li><a href="<?= SITE_URL ?>/register.php">Create Account</a></li>
                        <li><a href="<?= SITE_URL ?>/login.php">Sign In</a></li>
                    </ul>
                </div>
                
                <!-- Contact -->
                <div class="footer-col">
                    <h4>Support</h4>
                    <ul>
                        <li><a href="<?= SITE_URL ?>/about.php">About Us</a></li>
                        <li><a href="<?= SITE_URL ?>/blog.php">Blog Articles</a></li>
                        <li><a href="<?= SITE_URL ?>/contact.php">Contact Us</a></li>
                        <li><a href="<?= SITE_URL ?>/faq.php">FAQ</a></li>
                        <li><a href="<?= SITE_URL ?>/privacy.php">Privacy Policy</a></li>
                        <li><a href="<?= SITE_URL ?>/terms.php">Terms of Service</a></li>
                    </ul>
                    <div class="footer-newsletter">
                        <p>Subscribe for new opportunities:</p>
                        <form id="footerNewsletter" class="newsletter-form">
                            <input type="email" name="email" placeholder="Your email" required>
                            <button type="submit"><i class="fas fa-arrow-right"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved. Scholarship data sourced from Erasmus+, DAAD, Chevening, Fulbright, World Bank, and other official bodies.</p>
            <div class="payment-badges">
                <span>Accepted Payments:</span>
                <i class="fab fa-stripe" title="Stripe"></i>
                <i class="fab fa-paypal" title="PayPal"></i>
                <i class="fab fa-cc-visa" title="Visa"></i>
                <i class="fab fa-cc-mastercard" title="Mastercard"></i>
                <span class="mpesa-badge">M-PESA</span>
            </div>
        </div>
    </div>
</footer>

<!-- Main JS -->
<script src="<?= SITE_URL ?>/js/main.js"></script>
<script src="<?= SITE_URL ?>/js/chatbot.js"></script>

<script>
// Newsletter subscription
document.getElementById('footerNewsletter').addEventListener('submit', function(e) {
    e.preventDefault();
    const email = this.querySelector('[name=email]').value;
    fetch('<?= SITE_URL ?>/api/subscribe.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({email, csrf: '<?= $csrf ?>'})
    }).then(r => r.json()).then(d => {
        showToast(d.success ? '✅ Subscribed successfully!' : '❌ ' + (d.message || 'Error'), d.success ? 'success' : 'error');
        if (d.success) this.reset();
    });
});

// Country selection via AJAX
function selectCountry(country) {
    fetch('<?= SITE_URL ?>/api/set_country.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({country, csrf: '<?= $csrf ?>'})
    }).then(r => r.json()).then(d => {
        document.getElementById('selectedCountryDisplay').textContent = country;
        toggleCountrySelector();
        showToast('✅ Country updated to ' + country);
    });
}
</script>
</body>
</html>
