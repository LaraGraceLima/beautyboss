<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); exit;
}
require 'database_config.php';

$msg = '';
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM services WHERE id=?")->execute([$_GET['delete']]);
    $msg = "Service deleted.";
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']);
    $desc     = trim($_POST['description']);
    $price    = $_POST['price'];
    $duration = $_POST['duration'];
    $pdo->prepare("INSERT INTO services (name,description,price,duration) VALUES (?,?,?,?)")
        ->execute([$name, $desc, $price, $duration]);
    $msg = "Service added.";
}

$services = $pdo->query("SELECT * FROM services ORDER BY name")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Services - BeautyBoss</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container">
    <div class="top-bar">
        <div class="page-title">Manage Services</div>
        <a href="#add-form" class="btn-add">+ Add Service</a>
    </div>

    <?php if ($msg): ?>
        <div class="alert alert-success"><?= $msg ?></div>
    <?php endif; ?>

    <table>
        <thead>
            <tr><th>Service</th><th>Description</th><th>Price</th><th>Duration</th><th>Action</th></tr>
        </thead>
        <tbody>
            <?php foreach ($services as $s): ?>
            <tr>
                <td><?= htmlspecialchars($s['name']) ?></td>
                <td><?= htmlspecialchars($s['description']) ?></td>
                <td>&#8369;<?= number_format($s['price'], 2) ?></td>
                <td><?= $s['duration'] ?> mins</td>
                <td>
                    <a href="?delete=<?= $s['id'] ?>" class="btn-sm btn-delete"
                       onclick="return confirm('Delete this service?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div id="add-form" style="margin-top:40px;">
        <div class="page-title" style="font-size:18px;">Add New Service</div>
        <div class="form-card" style="margin:0;max-width:100%;">
            <form method="POST">
                <div style="display:flex;gap:16px;flex-wrap:wrap;">
                    <div class="form-group" style="flex:1;min-width:180px;">
                        <label>Service Name</label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="form-group" style="flex:1;min-width:180px;">
                        <label>Price (&#8369;)</label>
                        <input type="number" name="price" step="0.01" required>
                    </div>
                    <div class="form-group" style="flex:1;min-width:180px;">
                        <label>Duration (minutes)</label>
                        <input type="number" name="duration" required>
                    </div>
                    <div class="form-group" style="flex:2;min-width:200px;">
                        <label>Description</label>
                        <input type="text" name="description">
                    </div>
                </div>
                <button type="submit" class="btn" style="max-width:200px;">Add Service</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
