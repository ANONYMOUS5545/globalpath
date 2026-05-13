<?php
$pageTitle = 'Sign In';
require_once 'includes/config.php';
startSecureSession();

function resolveRedirectTarget($candidate) {
    $default = SITE_URL . '/dashboard.php';
    $candidate = trim((string)$candidate);

    if ($candidate === '') {
        return $default;
    }

    if (strpos($candidate, SITE_URL . '/') === 0) {
        return $candidate;
    }

    if (strpos($candidate, '/') === 0 && strpos($candidate, '//') !== 0) {
        return rtrim(SITE_URL, '/') . $candidate;
    }

    return $default;
}

$redirectTarget = resolveRedirectTarget($_GET['redirect'] ?? $_POST['redirect'] ?? '');
if (isLoggedIn()) {
    redirect($redirectTarget);
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRF($_POST['csrf'] ?? '')) {
        $error = 'Security error. Please refresh and try again.';
    } else {
        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';

        if (!$email || !$password) {
            $error = 'Enter your email address and password.';
        } else {
            $db = getDB();
            $stmt = $db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if (!$user || !verifyPassword($password, $user['password_hash'])) {
                $error = 'Invalid email or password.';
                sleep(1);
            } elseif ($user['status'] !== 'active') {
                $error = 'Your account is currently unavailable. Please contact support on WhatsApp.';
            } else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['first_name'];
                $_SESSION['user_country'] = $user['country'] ?: detectCountry();

                $db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?")->execute([$user['id']]);
                redirect($redirectTarget);
            }
        }
    }
}

require_once 'includes/header.php';
?>

<div class="page-header" style="padding:3rem 0 2rem;">
    <div class="container text-center">
        <h1>Welcome Back</h1>
        <p>Sign in to track applications, manage your plan, and get support.</p>
    </div>
</div>

<section style="padding:3rem 0 5rem;">
    <div class="container">
        <div class="form-section" style="max-width:560px;">
            <div style="text-align:center;margin-bottom:2rem;">
                <div style="font-size:3rem;">GPA</div>
                <h2 style="font-family:var(--font-display);margin-bottom:0.25rem;">Member Sign In</h2>
                <p style="color:var(--text-muted);font-size:0.9rem;">New here? <a href="register.php" style="color:var(--primary);font-weight:600;">Create your free account</a></p>
            </div>

            <?php if ($error): ?>
            <div style="background:#fee2e2;color:#991b1b;padding:0.9rem 1.25rem;border-radius:var(--radius-sm);margin-bottom:1.25rem;font-size:0.875rem;">
                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="csrf" value="<?= generateCSRF() ?>">
                <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirectTarget) ?>">

                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" required placeholder="your@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" autofocus>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required placeholder="Enter your password">
                </div>

                <button type="submit" class="btn btn-green btn-block btn-lg">
                    <i class="fas fa-sign-in-alt"></i> Sign In
                </button>
            </form>

            <div style="display:flex;justify-content:space-between;gap:1rem;flex-wrap:wrap;margin-top:1.5rem;font-size:0.9rem;">
                <a href="forgot-password.php" style="color:var(--primary);font-weight:600;">Forgot your password?</a>
                <a href="admin/login.php" style="color:var(--text-muted);">Admin sign in</a>
            </div>

            <div style="margin-top:1.5rem;padding:1rem 1.25rem;background:#f8fafc;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:0.9rem;color:var(--text-muted);">
                Need help with your account? <a href="<?= WHATSAPP_LINK ?>?text=Hello%20Global%20Path%20Africa%2C%20I%20need%20help%20signing%20in" target="_blank" style="color:#25d366;font-weight:600;"><i class="fab fa-whatsapp"></i> Chat on WhatsApp</a>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
