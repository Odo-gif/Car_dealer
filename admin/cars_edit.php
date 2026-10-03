<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/../config/db.php';

$pageTitle = 'Edit Car';
$activePage = 'cars';

$id = $_GET['id'] ?? $_POST['id'] ?? null;
if (!$id) { header('Location: cars_list.php'); exit; }

$stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ?");
$stmt->execute([$id]);
$car = $stmt->fetch();
if (!$car) {
    $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Car not found.'];
    header('Location: cars_list.php');
    exit;
}

$errors = [];
$form = $car;
$form['featured'] = (int)$car['featured'];

$uploadDir = __DIR__ . '/../assets/uploads/cars/';
$allowedTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (['title','make','model','year','price','mileage','transmission','fuel_type','body_type','color','location','status','description'] as $key) {
        $form[$key] = trim($_POST[$key] ?? '');
    }
    $form['featured'] = isset($_POST['featured']) ? 1 : 0;

    if ($form['title'] === '') $errors[] = 'Car title is required.';
    if ($form['make'] === '') $errors[] = 'Make is required.';
    if ($form['model'] === '') $errors[] = 'Model is required.';
    if ($form['price'] === '' || !is_numeric($form['price']) || $form['price'] < 0) $errors[] = 'Enter a valid price.';

    $filesToSave = [];
    if (!empty($_FILES['images']['name'][0])) {
        foreach ($_FILES['images']['tmp_name'] as $i => $tmpName) {
            if (!$tmpName) continue;
            $type = mime_content_type($tmpName);
            if (!isset($allowedTypes[$type])) { $errors[] = 'Only JPG, PNG or WEBP images are allowed.'; break; }
            if ($_FILES['images']['size'][$i] > 5 * 1024 * 1024) { $errors[] = 'Each image must be under 5MB.'; break; }
            $filesToSave[] = ['tmp' => $tmpName, 'ext' => $allowedTypes[$type]];
        }
    }

    if (!$errors) {
        $upd = $pdo->prepare("UPDATE cars SET
            title=?, make=?, model=?, year=?, price=?, mileage=?, transmission=?, fuel_type=?,
            body_type=?, color=?, location=?, status=?, featured=?, description=?
            WHERE id=?");
        $upd->execute([
            $form['title'], $form['make'], $form['model'], $form['year'], $form['price'],
            $form['mileage'] ?: null, $form['transmission'], $form['fuel_type'],
            $form['body_type'] ?: null, $form['color'] ?: null, $form['location'] ?: null,
            $form['status'], $form['featured'], $form['description'] ?: null, $id,
        ]);

        // Remove images the admin checked for deletion
        $deleteIds = $_POST['delete_images'] ?? [];
        foreach ($deleteIds as $delId) {
            $imgStmt = $pdo->prepare("SELECT * FROM car_images WHERE id = ? AND car_id = ?");
            $imgStmt->execute([$delId, $id]);
            $img = $imgStmt->fetch();
            if ($img) {
                $path = __DIR__ . '/../' . $img['image_path'];
                if (is_file($path)) @unlink($path);
                $pdo->prepare("DELETE FROM car_images WHERE id = ?")->execute([$delId]);
            }
        }

        // Add newly uploaded images
        $primaryCheck = $pdo->prepare("SELECT id FROM car_images WHERE car_id = ? AND is_primary = 1");
        $primaryCheck->execute([$id]);
        $hasPrimary = (bool)$primaryCheck->fetchColumn();
        foreach ($filesToSave as $i => $file) {
            $filename = 'car_' . $id . '_' . time() . '_' . $i . '.' . $file['ext'];
            if (move_uploaded_file($file['tmp'], $uploadDir . $filename)) {
                $makePrimary = (!$hasPrimary && $i === 0) ? 1 : 0;
                $imgStmt = $pdo->prepare("INSERT INTO car_images (car_id, image_path, is_primary) VALUES (?,?,?)");
                $imgStmt->execute([$id, 'assets/uploads/cars/' . $filename, $makePrimary]);
                if ($makePrimary) $hasPrimary = true;
            }
        }

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Car "' . $form['title'] . '" updated successfully.'];
        header('Location: cars_list.php');
        exit;
    }
}

$imagesStmt = $pdo->prepare("SELECT * FROM car_images WHERE car_id = ? ORDER BY is_primary DESC, id ASC");
$imagesStmt->execute([$id]);
$images = $imagesStmt->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<div class="topbar">
    <div>
        <h1>Edit Car</h1>
        <div class="breadcrumb"><a href="cars_list.php">Manage Cars</a> / Edit</div>
    </div>
</div>

<?php foreach ($errors as $e): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($e) ?></div>
<?php endforeach; ?>

<div class="card">
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $car['id'] ?>">

            <div class="form-row">
                <div class="form-group">
                    <label>Listing Title *</label>
                    <input type="text" name="title" value="<?= htmlspecialchars($form['title']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status">
                        <option value="available" <?= $form['status'] === 'available' ? 'selected' : '' ?>>Available</option>
                        <option value="reserved" <?= $form['status'] === 'reserved' ? 'selected' : '' ?>>Reserved</option>
                        <option value="sold" <?= $form['status'] === 'sold' ? 'selected' : '' ?>>Sold</option>
                    </select>
                </div>
            </div>

            <div class="form-row-3">
                <div class="form-group">
                    <label>Make *</label>
                    <input type="text" name="make" value="<?= htmlspecialchars($form['make']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Model *</label>
                    <input type="text" name="model" value="<?= htmlspecialchars($form['model']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Year *</label>
                    <input type="number" name="year" value="<?= htmlspecialchars($form['year']) ?>" min="1980" max="<?= date('Y') + 1 ?>" required>
                </div>
            </div>

            <div class="form-row-3">
                <div class="form-group">
                    <label>Price (KES) *</label>
                    <input type="number" step="0.01" min="0" name="price" value="<?= htmlspecialchars($form['price']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Mileage (km)</label>
                    <input type="number" min="0" name="mileage" value="<?= htmlspecialchars($form['mileage']) ?>">
                </div>
                <div class="form-group">
                    <label>Color</label>
                    <input type="text" name="color" value="<?= htmlspecialchars($form['color']) ?>">
                </div>
            </div>

            <div class="form-row-3">
                <div class="form-group">
                    <label>Transmission</label>
                    <select name="transmission">
                        <option value="automatic" <?= $form['transmission'] === 'automatic' ? 'selected' : '' ?>>Automatic</option>
                        <option value="manual" <?= $form['transmission'] === 'manual' ? 'selected' : '' ?>>Manual</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Fuel Type</label>
                    <select name="fuel_type">
                        <?php foreach (['petrol','diesel','hybrid','electric'] as $f): ?>
                            <option value="<?= $f ?>" <?= $form['fuel_type'] === $f ? 'selected' : '' ?>><?= ucfirst($f) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Body Type</label>
                    <input type="text" name="body_type" value="<?= htmlspecialchars($form['body_type']) ?>">
                </div>
            </div>

            <div class="form-group">
                <label>Location</label>
                <input type="text" name="location" value="<?= htmlspecialchars($form['location']) ?>">
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description"><?= htmlspecialchars($form['description']) ?></textarea>
            </div>

            <div class="form-group checkbox-row">
                <input type="checkbox" name="featured" id="featured" <?= $form['featured'] ? 'checked' : '' ?>>
                <label for="featured" style="margin:0;">Mark as Featured (shows on homepage)</label>
            </div>

            <?php if ($images): ?>
            <div class="form-group">
                <label>Current Photos (check to remove)</label>
                <div class="image-preview-grid current-images">
                    <?php foreach ($images as $img): ?>
                        <label class="thumb" style="cursor:pointer;">
                            <img src="../<?= htmlspecialchars($img['image_path']) ?>" alt="">
                            <input type="checkbox" name="delete_images[]" value="<?= $img['id'] ?>" style="position:absolute; top:4px; left:4px;">
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="form-group">
                <label>Add More Photos</label>
                <label for="carImages" class="upload-box">
                    📷 Click to select photos (JPG, PNG, WEBP — max 5MB each)
                    <input type="file" id="carImages" name="images[]" accept="image/jpeg,image/png,image/webp" multiple style="display:none;">
                </label>
                <div id="newImagePreview" class="image-preview-grid"></div>
            </div>

            <div class="page-actions">
                <button type="submit" class="btn btn-primary">Update Listing</button>
                <a href="cars_list.php" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('carImages').addEventListener('change', function(e){
    document.querySelector('.upload-box').firstChild.textContent = e.target.files.length + ' file(s) selected — click to change ';
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
