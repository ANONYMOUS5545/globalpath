<?php
require_once __DIR__ . '/config.php';
startSecureSession();
$currentUser = getCurrentUser();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$csrf = generateCSRF();

// Auto-detect country
if (!isset($_SESSION['user_country'])) {
    if ($currentUser && $currentUser['country']) {
        $_SESSION['user_country'] = $currentUser['country'];
    } else {
        $_SESSION['user_country'] = detectCountry();
    }
}
$userCountry = $_SESSION['user_country'];

// Get all African countries for selector
$db = getDB();
$countriesStmt = $db->query("SELECT name, code, region FROM african_countries ORDER BY name");
$africanCountries = $countriesStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Global Path Africa - Your gateway to scholarships, jobs abroad, visa assistance and online foreign language classes for African students and professionals.">
    <meta name="keywords" content="Africa scholarships, study abroad Africa, Erasmus Africa, DAAD Africa, Chevening, jobs abroad Africa, visa support Africa, online language classes Africa">
    <meta name="site-url" content="<?= htmlspecialchars(SITE_URL, ENT_QUOTES, 'UTF-8') ?>">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ' : '' ?><?= SITE_NAME ?></title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500;600&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- Main CSS -->
    <link rel="stylesheet" href="<?= SITE_URL ?>/css/style.css">
    
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🌍</text></svg>">
</head>
<body class="<?= $currentPage ?>-page" data-site-url="<?= htmlspecialchars(SITE_URL, ENT_QUOTES, 'UTF-8') ?>" data-logged-in="<?= $currentUser ? '1' : '0' ?>">

<!-- Top Bar -->
<div class="top-bar">
    <div class="container">
        <div class="top-bar-left">
            <span><i class="fab fa-whatsapp"></i> <a href="<?= WHATSAPP_LINK ?>?text=Hello%20Global%20Path%20Africa" target="_blank">+254 792 579 974</a></span>
            <span><i class="fas fa-envelope"></i> <?= SITE_EMAIL ?></span>
        </div>
        <div class="top-bar-right">
            <div class="country-selector-mini">
                <i class="fas fa-globe-africa"></i>
                <span id="selectedCountryDisplay"><?= htmlspecialchars($userCountry) ?></span>
                <button onclick="toggleCountrySelector()" class="btn-link"><i class="fas fa-chevron-down"></i></button>
            </div>
            <?php if ($currentUser): ?>
                <a href="<?= SITE_URL ?>/dashboard.php" class="btn-topbar"><i class="fas fa-user"></i> My Dashboard</a>
            <?php else: ?>
                <a href="<?= SITE_URL ?>/login.php" class="btn-topbar">Login</a>
                <a href="<?= SITE_URL ?>/register.php" class="btn-topbar btn-topbar-accent">Join Free</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Country Selector Dropdown -->
<div id="countrySelectorPanel" class="country-selector-panel" style="display:none;">
    <div class="country-panel-inner">
        <div class="country-panel-header">
            <h3><i class="fas fa-map-marker-alt"></i> Select Your Country</h3>
            <button onclick="toggleCountrySelector()" class="btn-close-panel"><i class="fas fa-times"></i></button>
        </div>
        <p class="country-panel-sub">Your country helps us show relevant scholarships and opportunities</p>
        <input type="text" id="countrySearch" placeholder="Search country..." onkeyup="filterCountries()" class="country-search-input">
        <div class="country-grid" id="countryGrid">
            <?php foreach ($africanCountries as $c): ?>
            <label class="country-option <?= $c['name'] === $userCountry ? 'selected' : '' ?>">
                <input type="radio" name="userCountry" value="<?= htmlspecialchars($c['name']) ?>" 
                    <?= $c['name'] === $userCountry ? 'checked' : '' ?>
                    onchange="selectCountry('<?= htmlspecialchars($c['name']) ?>')">
                <span class="country-flag"><?= getFlagEmoji($c['code']) ?></span>
                <span><?= htmlspecialchars($c['name']) ?></span>
            </label>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<div id="countryOverlay" class="country-overlay" onclick="toggleCountrySelector()" style="display:none;"></div>

<!-- Navigation -->
<nav class="navbar" id="navbar">
    <div class="container">
        <a href="<?= SITE_URL ?>/index.php" class="logo">
            <div class="logo-icon">🌍</div>
            <div class="logo-text">
                <span class="logo-main">Global Path</span>
                <span class="logo-sub">Africa</span>
            </div>
        </a>
        
        <button class="nav-toggle" id="navToggle" onclick="toggleNav()">
            <span></span><span></span><span></span>
        </button>
        
        <ul class="nav-links" id="navLinks">
            <li><a href="<?= SITE_URL ?>/index.php" class="<?= $currentPage==='index'?'active':'' ?>">Home</a></li>
            <li class="has-dropdown">
                <a href="<?= SITE_URL ?>/scholarships.php" class="<?= $currentPage==='scholarships'?'active':'' ?>">Scholarships <i class="fas fa-chevron-down"></i></a>
                <ul class="dropdown">
                    <li><a href="<?= SITE_URL ?>/scholarships.php?level=postgraduate">Postgraduate</a></li>
                    <li><a href="<?= SITE_URL ?>/scholarships.php?level=undergraduate">Undergraduate</a></li>
                    <li><a href="<?= SITE_URL ?>/scholarships.php?level=phd">PhD Funding</a></li>
                    <li><a href="<?= SITE_URL ?>/scholarship-support.php">Application Support</a></li>
                </ul>
            </li>
            <li class="has-dropdown">
                <a href="<?= SITE_URL ?>/jobs.php" class="<?= ($currentPage==='jobs' || $currentPage==='job-resources')?'active':'' ?>">Jobs Abroad <i class="fas fa-chevron-down"></i></a>
                <ul class="dropdown">
                    <li><a href="<?= SITE_URL ?>/jobs.php?sector=development">International Development</a></li>
                    <li><a href="<?= SITE_URL ?>/jobs.php?sector=ngo">NGO & Non-Profit</a></li>
                    <li><a href="<?= SITE_URL ?>/jobs.php?sector=tech">Technology</a></li>
                    <li><a href="<?= SITE_URL ?>/jobs.php?sector=health">Healthcare</a></li>
                    <li><a href="<?= SITE_URL ?>/job-resources.php">Application Resources</a></li>
                </ul>
            </li>
            <li><a href="<?= SITE_URL ?>/blog.php" class="<?= $currentPage==='blog' || $currentPage==='blog-detail'?'active':'' ?>">Blog</a></li>
            <li><a href="<?= SITE_URL ?>/visas.php" class="<?= $currentPage==='visas'?'active':'' ?>">Visa Help</a></li>
            <li><a href="<?= SITE_URL ?>/language-classes.php" class="<?= $currentPage==='language-classes'?'active':'' ?>">Language Classes</a></li>
            <li><a href="<?= SITE_URL ?>/membership.php" class="<?= $currentPage==='membership'?'active':'' ?>">Membership</a></li>
            <li><a href="<?= SITE_URL ?>/about.php" class="<?= $currentPage==='about'?'active':'' ?>">About</a></li>
        </ul>
        
        <div class="nav-cta">
            <?php if ($currentUser): ?>
                <div class="user-menu">
                    <button class="user-avatar-btn" onclick="toggleUserMenu()">
                        <span class="avatar-circle"><?= strtoupper(substr($currentUser['first_name'],0,1)) ?></span>
                        <span><?= htmlspecialchars($currentUser['first_name']) ?></span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="user-dropdown" id="userDropdown">
                        <a href="<?= SITE_URL ?>/dashboard.php"><i class="fas fa-chart-pie"></i> Dashboard</a>
                        <a href="<?= SITE_URL ?>/profile.php"><i class="fas fa-user-edit"></i> Profile</a>
                        <a href="<?= SITE_URL ?>/applications.php"><i class="fas fa-file-alt"></i> Applications</a>
                        <a href="<?= SITE_URL ?>/payments.php"><i class="fas fa-credit-card"></i> Payments</a>
                        <a href="<?= SITE_URL ?>/logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="<?= SITE_URL ?>/membership.php" class="btn-premium">Get Premium <i class="fas fa-star"></i></a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<?php
function getFlagEmoji($countryCode) {
    $codePoints = array_map(fn($char) => 127397 + ord($char), str_split(strtoupper($countryCode)));
    return implode('', array_map('mb_chr', $codePoints));
}
?>
