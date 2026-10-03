<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/../config/db.php';

$id = $_GET['id'] ?? null;
if ($id) {
    $stmt = $pdo->prepare("SELECT title FROM cars WHERE id = ?");
    $stmt->execute([$id]);
    $car = $stmt->fetch();

    if ($car) {
        // Remove image files from disk first
        $imgStmt = $pdo->prepare("SELECT image_path FROM car_images WHERE car_id = ?");
        $imgStmt->execute([$id]);
        foreach ($imgStmt->fetchAll() as $img) {
            $path = __DIR__ . '/../' . $img['image_path'];
            if (is_file($path)) @unlink($path);
        }

        // car_images rows cascade-delete via FK, inquiries.car_id is set NULL via FK
        $del = $pdo->prepare("DELETE FROM cars WHERE id = ?");
        $del->execute([$id]);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Car "' . $car['title'] . '" and its photos were deleted.'];
    } else {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Car not found.'];
    }
}

header('Location: cars_list.php');
exit;
