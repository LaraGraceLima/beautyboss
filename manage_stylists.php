<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); exit;
}
require 'database_config.php';

$msg = '';

// Delete stylist
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM users WHERE id=? AND role='stylist'")->execute([$_GET['delete']]);
    $msg = "Stylist deleted.";
}

// Add stylist
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $pass  = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $check = $pdo->prepare("SELECT id FROM users WHERE email=?");
    $check->execute([$email]);
    if ($check->fetch()) {
        $msg = "Email already exists.";
    } else {
        $pdo->prepare("INSERT INTO users (name,email,password,role,phone) VALUES (?,?,?,'stylist',?)")
            ->execute([$name, $email, $pass, $phone]);
        $msg = "Stylist added successfully.";
    }
}

$stylists = $pdo->query("SELECT * FROM users WHERE role='stylist' ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Stylists - BeautyBoss</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container">
    <div class="top-bar">
        <div class="page-title">Manage Stylists</div>
        <a href="#add-form" class="btn-add">+ Add Stylist</a>
    </div>

    <?php if ($msg): ?>
        <div class="alert alert-success"><?= $msg ?></div>
    <?php endif; ?>

    <table>
        <thead>
            <tr><th>Name</th><th>Email</th><th>Phone</th><th>Joined</th><th>Action</th></tr>
        </thead>
        <tbody>
            <?php foreach ($stylists as $s): ?>
            <tr>
                <td><?= htmlspecialchars($s['name']) ?></td>
                <td><?= htmlspecialchars($s['email']) ?></td>
                <td><?= htmlspecialchars($s['phone']) ?></td>
                <td><?= date('M d, Y', strtotime($s['created_at'])) ?></td>
                <td>
                    <a href="?delete=<?= $s['id'] ?>" class="btn-sm btn-delete"
                       onclick="return confirm('Delete this stylist?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($stylists)): ?>
            <tr><td colspan="5" style="text-align:center;color:#999;">No stylists found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div id="add-form" style="margin-top:40px;">
        <div class="page-title" style="font-size:18px;">Add New Stylist</div>
        <div class="form-card" style="margin:0;max-width:100%;">
            <form method="POST">
                <div style="display:flex;gap:16px;flex-wrap:wrap;">
                    <div class="form-group" style="flex:1;min-width:180px;">
                        <label>Full Name</label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="form-group" style="flex:1;min-width:180px;">
                        <label>Email</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="form-group" style="flex:1;min-width:180px;">
                        <label>Phone</label>
                        <input type="text" name="phone">
                    </div>
                    <div class="form-group" style="flex:1;min-width:180px;">
                        <label>Password</label>
                        <input type="password" name="password" required>
                    </div>
                </div>
                <button type="submit" class="btn" style="max-width:200px;">Add Stylist</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
