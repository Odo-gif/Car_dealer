<?php
session_start();
require_once __DIR__ . '/config/db.php';

$carId = $_POST['car_id'] ?? null;
$name = trim($_POST['name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$email = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

$redirect = $carId ? "car_details.php?id=$carId" : 'contact.php';

if ($name === '' || $phone === '') {
    $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Please provide your name and phone number.'];
    header("Location: $redirect");
    exit;
}

$stmt = $pdo->prepare("INSERT INTO inquiries (car_id, name, phone, email, message) VALUES (?,?,?,?,?)");
$stmt->execute([$carId ?: null, $name, $phone, $email ?: null, $message ?: null]);

$_SESSION['flash'] = ['type' => 'success', 'message' => 'Thanks ' . $name . '! Your inquiry has been sent. We will contact you shortly.'];
header("Location: $redirect");
exit;
