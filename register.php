<?php
$pageTitle = 'Create Account';
require_once 'includes/config.php';
startSecureSession();

if (isLoggedIn()) redirect(SITE_URL . '/dashboard.php');

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRF($_POST['csrf'] ?? '')) {
        $error = 'Security error. Please refresh and try again.';
    } else {
        $firstName  = sanitize($_POST['first_name'] ?? '');
        $lastName   = sanitize($_POST['last_name'] ?? '');
        $email      = strtolower(trim($_POST['email'] ?? ''));
        $phone      = sanitize($_POST['phone'] ?? '');
        $country    = sanitize($_POST['country'] ?? '');
        $password   = $_POST['password'] ?? '';
        $confirm    = $_POST['confirm_password'] ?? '';
        $terms      = isset($_POST['terms']);
        
        if (!$firstName || !$lastName || !$email || !$password || !$country) {
            $error = 'Please fill in all required fields.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } elseif (strlen($password) < 8) {
            $error = 'Password must be at least 8 characters long.';
        } elseif ($password !== $confirm) {
            $error = 'Passwords do not match.';
        } elseif (!$terms) {
            $error = 'You must agree to the Terms of Service.';
        } else {
            $db = getDB();
            $existing = $db->prepare("SELECT id FROM users WHERE email = ?");
            $existing->execute([$email]);
            
            if ($existing->fetch()) {
                $error = 'An account with this email already exists. <a href="login.php">Sign in instead</a>.';
            } else {
                $hash = hashPassword($password);
                $ip = $_SERVER['REMOTE_ADDR'];
                $verifyToken = generateToken();
                
                $stmt = $db->prepare("INSERT INTO users (first_name, last_name, email, password_hash, phone, country, nationality, verification_token, ip_address) VALUES (?,?,?,?,?,?,?,?,?)");
                $stmt->execute([$firstName, $lastName, $email, $hash, $phone, $country, $country, $verifyToken, $ip]);
                $userId = $db->lastInsertId();
                
                // Auto-login
                $_SESSION['user_id'] = $userId;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_name'] = $firstName;
                $_SESSION['user_country'] = $country;
                
                redirect(SITE_URL . '/dashboard.php?welcome=1');
            }
        }
    }
}

require_once 'includes/header.php';
$db = getDB();
$countries = $db->query("SELECT name, region FROM african_countries ORDER BY name")->fetchAll();
?>

<div class="page-header" style="padding:3rem 0 2rem;">
    <div class="container text-center">
        <h1>Join Global Path Africa</h1>
        <p>Create your free account and start discovering opportunities today</p>
    </div>
</div>

<section style="padding:3rem 0 5rem;">
    <div class="container">
        <div class="form-section" style="max-width:580px;">
            <div style="text-align:center;margin-bottom:2rem;">
                <div style="font-size:3rem;">🌍</div>
                <h2 style="font-family:var(--font-display);margin-bottom:0.25rem;">Create Free Account</h2>
                <p style="color:var(--text-muted);font-size:0.9rem;">Already a member? <a href="login.php" style="color:var(--primary);font-weight:600;">Sign in here</a></p>
            </div>
            
            <?php if ($error): ?>
            <div style="background:#fee2e2;color:#991b1b;padding:0.9rem 1.25rem;border-radius:var(--radius-sm);margin-bottom:1.25rem;font-size:0.875rem;">
                <i class="fas fa-exclamation-circle"></i> <?= $error ?>
            </div>
            <?php endif; ?>
            
            <form method="POST">
                <input type="hidden" name="csrf" value="<?= generateCSRF() ?>">
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">First Name <span style="color:var(--accent)">*</span></label>
                        <input type="text" name="first_name" class="form-control" required 
                               placeholder="e.g. Amara" value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Last Name <span style="color:var(--accent)">*</span></label>
                        <input type="text" name="last_name" class="form-control" required 
                               placeholder="e.g. Kofi" value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email Address <span style="color:var(--accent)">*</span></label>
                    <input type="email" name="email" class="form-control" required 
                           placeholder="your@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Phone Number</label>
                    <input type="tel" name="phone" class="form-control" 
                           placeholder="+254 700 000 000" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Your African Country <span style="color:var(--accent)">*</span></label>
                    <select name="country" class="form-control" required>
                        <option value="">-- Select your country --</option>
                        <?php 
                        $currentRegion = '';
                        foreach ($countries as $c):
                            if ($c['region'] !== $currentRegion) {
                                if ($currentRegion) echo '</optgroup>';
                                echo '<optgroup label="' . htmlspecialchars($c['region']) . '">';
                                $currentRegion = $c['region'];
                            }
                        ?>
                        <option value="<?= htmlspecialchars($c['name']) ?>" <?= ($_POST['country'] ?? '') === $c['name'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['name']) ?>
                        </option>
                        <?php endforeach; ?>
                        </optgroup>
                    </select>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Password <span style="color:var(--accent)">*</span></label>
                        <input type="password" name="password" class="form-control" required 
                               placeholder="Min. 8 characters" id="password">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Confirm Password <span style="color:var(--accent)">*</span></label>
                        <input type="password" name="confirm_password" class="form-control" required 
                               placeholder="Repeat password" id="confirm_password">
                    </div>
                </div>
                
                <!-- Password strength -->
                <div id="passwordStrength" style="margin:-0.75rem 0 1rem;"></div>
                
                <div class="form-group">
                    <label class="form-check">
                        <input type="checkbox" name="terms" required>
                        <label>I agree to the <a href="terms.php" target="_blank" style="color:var(--primary)">Terms of Service</a> and <a href="privacy.php" target="_blank" style="color:var(--primary)">Privacy Policy</a></label>
                    </label>
                </div>
                
                <div class="form-group">
                    <label class="form-check">
                        <input type="checkbox" name="newsletter" value="1" checked>
                        <label>Send me new scholarship and job alerts</label>
                    </label>
                </div>
                
                <button type="submit" class="btn btn-green btn-block btn-lg">
                    <i class="fas fa-user-plus"></i> Create My Free Account
                </button>
            </form>
            
            <div style="margin-top:1.5rem;padding:1rem;background:#f0faf5;border-radius:var(--radius-sm);text-align:center;font-size:0.82rem;color:var(--text-muted);">
                <i class="fas fa-shield-alt" style="color:var(--primary)"></i> Your information is secure and never sold to third parties.
            </div>
        </div>
    </div>
</section>

<script>
// Password strength indicator
document.getElementById('password').addEventListener('input', function() {
    const val = this.value;
    const strength = document.getElementById('passwordStrength');
    let score = 0;
    if (val.length >= 8) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;
    
    const labels = ['','Weak','Fair','Good','Strong'];
    const colors = ['','#ef4444','#f59e0b','#3b82f6','#10b981'];
    
    if (val.length > 0) {
        strength.innerHTML = `
            <div style="display:flex;gap:4px;margin-bottom:4px;">
                ${[1,2,3,4].map(i => `<div style="flex:1;height:4px;border-radius:2px;background:${i<=score?colors[score]:'#e5e7eb'};transition:all 0.3s;"></div>`).join('')}
            </div>
            <span style="font-size:0.78rem;color:${colors[score]}">${labels[score]}</span>
        `;
    } else {
        strength.innerHTML = '';
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>
