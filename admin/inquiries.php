<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/../config/db.php';

$pageTitle = 'Inquiries';
$activePage = 'inquiries';

// Handle status update / delete actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['mark_status'], $_POST['inquiry_id'])) {
        $stmt = $pdo->prepare("UPDATE inquiries SET status = ? WHERE id = ?");
        $stmt->execute([$_POST['mark_status'], $_POST['inquiry_id']]);
    }
    header('Location: inquiries.php');
    exit;
}

$statusFilter = $_GET['status'] ?? '';
$sql = "SELECT i.*, c.title AS car_title FROM inquiries i LEFT JOIN cars c ON i.car_id = c.id";
$params = [];
if ($statusFilter !== '') { $sql .= " WHERE i.status = ?"; $params[] = $statusFilter; }
$sql .= " ORDER BY i.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$inquiries = $stmt->fetchAll();

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

include __DIR__ . '/includes/header.php';
?>

<div class="topbar">
    <div>
        <h1>Inquiries</h1>
        <div class="breadcrumb">Leads from your website's contact &amp; inquiry forms</div>
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
                <input type="search" id="liveSearch" placeholder="Search by name or phone...">
            </div>
            <form method="GET">
                <select name="status" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="new" <?= $statusFilter === 'new' ? 'selected' : '' ?>>New</option>
                    <option value="contacted" <?= $statusFilter === 'contacted' ? 'selected' : '' ?>>Contacted</option>
                    <option value="closed" <?= $statusFilter === 'closed' ? 'selected' : '' ?>>Closed</option>
                </select>
            </form>
        </div>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Name</th><th>Phone</th><th>Car</th><th>Message</th><th>Date</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
            <?php if (!$inquiries): ?>
                <tr><td colspan="7" class="empty-state">No inquiries yet.</td></tr>
            <?php endif; ?>
            <?php foreach ($inquiries as $i): ?>
                <tr data-search="<?= htmlspecialchars(strtolower($i['name'] . ' ' . $i['phone'])) ?>">
                    <td class="cell-strong"><?= htmlspecialchars($i['name']) ?></td>
                    <td><a href="tel:<?= htmlspecialchars($i['phone']) ?>"><?= htmlspecialchars($i['phone']) ?></a></td>
                    <td class="cell-muted"><?= htmlspecialchars($i['car_title'] ?? 'General inquiry') ?></td>
                    <td class="cell-muted" style="max-width:220px;"><?= htmlspecialchars(mb_strimwidth($i['message'] ?? '', 0, 60, '...')) ?></td>
                    <td class="cell-muted"><?= date('d M Y, H:i', strtotime($i['created_at'])) ?></td>
                    <td>
                        <?php if ($i['status'] === 'new'): ?><span class="badge badge-warning">New</span>
                        <?php elseif ($i['status'] === 'contacted'): ?><span class="badge badge-success">Contacted</span>
                        <?php else: ?><span class="badge badge-muted">Closed</span><?php endif; ?>
                    </td>
                    <td>
                        <div class="row-actions">
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="inquiry_id" value="<?= $i['id'] ?>">
                                <?php if ($i['status'] !== 'contacted'): ?>
                                    <button type="submit" name="mark_status" value="contacted" class="btn btn-outline btn-sm">Mark Contacted</button>
                                <?php else: ?>
                                    <button type="submit" name="mark_status" value="closed" class="btn btn-outline btn-sm">Close</button>
                                <?php endif; ?>
                            </form>
                            <a href="inquiries_delete.php?id=<?= $i['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirmDelete('Delete this inquiry?')">Delete</a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
