<?php
require_once __DIR__ . '/config/db.php';
$pageTitle = 'Browse Cars';
$activeNav = 'cars';

$make = $_GET['make'] ?? '';
$bodyType = $_GET['body_type'] ?? '';
$priceMax = $_GET['price_max'] ?? '';
$priceMin = $_GET['price_min'] ?? '';
$q = trim($_GET['q'] ?? '');
$sort = $_GET['sort'] ?? 'newest';

$where = ["1=1"];
$params = [];

if ($make !== '') { $where[] = "c.make = ?"; $params[] = $make; }
if ($bodyType !== '') { $where[] = "c.body_type = ?"; $params[] = $bodyType; }
if ($priceMax !== '' && is_numeric($priceMax)) { $where[] = "c.price <= ?"; $params[] = $priceMax; }
if ($priceMin !== '' && is_numeric($priceMin)) { $where[] = "c.price >= ?"; $params[] = $priceMin; }
if ($q !== '') { $where[] = "(c.title LIKE ? OR c.make LIKE ? OR c.model LIKE ? OR c.description LIKE ?)"; $like = "%$q%"; array_push($params, $like, $like, $like, $like); }

$orderBy = "c.created_at DESC";
if ($sort === 'price_low') $orderBy = "c.price ASC";
if ($sort === 'price_high') $orderBy = "c.price DESC";
if ($sort === 'year_new') $orderBy = "c.year DESC";

$sql = "SELECT c.*, (SELECT image_path FROM car_images ci WHERE ci.car_id = c.id ORDER BY is_primary DESC, id ASC LIMIT 1) AS thumb
        FROM cars c WHERE " . implode(' AND ', $where) . " ORDER BY $orderBy";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$cars = $stmt->fetchAll();

$makes = $pdo->query("SELECT DISTINCT make FROM cars ORDER BY make")->fetchAll(PDO::FETCH_COLUMN);
$bodyTypes = $pdo->query("SELECT DISTINCT body_type FROM cars WHERE body_type IS NOT NULL ORDER BY body_type")->fetchAll(PDO::FETCH_COLUMN);

require_once __DIR__ . '/includes/site_header.php';
?>

<section class="section" style="padding-top:36px;">
    <div class="container">
        <div class="section-title" style="margin-bottom:26px;">
            <span class="eyebrow">Inventory</span>
            <h2>Browse Our Cars</h2>
        </div>

        <form class="filter-card" method="GET">
            <div class="filter-grid">
                <div>
                    <label>Make</label>
                    <select name="make">
                        <option value="">Any</option>
                        <?php foreach ($makes as $m): ?>
                            <option value="<?= htmlspecialchars($m) ?>" <?= $make === $m ? 'selected' : '' ?>><?= htmlspecialchars($m) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label>Body Type</label>
                    <select name="body_type">
                        <option value="">Any</option>
                        <?php foreach ($bodyTypes as $b): ?>
                            <option value="<?= htmlspecialchars($b) ?>" <?= $bodyType === $b ? 'selected' : '' ?>><?= htmlspecialchars($b) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label>Min Price</label>
                    <input type="number" name="price_min" value="<?= htmlspecialchars($priceMin) ?>" placeholder="KES">
                </div>
                <div>
                    <label>Max Price</label>
                    <input type="number" name="price_max" value="<?= htmlspecialchars($priceMax) ?>" placeholder="KES">
                </div>
                <div>
                    <label>Sort By</label>
                    <select name="sort">
                        <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest First</option>
                        <option value="price_low" <?= $sort === 'price_low' ? 'selected' : '' ?>>Price: Low to High</option>
                        <option value="price_high" <?= $sort === 'price_high' ? 'selected' : '' ?>>Price: High to Low</option>
                        <option value="year_new" <?= $sort === 'year_new' ? 'selected' : '' ?>>Year: Newest</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="btn btn-orange btn-block">Filter</button>
                </div>
            </div>
            <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Search by keyword (make, model, description)..." style="margin-top:14px;">
        </form>

        <p class="cell-muted" style="color:var(--text-muted); margin-bottom:20px;"><?= count($cars) ?> car(s) found</p>

        <div class="car-grid">
            <?php if (!$cars): ?>
                <div class="empty-state" style="grid-column:1/-1;">No cars match your search. Try adjusting the filters.</div>
            <?php endif; ?>
            <?php foreach ($cars as $car): ?>
                <?php include __DIR__ . '/includes/car_card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/site_footer.php'; ?>
