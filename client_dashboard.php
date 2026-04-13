<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: login.php"); exit;
}
require 'database_config.php';

$id = $_SESSION['user_id'];
$total     = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE client_id=?");
$total->execute([$id]); $total = $total->fetchColumn();

$pending   = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE client_id=? AND status='pending'");
$pending->execute([$id]); $pending = $pending->fetchColumn();

$completed = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE client_id=? AND status='completed'");
$completed->execute([$id]); $completed = $completed->fetchColumn();

$upcoming = $pdo->prepare("
    SELECT a.*, s.name AS stylist_name, sv.name AS service_name, sv.price
    FROM appointments a
    JOIN users s ON a.stylist_id = s.id
    JOIN services sv ON a.service_id = sv.id
    WHERE a.client_id=? AND a.status IN ('pending','confirmed') AND a.appointment_date >= CURDATE()
    ORDER BY a.appointment_date, a.appointment_time LIMIT 5
");
$upcoming->execute([$id]);
$upcoming = $upcoming->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Dashboard - BeautyBoss</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container">
    <div class="page-title">Welcome, <?= htmlspecialchars($_SESSION['name']) ?></div>

    <div class="dashboard-cards">
        <div class="card">
            <h3><?= $total ?></h3>
            <p>Total Bookings</p>
        </div>
        <div class="card">
            <h3><?= $pending ?></h3>
            <p>Pending</p>
        </div>
        <div class="card">
            <h3><?= $completed ?></h3>
            <p>Completed</p>
        </div>
    </div>

    <div class="top-bar">
        <div class="page-title" style="font-size:18px;">Upcoming Appointments</div>
        <a href="book_appointment.php" class="btn-add">+ Book Appointment</a>
    </div>

    <table>
        <thead>
            <tr><th>Service</th><th>Stylist</th><th>Date</th><th>Time</th><th>Price</th><th>Status</th></tr>
        </thead>
        <tbody>
            <?php foreach ($upcoming as $a): ?>
            <tr>
                <td><?= htmlspecialchars($a['service_name']) ?></td>
                <td><?= htmlspecialchars($a['stylist_name']) ?></td>
                <td><?= $a['appointment_date'] ?></td>
                <td><?= $a['appointment_time'] ?></td>
                <td>&#8369;<?= number_format($a['price'], 2) ?></td>
                <td><span class="badge badge-<?= $a['status'] ?>"><?= ucfirst($a['status']) ?></span></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($upcoming)): ?>
            <tr><td colspan="6" style="text-align:center;color:#999;">No upcoming appointments. <a href="book_appointment.php">Book one now!</a></td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
