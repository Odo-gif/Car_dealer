<?php
session_start();
if (!empty($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../config/db.php';

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Please enter both username and password.';
    } else {
        $stmt = $pdo->prepare('SELECT id, full_name, username, password, role FROM users WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['admin_id']   = $user['id'];
            $_SESSION['admin_name'] = $user['full_name'];
            $_SESSION['admin_role'] = $user['role'];
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Sign In · AutoDeal</title>
<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<div class="login-wrap">
    <div class="login-card">
        <div class="login-side">
            <div class="brand-icon">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 17h14M5 17a2 2 0 0 1-2-2v-2l2-5a2 2 0 0 1 2-1h6a2 2 0 0 1 2 1l2 5v2a2 2 0 0 1-2 2M5 17a2 2 0 0 0 2 2h0a2 2 0 0 0 2-2M17 17a2 2 0 0 0 2 2h0a2 2 0 0 0 2-2"></path></svg>
            </div>
            <h1>AutoDeal Admin</h1>
            <p>Manage your car listings, photos, prices and customer inquiries from one dashboard.</p>
            <ul class="feature-list">
                <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fdba74" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> Add &amp; edit listings with photos</li>
                <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fdba74" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> Track buyer inquiries &amp; leads</li>
                <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fdba74" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> Mark cars sold / reserved instantly</li>
            </ul>
        </div>
        <div class="login-form">
            <h2>Welcome back</h2>
            <div class="sub">Sign in to manage your listings</div>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="index.php">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" placeholder="e.g. admin" required autofocus>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Sign In</button>
            </form>

            <div class="login-hint">
                Default login &mdash; Username: <b>admin</b> &nbsp;|&nbsp; Password: <b>admin123</b><br>
                Please change this password after your first login.
            </div>
        </div>
    </div>
</div>
</body>
</html>
