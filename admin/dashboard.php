<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/../config/db.php';

$pageTitle = 'Dashboard';
$activePage = 'dashboard';

$totalCars = $pdo->query("SELECT COUNT(*) FROM cars")->fetchColumn();
$available = $pdo->query("SELECT COUNT(*) FROM cars WHERE status = 'available'")->fetchColumn();
$sold = $pdo->query("SELECT COUNT(*) FROM cars WHERE status = 'sold'")->fetchColumn();
$newInquiries = $pdo->query("SELECT COUNT(*) FROM inquiries WHERE status = 'new'")->fetchColumn();
$totalValue = $pdo->query("SELECT COALESCE(SUM(price),0) FROM cars WHERE status = 'available'")->fetchColumn();

$recentInquiries = $pdo->query("
    SELECT i.*, c.title AS car_title
    FROM inquiries i LEFT JOIN cars c ON i.car_id = c.id
    ORDER BY i.created_at DESC LIMIT 6
")->fetchAll();

$recentCars = $pdo->query("
    SELECT c.*, (SELECT image_path FROM car_images ci WHERE ci.car_id = c.id ORDER BY is_primary DESC, id ASC LIMIT 1) AS thumb
    FROM cars c ORDER BY c.created_at DESC LIMIT 5
")->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<div class="topbar">
    <div>
        <h1>Welcome back, <?= htmlspecialchars(explode(' ', $_SESSION['admin_name'] ?? 'Admin')[0]) ?> 👋</h1>
        <div class="breadcrumb">Here's an overview of your dealership today.</div>
    </div>
    <div class="page-actions">
        <a href="cars_add.php" class="btn btn-primary">+ Add New Car</a>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon orange">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 17h14M5 17a2 2 0 0 1-2-2v-2l2-5a2 2 0 0 1 2-1h6a2 2 0 0 1 2 1l2 5v2a2 2 0 0 1-2 2"></path></svg>
        </div>
        <div>
            <div class="stat-value"><?= (int)$totalCars ?></div>
            <div class="stat-label">Total listings</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4"></path><circle cx="12" cy="12" r="10"></circle></svg>
        </div>
        <div>
            <div class="stat-value"><?= (int)$available ?></div>
            <div class="stat-label">Available now</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
        </div>
        <div>
            <div class="stat-value">KES <?= number_format($totalValue, 0) ?></div>
            <div class="stat-label">Value of available stock</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
        </div>
        <div>
            <div class="stat-value"><?= (int)$newInquiries ?></div>
            <div class="stat-label">New inquiries</div>
        </div>
    </div>
</div>

<div class="dashboard-grid" style="display:grid; grid-template-columns:1.5fr 1fr; gap:20px;">
    <div class="card">
        <div class="card-header">
            <h3>Recent Listings</h3>
            <a href="cars_list.php" class="btn btn-outline btn-sm">View all</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th></th><th>Car</th><th>Price</th><th>Status</th></tr></thead>
                <tbody>
                <?php if (!$recentCars): ?>
                    <tr><td colspan="4" class="empty-state">No cars listed yet.</td></tr>
                <?php endif; ?>
                <?php foreach ($recentCars as $c): ?>
                    <tr>
                        <td class="thumb-cell"><img src="../<?= htmlspecialchars($c['thumb'] ?: 'assets/uploads/cars/placeholder.jpg') ?>" alt=""></td>
                        <td class="cell-strong"><?= htmlspecialchars($c['title']) ?></td>
                        <td>KES <?= number_format($c['price'], 0) ?></td>
                        <td>
                            <?php if ($c['status'] === 'available'): ?><span class="badge badge-success">Available</span>
                            <?php elseif ($c['status'] === 'reserved'): ?><span class="badge badge-warning">Reserved</span>
                            <?php else: ?><span class="badge badge-danger">Sold</span><?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>Recent Inquiries</h3>
            <a href="inquiries.php" class="btn btn-outline btn-sm">View all</a>
        </div>
        <div class="card-body" style="padding-top:6px;">
            <?php if (!$recentInquiries): ?>
                <div class="empty-state" style="padding:20px;">No inquiries yet.</div>
            <?php endif; ?>
            <?php foreach ($recentInquiries as $i): ?>
                <div class="alert <?= $i['status'] === 'new' ? 'alert-warning' : 'alert-success' ?>" style="margin-bottom:10px; display:block;">
                    <b><?= htmlspecialchars($i['name']) ?></b> — <?= htmlspecialchars($i['phone']) ?><br>
                    <span style="font-size:12.5px;">Re: <?= htmlspecialchars($i['car_title'] ?? 'General inquiry') ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
