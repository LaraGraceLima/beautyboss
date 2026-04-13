<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: " . $_SESSION['role'] . "_dashboard.php");
    exit;
}
require 'database_config.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name']    = $user['name'];
        $_SESSION['role']    = $user['role'];
        header("Location: " . $user['role'] . "_dashboard.php");
        exit;
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - BeautyBoss</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="form-card">
    <div class="login-logo">&#9997; BeautyBoss</div>
    <div class="login-sub">Salon Appointment Booking</div>
    <h2>Login</h2>
    <?php if ($error): ?>
        <div class="alert alert-error"><?= $error ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required placeholder="Enter your email">
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required placeholder="Enter your password">
        </div>
        <button type="submit" class="btn">Login</button>
    </form>
    <div class="form-footer">
        Don't have an account? <a href="register.php">Register here</a>
    </div>
</div>
</body>
</html>
