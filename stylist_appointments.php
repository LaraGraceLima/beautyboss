<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'stylist') {
    header("Location: login.php"); exit;
}
require 'database_config.php';

$msg = '';
if (isset($_GET['complete'])) {
    $pdo->prepare("UPDATE appointments SET status='completed' WHERE id=? AND stylist_id=?")
        ->execute([$_GET['complete'], $_SESSION['user_id']]);
    $msg = "Appointment marked as completed.";
}

$appointments = $pdo->prepare("
    SELECT a.*, c.name AS client_name, sv.name AS service_name, sv.price
    FROM appointments a
    JOIN users c ON a.client_id = c.id
    JOIN services sv ON a.service_id = sv.id
    WHERE a.stylist_id=?
    ORDER BY a.appointment_date DESC, a.appointment_time DESC
");
$appointments->execute([$_SESSION['user_id']]);
$appointments = $appointments->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Appointments - BeautyBoss</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container">
    <div class="page-title">My Appointments</div>

    <?php if ($msg): ?>
        <div class="alert alert-success"><?= $msg ?></div>
    <?php endif; ?>

    <table>
        <thead>
            <tr><th>Client</th><th>Service</th><th>Date</th><th>Time</th><th>Price</th><th>Notes</th><th>Status</th><th>Action</th></tr>
        </thead>
        <tbody>
            <?php foreach ($appointments as $a): ?>
            <tr>
                <td><?= htmlspecialchars($a['client_name']) ?></td>
                <td><?= htmlspecialchars($a['service_name']) ?></td>
                <td><?= $a['appointment_date'] ?></td>
                <td><?= $a['appointment_time'] ?></td>
                <td>&#8369;<?= number_format($a['price'], 2) ?></td>
                <td><?= htmlspecialchars($a['notes'] ?? '—') ?></td>
                <td><span class="badge badge-<?= $a['status'] ?>"><?= ucfirst($a['status']) ?></span></td>
                <td>
                    <?php if ($a['status'] === 'confirmed'): ?>
                    <a href="?complete=<?= $a['id'] ?>" class="btn-sm btn-confirm">Mark Done</a>
                    <?php else: ?>
                    <span style="color:#aaa;font-size:13px;">—</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($appointments)): ?>
            <tr><td colspan="8" style="text-align:center;color:#999;">No appointments yet.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
