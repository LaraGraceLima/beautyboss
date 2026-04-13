<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'stylist') {
    header("Location: login.php"); exit;
}
require 'database_config.php';

$id = $_SESSION['user_id'];

$total = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE stylist_id=?");
$total->execute([$id]); $total = $total->fetchColumn();

$today = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE stylist_id=? AND appointment_date=CURDATE() AND status != 'cancelled'");
$today->execute([$id]); $today = $today->fetchColumn();

$pending = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE stylist_id=? AND status='pending'");
$pending->execute([$id]); $pending = $pending->fetchColumn();

$upcoming = $pdo->prepare("
    SELECT a.*, c.name AS client_name, sv.name AS service_name, sv.price
    FROM appointments a
    JOIN users c ON a.client_id = c.id
    JOIN services sv ON a.service_id = sv.id
    WHERE a.stylist_id=? AND a.appointment_date >= CURDATE() AND a.status != 'cancelled'
    ORDER BY a.appointment_date, a.appointment_time LIMIT 5
");
$upcoming->execute([$id]);
$upcoming = $upcoming->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Stylist Dashboard - BeautyBoss</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container">
    <div class="page-title">Welcome, <?= htmlspecialchars($_SESSION['name']) ?></div>

    <div class="dashboard-cards">
        <div class="card">
            <h3><?= $total ?></h3>
            <p>Total Appointments</p>
        </div>
        <div class="card">
            <h3><?= $today ?></h3>
            <p>Today's Schedule</p>
        </div>
        <div class="card">
            <h3><?= $pending ?></h3>
            <p>Pending</p>
        </div>
    </div>

    <div class="page-title" style="font-size:18px;">Upcoming Appointments</div>
    <table>
        <thead>
            <tr><th>Client</th><th>Service</th><th>Date</th><th>Time</th><th>Price</th><th>Status</th></tr>
        </thead>
        <tbody>
            <?php foreach ($upcoming as $a): ?>
            <tr>
                <td><?= htmlspecialchars($a['client_name']) ?></td>
                <td><?= htmlspecialchars($a['service_name']) ?></td>
                <td><?= $a['appointment_date'] ?></td>
                <td><?= $a['appointment_time'] ?></td>
                <td>&#8369;<?= number_format($a['price'], 2) ?></td>
                <td><span class="badge badge-<?= $a['status'] ?>"><?= ucfirst($a['status']) ?></span></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($upcoming)): ?>
            <tr><td colspan="6" style="text-align:center;color:#999;">No upcoming appointments.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
