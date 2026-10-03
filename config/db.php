<?php
/**
 * AutoDeal Car Sales Website
 * Database connection (PDO + prepared statements)
 */

$DB_HOST = 'localhost';
$DB_NAME = 'car_dealer';
$DB_USER = 'root';
$DB_PASS = ''; // default XAMPP MySQL password is empty

try {
    $pdo = new PDO(
        "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die('<div style="font-family:sans-serif;padding:40px;color:#b91c1c;">
        <h2>Database connection failed</h2>
        <p>Make sure XAMPP MySQL is running and the <code>car_dealer</code> database has been imported.</p>
        <p style="color:#6b7280;font-size:13px;">Details: ' . htmlspecialchars($e->getMessage()) . '</p>
        </div>');
}
