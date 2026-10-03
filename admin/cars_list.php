<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/../config/db.php';

$pageTitle = 'Manage Cars';
$activePage = 'cars';

$statusFilter = $_GET['status'] ?? '';

$sql = "SELECT c.*, (SELECT image_path FROM car_images ci WHERE ci.car_id = c.id ORDER BY is_primary DESC, id ASC LIMIT 1) AS thumb
        FROM cars c";
$params = [];
if ($statusFilter !== '') {
    $sql .= " WHERE c.status = ?";
    $params[] = $statusFilter;
}
$sql .= " ORDER BY c.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$cars = $stmt->fetchAll();

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

include __DIR__ . '/includes/header.php';
?>

<div class="topbar">
    <div>
        <h1>Manage Cars</h1>
        <div class="breadcrumb">All listings in your inventory</div>
    </div>
    <div class="page-actions">
        <a href="cars_add.php" class="btn btn-primary">+ Add New Car</a>
    </div>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body" style="padding-bottom:0;">
        <div class="filter-bar">
            <div class="search-box">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                <input type="search" id="liveSearch" placeholder="Search by title or make...">
            </div>
            <form method="GET">
                <select name="status" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="available" <?= $statusFilter === 'available' ? 'selected' : '' ?>>Available</option>
                    <option value="reserved" <?= $statusFilter === 'reserved' ? 'selected' : '' ?>>Reserved</option>
                    <option value="sold" <?= $statusFilter === 'sold' ? 'selected' : '' ?>>Sold</option>
                </select>
            </form>
        </div>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr><th></th><th>Title</th><th>Year</th><th>Price</th><th>Status</th><th>Featured</th><th>Actions</th></tr>
            </thead>
            <tbody>
            <?php if (!$cars): ?>
                <tr><td colspan="7" class="empty-state">No cars found. <a href="cars_add.php">Add your first listing</a>.</td></tr>
            <?php endif; ?>
            <?php foreach ($cars as $c): ?>
                <tr data-search="<?= htmlspecialchars(strtolower($c['title'] . ' ' . $c['make'])) ?>">
                    <td class="thumb-cell"><img src="../<?= htmlspecialchars($c['thumb'] ?: 'assets/uploads/cars/placeholder.jpg') ?>" alt=""></td>
                    <td>
                        <div class="cell-strong"><?= htmlspecialchars($c['title']) ?></div>
                        <div class="cell-muted"><?= htmlspecialchars($c['make']) ?> · <?= htmlspecialchars($c['model']) ?></div>
                    </td>
                    <td><?= (int)$c['year'] ?></td>
                    <td class="cell-strong">KES <?= number_format($c['price'], 0) ?></td>
                    <td>
                        <?php if ($c['status'] === 'available'): ?><span class="badge badge-success">Available</span>
                        <?php elseif ($c['status'] === 'reserved'): ?><span class="badge badge-warning">Reserved</span>
                        <?php else: ?><span class="badge badge-danger">Sold</span><?php endif; ?>
                    </td>
                    <td><?= $c['featured'] ? '★ Yes' : '—' ?></td>
                    <td>
                        <div class="row-actions">
                            <a href="cars_edit.php?id=<?= $c['id'] ?>" class="btn btn-outline btn-sm">Edit</a>
                            <a href="cars_delete.php?id=<?= $c['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirmDelete('Delete \'<?= htmlspecialchars(addslashes($c['title'])) ?>\' and all its photos?')">Delete</a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
