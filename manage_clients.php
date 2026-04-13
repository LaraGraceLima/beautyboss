<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); exit;
}
require 'database_config.php';

$msg = '';
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM users WHERE id=? AND role='client'")->execute([$_GET['delete']]);
    $msg = "Client deleted.";
}

$clients = $pdo->query("SELECT * FROM users WHERE role='client' ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Clients - BeautyBoss</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container">
    <div class="page-title">Manage Clients</div>

    <?php if ($msg): ?>
        <div class="alert alert-success"><?= $msg ?></div>
    <?php endif; ?>

    <table>
        <thead>
            <tr><th>Name</th><th>Email</th><th>Phone</th><th>Joined</th><th>Action</th></tr>
        </thead>
        <tbody>
            <?php foreach ($clients as $c): ?>
            <tr>
                <td><?= htmlspecialchars($c['name']) ?></td>
                <td><?= htmlspecialchars($c['email']) ?></td>
                <td><?= htmlspecialchars($c['phone']) ?></td>
                <td><?= date('M d, Y', strtotime($c['created_at'])) ?></td>
                <td>
                    <a href="?delete=<?= $c['id'] ?>" class="btn-sm btn-delete"
                       onclick="return confirm('Delete this client?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($clients)): ?>
            <tr><td colspan="5" style="text-align:center;color:#999;">No clients found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
