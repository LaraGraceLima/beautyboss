<?php
session_start();
require 'database_config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $phone    = trim($_POST['phone']);
    $password = $_POST['password'];
    $role     = $_POST['role'];

    // Only allow client or stylist self-registration
    if (!in_array($role, ['client', 'stylist'])) {
        $error = "Invalid role selected.";
    } else {
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetch()) {
            $error = "Email already registered.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, phone) VALUES (?,?,?,?,?)");
            $stmt->execute([$name, $email, $hash, $role, $phone]);
            $success = "Account created! <a href='login.php'>Login here</a>.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - BeautyBoss</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="form-card">
    <div class="login-logo">&#9997; BeautyBoss</div>
    <h2>Create Account</h2>
    <?php if ($error): ?>
        <div class="alert alert-error"><?= $error ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="name" required placeholder="Your full name">
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required placeholder="Your email">
        </div>
        <div class="form-group">
            <label>Phone</label>
            <input type="text" name="phone" placeholder="Your phone number">
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required placeholder="Create a password">
        </div>
        <div class="form-group">
            <label>Register as</label>
            <select name="role">
                <option value="client">Client</option>
                <option value="stylist">Stylist</option>
            </select>
        </div>
        <button type="submit" class="btn">Register</button>
    </form>
    <div class="form-footer">
        Already have an account? <a href="login.php">Login here</a>
    </div>
</div>
</body>
</html>
