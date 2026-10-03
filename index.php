<?php
require_once __DIR__ . '/config/db.php';
$pageTitle = 'Home';
$activeNav = 'home';
require_once __DIR__ . '/includes/site_header.php';

$makes = $pdo->query("SELECT DISTINCT make FROM cars ORDER BY make")->fetchAll(PDO::FETCH_COLUMN);
$bodyTypes = $pdo->query("SELECT DISTINCT body_type FROM cars WHERE body_type IS NOT NULL ORDER BY body_type")->fetchAll(PDO::FETCH_COLUMN);

$featured = $pdo->query("
    SELECT c.*, (SELECT image_path FROM car_images ci WHERE ci.car_id = c.id ORDER BY is_primary DESC, id ASC LIMIT 1) AS thumb
    FROM cars c
    WHERE c.featured = 1
    ORDER BY c.created_at DESC
    LIMIT 6
")->fetchAll();

$totalAvailable = $pdo->query("SELECT COUNT(*) FROM cars WHERE status = 'available'")->fetchColumn();
?>

<section class="hero">
    <div class="container">
        <h1>Find Your Next <span>Ride</span>, Hassle-Free</h1>
        <p>Browse <?= (int)$totalAvailable ?>+ quality vetted cars with transparent pricing. No hidden costs, no games — just honest deals.</p>

        <form class="search-card" method="GET" action="cars.php">
            <div>
                <label>Make</label>
                <select name="make">
                    <option value="">Any Make</option>
                    <?php foreach ($makes as $m): ?>
                        <option value="<?= htmlspecialchars($m) ?>"><?= htmlspecialchars($m) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label>Body Type</label>
                <select name="body_type">
                    <option value="">Any Type</option>
                    <?php foreach ($bodyTypes as $b): ?>
                        <option value="<?= htmlspecialchars($b) ?>"><?= htmlspecialchars($b) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label>Max Price (KES)</label>
                <input type="number" name="price_max" placeholder="e.g. 2000000">
            </div>
            <div>
                <label>&nbsp;</label>
                <input type="text" name="q" placeholder="Search keyword...">
            </div>
            <div>
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-orange btn-block">Search</button>
            </div>
        </form>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head-row">
            <div>
                <div class="section-title" style="text-align:left; margin-bottom:0;">
                    <span class="eyebrow">Handpicked</span>
                    <h2 style="margin-bottom:0;">Featured Listings</h2>
                </div>
            </div>
            <a href="cars.php" class="btn btn-outline">View All Cars</a>
        </div>

        <div class="car-grid">
            <?php if (!$featured): ?>
                <div class="empty-state" style="grid-column:1/-1;">No featured listings yet — check back soon.</div>
            <?php endif; ?>
            <?php foreach ($featured as $car): ?>
                <?php include __DIR__ . '/includes/car_card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section" style="background:#fff;">
    <div class="container">
        <div class="section-title">
            <span class="eyebrow">Why Choose Us</span>
            <h2>Buying a Car, Made Simple</h2>
            <p>We make car buying straightforward and stress-free from start to finish.</p>
        </div>
        <div class="features-grid">
            <div class="feature-box">
                <div class="icon-wrap"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4"></path><circle cx="12" cy="12" r="10"></circle></svg></div>
                <h4>Verified Listings</h4>
                <p>Every car is inspected and details confirmed before it's listed.</p>
            </div>
            <div class="feature-box">
                <div class="icon-wrap"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg></div>
                <h4>Fair, Transparent Prices</h4>
                <p>No hidden fees or last-minute price changes — what you see is what you pay.</p>
            </div>
            <div class="feature-box">
                <div class="icon-wrap"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg></div>
                <h4>Direct Communication</h4>
                <p>Chat or call us directly on WhatsApp — no waiting on hold.</p>
            </div>
            <div class="feature-box">
                <div class="icon-wrap"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg></div>
                <h4>Trusted by Buyers</h4>
                <p>Hundreds of satisfied customers across the country.</p>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/site_footer.php'; ?>
