// ============================================================
// Global Path Africa - Main JavaScript
// ============================================================

const siteUrl = document.body?.dataset.siteUrl || document.querySelector('meta[name="site-url"]')?.content || '';

function buildSiteUrl(path) {
    return siteUrl ? `${siteUrl}${path}` : path;
}

// ============================================================
// Navigation
// ============================================================
function toggleNav() {
    const nav = document.getElementById('navLinks');
    const toggle = document.getElementById('navToggle');
    nav.classList.toggle('open');
    toggle.classList.toggle('open');
}

function toggleUserMenu() {
    const dropdown = document.getElementById('userDropdown');
    if (dropdown) dropdown.classList.toggle('open');
}

// Close menus when clicking outside
document.addEventListener('click', function(e) {
    const userMenu = document.querySelector('.user-menu');
    const userDropdown = document.getElementById('userDropdown');
    if (userMenu && userDropdown && !userMenu.contains(e.target)) {
        userDropdown.classList.remove('open');
    }
});

// Sticky navbar shadow
window.addEventListener('scroll', function() {
    const navbar = document.getElementById('navbar');
    if (navbar) {
        navbar.style.boxShadow = window.scrollY > 10 
            ? '0 4px 30px rgba(0,0,0,0.15)' 
            : '0 2px 20px rgba(0,0,0,0.08)';
    }
});

// Mobile dropdown toggles
document.querySelectorAll('.has-dropdown > a').forEach(link => {
    link.addEventListener('click', function(e) {
        if (window.innerWidth <= 768) {
            e.preventDefault();
            this.parentElement.classList.toggle('open');
        }
    });
});

// ============================================================
// Country Selector
// ============================================================
function toggleCountrySelector() {
    const panel = document.getElementById('countrySelectorPanel');
    const overlay = document.getElementById('countryOverlay');
    const isOpen = panel.style.display !== 'none';
    panel.style.display = isOpen ? 'none' : 'block';
    overlay.style.display = isOpen ? 'none' : 'block';
}

function filterCountries() {
    const query = document.getElementById('countrySearch').value.toLowerCase();
    const options = document.querySelectorAll('.country-option');
    options.forEach(option => {
        const name = option.querySelector('span:last-child').textContent.toLowerCase();
        option.style.display = name.includes(query) ? '' : 'none';
    });
}

// ============================================================
// Toast Notifications
// ============================================================
function showToast(message, type = 'success', duration = 4000) {
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }
    
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.textContent = message;
    container.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'slideIn 0.3s ease reverse';
        setTimeout(() => toast.remove(), 300);
    }, duration);
}

// ============================================================
// Scroll Reveal Animations
// ============================================================
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('visible');
            observer.unobserve(entry.target);
        }
    });
}, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

// ============================================================
// Counter Animation
// ============================================================
function animateCounter(element, target, duration = 2000) {
    const start = 0;
    const increment = target / (duration / 16);
    let current = start;
    
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            current = target;
            clearInterval(timer);
        }
        element.textContent = Math.floor(current).toLocaleString() + (element.dataset.suffix || '');
    }, 16);
}

// Trigger counters when visible
const counterObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const target = parseInt(entry.target.dataset.count);
            animateCounter(entry.target, target);
            counterObserver.unobserve(entry.target);
        }
    });
}, { threshold: 0.5 });

document.querySelectorAll('[data-count]').forEach(el => counterObserver.observe(el));

// ============================================================
// Search / Filter
// ============================================================
function setupSearch(inputId, cardSelector) {
    const input = document.getElementById(inputId);
    if (!input) return;
    
    input.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        document.querySelectorAll(cardSelector).forEach(card => {
            const text = card.textContent.toLowerCase();
            card.style.display = text.includes(query) ? '' : 'none';
        });
    });
}

// ============================================================
// Form Validation
// ============================================================
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;
    
    let valid = true;
    form.querySelectorAll('[required]').forEach(field => {
        const error = field.parentElement.querySelector('.form-error');
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            if (error) error.style.display = 'flex';
            valid = false;
        } else {
            field.classList.remove('is-invalid');
            if (error) error.style.display = 'none';
        }
    });
    
    // Email validation
    form.querySelectorAll('input[type=email]').forEach(field => {
        if (field.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(field.value)) {
            field.classList.add('is-invalid');
            valid = false;
        }
    });
    
    return valid;
}

// ============================================================
// Payment Method Selection
// ============================================================
function selectPayment(method) {
    document.querySelectorAll('.payment-option').forEach(opt => {
        opt.classList.remove('selected');
    });
    const selected = document.querySelector(`[data-method="${method}"]`);
    if (selected) selected.classList.add('selected');
    
    // Show/hide relevant payment forms
    document.querySelectorAll('.payment-form').forEach(form => {
        form.style.display = 'none';
    });
    const targetForm = document.getElementById(`form-${method}`);
    if (targetForm) targetForm.style.display = 'block';
}

// ============================================================
// Modal
// ============================================================
function openModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
}

// Close modal on backdrop click
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal-backdrop')) {
        e.target.style.display = 'none';
        document.body.style.overflow = '';
    }
});

// ============================================================
// AJAX Helper
// ============================================================
async function apiRequest(url, data = {}) {
    const response = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    });
    return response.json();
}

// ============================================================
// Scholarship / Job Application
// ============================================================
async function applyNow(type, id, csrfToken) {
    if (document.body?.dataset.loggedIn !== '1') {
        window.location.href = `${buildSiteUrl('/login.php')}?redirect=${encodeURIComponent(window.location.href)}`;
        return;
    }
    
    const btn = document.querySelector(`[data-apply="${id}"]`);
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Applying...';
    }
    
    try {
        const result = await apiRequest(buildSiteUrl('/api/apply.php'), { type, id, csrf: csrfToken });
        if (result.success) {
            showToast('✅ Application submitted successfully!', 'success');
            if (btn) { btn.textContent = '✓ Applied'; btn.classList.add('btn-outline'); }
        } else {
            showToast('❌ ' + (result.message || 'Error submitting application'), 'error');
            if (btn) { btn.disabled = false; btn.textContent = 'Apply Now'; }
        }
    } catch (e) {
        showToast('❌ Network error. Please try again.', 'error');
        if (btn) { btn.disabled = false; btn.textContent = 'Apply Now'; }
    }
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    // Re-observe any dynamically added reveal elements
    document.querySelectorAll('.reveal:not(.visible)').forEach(el => observer.observe(el));
    
    // Auto-dismiss alerts
    document.querySelectorAll('.alert-auto-dismiss').forEach(alert => {
        setTimeout(() => alert.remove(), 5000);
    });
});
