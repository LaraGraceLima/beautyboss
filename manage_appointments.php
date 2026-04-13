<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); exit;
}
require 'database_config.php';

$msg = '';
if (isset($_GET['status']) && isset($_GET['id'])) {
    $allowed = ['pending','confirmed','completed','cancelled'];
    if (in_array($_GET['status'], $allowed)) {
        $pdo->prepare("UPDATE appointments SET status=? WHERE id=?")->execute([$_GET['status'], $_GET['id']]);
        $msg = "Appointment updated.";
    }
}
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM appointments WHERE id=?")->execute([$_GET['delete']]);
    $msg = "Appointment deleted.";
}

$appointments = $pdo->query("
    SELECT a.*, 
        c.name AS client_name, 
        s.name AS stylist_name, 
        sv.name AS service_name,
        sv.price
    FROM appointments a
    JOIN users c ON a.client_id = c.id
    JOIN users s ON a.stylist_id = s.id
    JOIN services sv ON a.service_id = sv.id
    ORDER BY a.appointment_date DESC, a.appointment_time DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Appointments - BeautyBoss</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container">
    <div class="page-title">Manage Appointments</div>

    <?php if ($msg): ?>
        <div class="alert alert-success"><?= $msg ?></div>
    <?php endif; ?>

    <table>
        <thead>
            <tr><th>Client</th><th>Stylist</th><th>Service</th><th>Date</th><th>Time</th><th>Price</th><th>Status</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php foreach ($appointments as $a): ?>
            <tr>
                <td><?= htmlspecialchars($a['client_name']) ?></td>
                <td><?= htmlspecialchars($a['stylist_name']) ?></td>
                <td><?= htmlspecialchars($a['service_name']) ?></td>
                <td><?= $a['appointment_date'] ?></td>
                <td><?= $a['appointment_time'] ?></td>
                <td>&#8369;<?= number_format($a['price'], 2) ?></td>
                <td><span class="badge badge-<?= $a['status'] ?>"><?= ucfirst($a['status']) ?></span></td>
                <td>
                    <?php if ($a['status'] === 'pending'): ?>
                        <a href="?id=<?= $a['id'] ?>&status=confirmed" class="btn-sm btn-confirm">Confirm</a>
                    <?php endif; ?>
                    <?php if ($a['status'] === 'confirmed'): ?>
                        <a href="?id=<?= $a['id'] ?>&status=completed" class="btn-sm btn-edit">Complete</a>
                    <?php endif; ?>
                    <a href="?delete=<?= $a['id'] ?>" class="btn-sm btn-delete"
                       onclick="return confirm('Delete this appointment?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($appointments)): ?>
            <tr><td colspan="8" style="text-align:center;color:#999;">No appointments found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
