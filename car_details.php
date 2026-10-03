<?php
session_start();
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/site_config.php';

$id = $_GET['id'] ?? null;
$stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ?");
$stmt->execute([$id]);
$car = $stmt->fetch();

if (!$car) {
    header('Location: cars.php');
    exit;
}

$imagesStmt = $pdo->prepare("SELECT * FROM car_images WHERE car_id = ? ORDER BY is_primary DESC, id ASC");
$imagesStmt->execute([$id]);
$images = $imagesStmt->fetchAll();
if (!$images) {
    $images = [['image_path' => 'assets/uploads/cars/placeholder.jpg']];
}

$similarStmt = $pdo->prepare("
    SELECT c.*, (SELECT image_path FROM car_images ci WHERE ci.car_id = c.id ORDER BY is_primary DESC, id ASC LIMIT 1) AS thumb
    FROM cars c WHERE c.make = ? AND c.id != ? ORDER BY c.created_at DESC LIMIT 3
");
$similarStmt->execute([$car['make'], $car['id']]);
$similar = $similarStmt->fetchAll();

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$pageTitle = $car['title'];
$activeNav = 'cars';
require_once __DIR__ . '/includes/site_header.php';
?>

<section class="section" style="padding-top:32px;">
    <div class="container">
        <div class="breadcrumb" style="color:var(--text-muted); font-size:13px; margin-bottom:18px;">
            <a href="index.php" style="color:var(--text-muted);">Home</a> /
            <a href="cars.php" style="color:var(--text-muted);">Browse Cars</a> /
            <?= htmlspecialchars($car['title']) ?>
        </div>

        <?php if ($flash): ?>
            <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></div>
        <?php endif; ?>

        <div class="details-grid">
            <div>
                <div class="gallery-main">
                    <img id="galleryMain" src="<?= htmlspecialchars($images[0]['image_path']) ?>" alt="<?= htmlspecialchars($car['title']) ?>">
                </div>
                <?php if (count($images) > 1): ?>
                    <div class="gallery-thumbs">
                        <?php foreach ($images as $i => $img): ?>
                            <img src="<?= htmlspecialchars($img['image_path']) ?>" data-full="<?= htmlspecialchars($img['image_path']) ?>" class="<?= $i === 0 ? 'active' : '' ?>" alt="">
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="card" style="margin-top:26px; box-shadow:var(--shadow); border-radius:var(--radius); border:1px solid var(--border); background:#fff;">
                    <div style="padding:22px;">
                        <h2 style="margin:0 0 6px;"><?= htmlspecialchars($car['title']) ?></h2>
                        <div style="display:flex; gap:10px; margin-bottom:18px;">
                            <?php if ($car['status'] === 'available'): ?><span class="badge badge-success">Available</span>
                            <?php elseif ($car['status'] === 'reserved'): ?><span class="badge badge-warning">Reserved</span>
                            <?php else: ?><span class="badge badge-danger">Sold</span><?php endif; ?>
                            <?php if ($car['featured']): ?><span class="badge" style="background:#fff7ed; color:#ea580c;">★ Featured</span><?php endif; ?>
                        </div>

                        <h4 style="margin-bottom:6px;">Description</h4>
                        <p style="color:var(--text-muted); line-height:1.7; margin-bottom:22px;"><?= nl2br(htmlspecialchars($car['description'] ?: 'No description provided.')) ?></p>

                        <h4 style="margin-bottom:6px;">Specifications</h4>
                        <table class="spec-table">
                            <tr><td>Make</td><td><?= htmlspecialchars($car['make']) ?></td></tr>
                            <tr><td>Model</td><td><?= htmlspecialchars($car['model']) ?></td></tr>
                            <tr><td>Year</td><td><?= (int)$car['year'] ?></td></tr>
                            <tr><td>Mileage</td><td><?= $car['mileage'] ? number_format($car['mileage']) . ' km' : 'N/A' ?></td></tr>
                            <tr><td>Transmission</td><td><?= ucfirst($car['transmission']) ?></td></tr>
                            <tr><td>Fuel Type</td><td><?= ucfirst($car['fuel_type']) ?></td></tr>
                            <tr><td>Body Type</td><td><?= htmlspecialchars($car['body_type'] ?: 'N/A') ?></td></tr>
                            <tr><td>Color</td><td><?= htmlspecialchars($car['color'] ?: 'N/A') ?></td></tr>
                            <tr><td>Location</td><td><?= htmlspecialchars($car['location'] ?: 'N/A') ?></td></tr>
                        </table>
                    </div>
                </div>
            </div>

            <div>
                <div class="sticky-card">
                    <div class="price-big">KES <?= number_format($car['price'], 0) ?></div>
                    <div class="location">📍 <?= htmlspecialchars($car['location'] ?: 'Nairobi, Kenya') ?></div>

                    <div class="contact-actions">
                        <a href="https://wa.me/<?= htmlspecialchars(SITE_WHATSAPP) ?>?text=<?= urlencode('Hi, I am interested in the ' . $car['title'] . ' (KES ' . number_format($car['price'],0) . ') listed on your website.') ?>"
                           target="_blank" rel="noopener" class="btn btn-whatsapp btn-block">💬 Chat on WhatsApp</a>
                        <a href="tel:<?= htmlspecialchars(SITE_PHONE) ?>" class="btn btn-outline btn-block">📞 Call <?= htmlspecialchars(SITE_PHONE) ?></a>
                    </div>

                    <hr style="border:none; border-top:1px solid var(--border); margin:20px 0;">

                    <h4 style="margin:0 0 14px; font-size:15px;">Or send an inquiry</h4>
                    <form method="POST" action="inquiry_submit.php">
                        <input type="hidden" name="car_id" value="<?= $car['id'] ?>">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="name" required>
                        </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="tel" name="phone" required>
                        </div>
                        <div class="form-group">
                            <label>Email (optional)</label>
                            <input type="email" name="email">
                        </div>
                        <div class="form-group">
                            <label>Message</label>
                            <textarea name="message" placeholder="I'm interested in this car, is it still available?"></textarea>
                        </div>
                        <button type="submit" class="btn btn-orange btn-block">Send Inquiry</button>
                    </form>
                </div>
            </div>
        </div>

        <?php if ($similar): ?>
        <div style="margin-top:60px;">
            <div class="section-title" style="text-align:left;">
                <span class="eyebrow">You May Also Like</span>
                <h2>Similar <?= htmlspecialchars($car['make']) ?> Listings</h2>
            </div>
            <div class="car-grid">
                <?php foreach ($similar as $car): // reuse $car variable in partial ?>
                    <?php include __DIR__ . '/includes/car_card.php'; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/site_footer.php'; ?>
