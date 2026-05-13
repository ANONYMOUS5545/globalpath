<?php
// ============================================================
// Global Path Africa - Configuration File
// ============================================================

define('SITE_NAME', 'Global Path Africa');
define('SITE_URL', 'http://localhost/globalpath'); // Change for production
define('SITE_EMAIL', 'info@globalpathAfrica.org');
define('WHATSAPP_NUMBER', '+254792579974');
define('WHATSAPP_LINK', 'https://wa.me/254792579974');

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'globalpath_africa');
define('DB_USER', 'root');       // Change in production
define('DB_PASS', '');           // Change in production
define('DB_CHARSET', 'utf8mb4');

// Membership Pricing (USD)
define('PRICE_PREMIUM_MONTHLY', 9.99);
define('PRICE_PREMIUM_ANNUAL', 89.99);
define('PRICE_PREMIUM_PLUS_MONTHLY', 19.99);
define('PRICE_PREMIUM_PLUS_ANNUAL', 179.99);
define('PRICE_SCHOLARSHIP_SUPPORT', 49.99);
define('PRICE_VISA_SUPPORT', 79.99);

// Payment Gateway Keys (replace with real keys)
define('STRIPE_PUBLISHABLE_KEY', 'pk_test_YOUR_STRIPE_KEY');
define('STRIPE_SECRET_KEY', 'sk_test_YOUR_STRIPE_SECRET');
define('PAYPAL_CLIENT_ID', 'YOUR_PAYPAL_CLIENT_ID');
define('PAYPAL_SECRET', 'YOUR_PAYPAL_SECRET');
define('FLUTTERWAVE_PUBLIC_KEY', 'FLWPUBK_TEST-YOUR_KEY');
define('FLUTTERWAVE_SECRET_KEY', 'FLWSECK_TEST-YOUR_SECRET');
define('MPESA_CONSUMER_KEY', 'YOUR_MPESA_CONSUMER_KEY');
define('MPESA_CONSUMER_SECRET', 'YOUR_MPESA_CONSUMER_SECRET');
define('MPESA_SHORTCODE', '174379');
define('MPESA_PASSKEY', 'YOUR_MPESA_PASSKEY');
define('MPESA_CALLBACK_URL', SITE_URL . '/api/mpesa_callback.php');

// AI Chatbot (Anthropic Claude API)
define('ANTHROPIC_API_KEY', 'YOUR_ANTHROPIC_API_KEY');

// IP Geolocation API
define('IPINFO_TOKEN', 'YOUR_IPINFO_TOKEN'); // https://ipinfo.io

// Security
define('JWT_SECRET', 'CHANGE_THIS_TO_RANDOM_64_CHAR_STRING');
define('SESSION_LIFETIME', 3600 * 24 * 7); // 7 days

// Email (SMTP)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your@gmail.com');
define('SMTP_PASS', 'your_app_password');

// Environment
define('DEBUG_MODE', true); // Set false in production

// ============================================================
// Database Connection (PDO)
// ============================================================
function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            if (DEBUG_MODE) {
                die("Database connection failed: " . $e->getMessage());
            } else {
                die("A database error occurred. Please try again later.");
            }
        }
    }
    return $pdo;
}

// ============================================================
// Session Management
// ============================================================
function startSecureSession() {
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
        ini_set('session.use_strict_mode', 1);
        session_start();
    }
}

// ============================================================
// Helper Functions
// ============================================================
function isLoggedIn() {
    startSecureSession();
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function isAdmin() {
    startSecureSession();
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

function getCurrentUser() {
    if (!isLoggedIn()) return null;
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ? AND status = 'active'");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

function formatCurrency($amount, $currency = 'USD') {
    if ($currency === 'KES') return 'KES ' . number_format($amount, 2);
    return '$' . number_format($amount, 2);
}

function getPlanCatalog() {
    static $plans = null;

    if ($plans === null) {
        $plans = [
            'premium_monthly' => [
                'name' => 'Premium Monthly',
                'amount_usd' => PRICE_PREMIUM_MONTHLY,
                'period' => '/month',
                'description' => 'Monthly Premium membership access',
                'mpesa_amount_kes' => 1350,
            ],
            'premium_annual' => [
                'name' => 'Premium Annual',
                'amount_usd' => PRICE_PREMIUM_ANNUAL,
                'period' => '/year',
                'description' => 'Annual Premium membership access',
                'mpesa_amount_kes' => 12150,
            ],
            'premium_plus_monthly' => [
                'name' => 'Premium Plus Monthly',
                'amount_usd' => PRICE_PREMIUM_PLUS_MONTHLY,
                'period' => '/month',
                'description' => 'Monthly Premium Plus membership access',
                'mpesa_amount_kes' => 2700,
            ],
            'premium_plus_annual' => [
                'name' => 'Premium Plus Annual',
                'amount_usd' => PRICE_PREMIUM_PLUS_ANNUAL,
                'period' => '/year',
                'description' => 'Annual Premium Plus membership access',
                'mpesa_amount_kes' => 24300,
            ],
            'scholarship_support' => [
                'name' => 'Scholarship Application Support',
                'amount_usd' => PRICE_SCHOLARSHIP_SUPPORT,
                'period' => ' one-time',
                'description' => 'One-time scholarship application support',
                'mpesa_amount_kes' => 6750,
            ],
            'visa_support' => [
                'name' => 'Visa Application Support',
                'amount_usd' => PRICE_VISA_SUPPORT,
                'period' => ' one-time',
                'description' => 'One-time visa application support',
                'mpesa_amount_kes' => 10800,
            ],
        ];
    }

    return $plans;
}

function getPlanDetails($plan) {
    $plans = getPlanCatalog();
    return $plans[$plan] ?? null;
}

function formatPlanName($plan) {
    $details = getPlanDetails($plan);
    if ($details) {
        return $details['name'];
    }

    return ucwords(str_replace('_', ' ', $plan));
}

function activatePurchasedPlan($db, $userId, $plan) {
    $type = '';
    $months = 1;

    if (strpos($plan, 'premium_plus') === 0) {
        $type = 'premium_plus';
    } elseif (strpos($plan, 'premium') === 0) {
        $type = 'premium';
    }

    if (strpos($plan, 'annual') !== false) {
        $months = 12;
    }

    if ($type) {
        $expires = date('Y-m-d H:i:s', strtotime("+{$months} months"));
        $db->prepare("UPDATE users SET membership_type=?,membership_expires=? WHERE id=?")
           ->execute([$type, $expires, $userId]);
        return;
    }

    if ($plan === 'scholarship_support') {
        $db->prepare("UPDATE users SET scholarship_access=1 WHERE id=?")->execute([$userId]);
    }
}

function timeAgo($datetime) {
    $now  = new DateTime();
    $ago  = new DateTime($datetime);
    $diff = $now->diff($ago);
    if ($diff->y > 0) return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
    if ($diff->m > 0) return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
    if ($diff->d > 0) return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
    if ($diff->h > 0) return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
    return 'Just now';
}

// Detect user country from IP
function detectCountry() {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    if ($ip === '127.0.0.1' || $ip === '::1') return 'Kenya'; // Default for localhost
    
    // Try ipinfo.io
    $token = IPINFO_TOKEN;
    $url = "https://ipinfo.io/{$ip}/json?token={$token}";
    $ctx = stream_context_create(['http' => ['timeout' => 3]]);
    $response = @file_get_contents($url, false, $ctx);
    if ($response) {
        $data = json_decode($response, true);
        if (isset($data['country'])) {
            // Map ISO code to country name
            $db = getDB();
            $stmt = $db->prepare("SELECT name FROM african_countries WHERE code = ?");
            $stmt->execute([$data['country']]);
            $country = $stmt->fetchColumn();
            return $country ?: $data['country'];
        }
    }
    return 'Nigeria'; // Fallback
}

// Check membership
function hasMembership($type = 'premium') {
    $user = getCurrentUser();
    if (!$user) return false;
    $validTypes = ($type === 'premium_plus') 
        ? ['premium_plus'] 
        : ['premium', 'premium_plus'];
    if (!in_array($user['membership_type'], $validTypes)) return false;
    if ($user['membership_expires'] && strtotime($user['membership_expires']) < time()) return false;
    return true;
}

function hasScholarshipAccess() {
    $user = getCurrentUser();
    return $user && $user['scholarship_access'];
}

// CSRF Protection
function generateCSRF() {
    startSecureSession();
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = generateToken();
    }
    return $_SESSION['csrf_token'];
}

function validateCSRF($token) {
    startSecureSession();
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>
