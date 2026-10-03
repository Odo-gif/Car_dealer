<?php
// Expects $car (with 'thumb' key) to be set by the including page
$imgSrc = $car['thumb'] ?: 'assets/uploads/cars/placeholder.jpg';
?>
<div class="car-card">
    <div class="thumb">
        <span class="status-tag status-<?= htmlspecialchars($car['status']) ?>"><?= ucfirst($car['status']) ?></span>
        <?php if ($car['featured']): ?><span class="featured-tag">★ Featured</span><?php endif; ?>
        <img src="<?= htmlspecialchars($imgSrc) ?>" alt="<?= htmlspecialchars($car['title']) ?>" loading="lazy">
    </div>
    <div class="body">
        <div class="price">KES <?= number_format($car['price'], 0) ?></div>
        <h3><?= htmlspecialchars($car['title']) ?></h3>
        <div class="specs">
            <span>📅 <?= (int)$car['year'] ?></span>
            <span>🛣️ <?= $car['mileage'] ? number_format($car['mileage']) . ' km' : 'N/A' ?></span>
            <span>⚙️ <?= ucfirst($car['transmission']) ?></span>
        </div>
        <a href="car_details.php?id=<?= $car['id'] ?>" class="btn btn-orange btn-block">View Details</a>
    </div>
</div>
