<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: login.php"); exit;
}
require 'database_config.php';

$msg = '';
if (isset($_GET['cancel'])) {
    $pdo->prepare("UPDATE appointments SET status='cancelled' WHERE id=? AND client_id=? AND status='pending'")
        ->execute([$_GET['cancel'], $_SESSION['user_id']]);
    $msg = "Appointment cancelled.";
}

$appointments = $pdo->prepare("
    SELECT a.*, s.name AS stylist_name, sv.name AS service_name, sv.price
    FROM appointments a
    JOIN users s ON a.stylist_id = s.id
    JOIN services sv ON a.service_id = sv.id
    WHERE a.client_id=?
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
    <div class="top-bar">
        <div class="page-title">My Appointments</div>
        <a href="book_appointment.php" class="btn-add">+ Book New</a>
    </div>

    <?php if ($msg): ?>
        <div class="alert alert-success"><?= $msg ?></div>
    <?php endif; ?>

    <table>
        <thead>
            <tr><th>Service</th><th>Stylist</th><th>Date</th><th>Time</th><th>Price</th><th>Status</th><th>Action</th></tr>
        </thead>
        <tbody>
            <?php foreach ($appointments as $a): ?>
            <tr>
                <td><?= htmlspecialchars($a['service_name']) ?></td>
                <td><?= htmlspecialchars($a['stylist_name']) ?></td>
                <td><?= $a['appointment_date'] ?></td>
                <td><?= $a['appointment_time'] ?></td>
                <td>&#8369;<?= number_format($a['price'], 2) ?></td>
                <td><span class="badge badge-<?= $a['status'] ?>"><?= ucfirst($a['status']) ?></span></td>
                <td>
                    <?php if ($a['status'] === 'pending'): ?>
                    <a href="?cancel=<?= $a['id'] ?>" class="btn-sm btn-delete"
                       onclick="return confirm('Cancel this appointment?')">Cancel</a>
                    <?php else: ?>
                    <span style="color:#aaa;font-size:13px;">—</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($appointments)): ?>
            <tr><td colspan="7" style="text-align:center;color:#999;">No appointments yet.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
