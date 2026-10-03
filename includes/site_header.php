<?php
require_once __DIR__ . '/../config/site_config.php';
$pageTitle = $pageTitle ?? 'Home';
$activeNav = $activeNav ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle) ?> · <?= htmlspecialchars(SITE_NAME) ?></title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header class="site-header">
    <div class="container nav-inner">
        <a href="index.php" class="brand">
            <span class="icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 17h14M5 17a2 2 0 0 1-2-2v-2l2-5a2 2 0 0 1 2-1h6a2 2 0 0 1 2 1l2 5v2a2 2 0 0 1-2 2M5 17a2 2 0 0 0 2 2h0a2 2 0 0 0 2-2M17 17a2 2 0 0 0 2 2h0a2 2 0 0 0 2-2"></path><circle cx="7.5" cy="14.5" r=".6" fill="#fff"></circle><circle cx="16.5" cy="14.5" r=".6" fill="#fff"></circle></svg>
            </span>
            <?= htmlspecialchars(SITE_NAME) ?>
        </a>
        <nav class="nav-links">
            <a href="index.php" class="<?= $activeNav === 'home' ? 'active' : '' ?>">Home</a>
            <a href="cars.php" class="<?= $activeNav === 'cars' ? 'active' : '' ?>">Browse Cars</a>
            <a href="contact.php" class="<?= $activeNav === 'contact' ? 'active' : '' ?>">Contact</a>
            <a href="tel:<?= htmlspecialchars(SITE_PHONE) ?>" class="btn btn-outline-light btn-sm">📞 <?= htmlspecialchars(SITE_PHONE) ?></a>
        </nav>
        <button class="mobile-toggle" aria-label="Menu">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
        </button>
    </div>
</header>
