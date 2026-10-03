<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/../config/db.php';

$pageTitle = 'Add New Car';
$activePage = 'cars_add';

$errors = [];
$form = [
    'title' => '', 'make' => '', 'model' => '', 'year' => date('Y'), 'price' => '',
    'mileage' => '', 'transmission' => 'automatic', 'fuel_type' => 'petrol',
    'body_type' => '', 'color' => '', 'location' => '', 'status' => 'available',
    'featured' => '', 'description' => '',
];

$uploadDir = __DIR__ . '/../assets/uploads/cars/';
$allowedTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($form as $key => $default) {
        if ($key === 'featured') continue;
        $form[$key] = trim($_POST[$key] ?? $default);
    }
    $form['featured'] = isset($_POST['featured']) ? 1 : 0;

    if ($form['title'] === '') $errors[] = 'Car title is required.';
    if ($form['make'] === '') $errors[] = 'Make is required.';
    if ($form['model'] === '') $errors[] = 'Model is required.';
    if ($form['price'] === '' || !is_numeric($form['price']) || $form['price'] < 0) $errors[] = 'Enter a valid price.';
    if (!ctype_digit((string)$form['year']) || $form['year'] < 1980 || $form['year'] > (int)date('Y') + 1) $errors[] = 'Enter a valid year.';

    // Validate any uploaded images before touching the DB
    $filesToSave = [];
    if (!empty($_FILES['images']['name'][0])) {
        foreach ($_FILES['images']['tmp_name'] as $i => $tmpName) {
            if (!$tmpName) continue;
            $type = mime_content_type($tmpName);
            if (!isset($allowedTypes[$type])) {
                $errors[] = 'Only JPG, PNG or WEBP images are allowed.';
                break;
            }
            if ($_FILES['images']['size'][$i] > 5 * 1024 * 1024) {
                $errors[] = 'Each image must be under 5MB.';
                break;
            }
            $filesToSave[] = ['tmp' => $tmpName, 'ext' => $allowedTypes[$type]];
        }
    }

    if (!$errors) {
        $stmt = $pdo->prepare("INSERT INTO cars
            (title, make, model, year, price, mileage, transmission, fuel_type, body_type, color, location, status, featured, description)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([
            $form['title'], $form['make'], $form['model'], $form['year'], $form['price'],
            $form['mileage'] ?: null, $form['transmission'], $form['fuel_type'],
            $form['body_type'] ?: null, $form['color'] ?: null, $form['location'] ?: null,
            $form['status'], $form['featured'], $form['description'] ?: null,
        ]);
        $carId = $pdo->lastInsertId();

        foreach ($filesToSave as $i => $file) {
            $filename = 'car_' . $carId . '_' . time() . '_' . $i . '.' . $file['ext'];
            if (move_uploaded_file($file['tmp'], $uploadDir . $filename)) {
                $imgStmt = $pdo->prepare("INSERT INTO car_images (car_id, image_path, is_primary) VALUES (?,?,?)");
                $imgStmt->execute([$carId, 'assets/uploads/cars/' . $filename, $i === 0 ? 1 : 0]);
            }
        }

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Car "' . $form['title'] . '" added successfully.'];
        header('Location: cars_list.php');
        exit;
    }
}

include __DIR__ . '/includes/header.php';
?>

<div class="topbar">
    <div>
        <h1>Add New Car</h1>
        <div class="breadcrumb"><a href="cars_list.php">Manage Cars</a> / Add New</div>
    </div>
</div>

<?php foreach ($errors as $e): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($e) ?></div>
<?php endforeach; ?>

<div class="card">
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group">
                    <label>Listing Title *</label>
                    <input type="text" name="title" value="<?= htmlspecialchars($form['title']) ?>" placeholder="e.g. Toyota Axio 2015" required>
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
                    <input type="text" name="make" value="<?= htmlspecialchars($form['make']) ?>" placeholder="e.g. Toyota" required>
                </div>
                <div class="form-group">
                    <label>Model *</label>
                    <input type="text" name="model" value="<?= htmlspecialchars($form['model']) ?>" placeholder="e.g. Axio" required>
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
                    <input type="text" name="body_type" value="<?= htmlspecialchars($form['body_type']) ?>" placeholder="e.g. Sedan, SUV, Hatchback">
                </div>
            </div>

            <div class="form-group">
                <label>Location</label>
                <input type="text" name="location" value="<?= htmlspecialchars($form['location']) ?>" placeholder="e.g. Nairobi">
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" placeholder="Full car description — condition, history, features..."><?= htmlspecialchars($form['description']) ?></textarea>
            </div>

            <div class="form-group checkbox-row">
                <input type="checkbox" name="featured" id="featured" <?= $form['featured'] ? 'checked' : '' ?>>
                <label for="featured" style="margin:0;">Mark as Featured (shows on homepage)</label>
            </div>

            <div class="form-group">
                <label>Car Photos (first photo becomes the main image)</label>
                <label for="carImages" class="upload-box">
                    📷 Click to select photos (JPG, PNG, WEBP — max 5MB each)
                    <input type="file" id="carImages" name="images[]" accept="image/jpeg,image/png,image/webp" multiple style="display:none;">
                </label>
                <div id="newImagePreview" class="image-preview-grid"></div>
            </div>

            <div class="page-actions">
                <button type="submit" class="btn btn-primary">Save Listing</button>
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
