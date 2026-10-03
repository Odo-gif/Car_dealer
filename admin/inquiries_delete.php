<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/../config/db.php';

$id = $_GET['id'] ?? null;
if ($id) {
    $pdo->prepare("DELETE FROM inquiries WHERE id = ?")->execute([$id]);
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Inquiry deleted.'];
}

header('Location: inquiries.php');
exit;
