<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); exit;
}
require 'database_config.php';

$totalClients   = $pdo->query("SELECT COUNT(*) FROM users WHERE role='client'")->fetchColumn();
$totalStylists  = $pdo->query("SELECT COUNT(*) FROM users WHERE role='stylist'")->fetchColumn();
$totalAppts     = $pdo->query("SELECT COUNT(*) FROM appointments")->fetchColumn();
$pendingAppts   = $pdo->query("SELECT COUNT(*) FROM appointments WHERE status='pending'")->fetchColumn();

$recentAppts = $pdo->query("
    SELECT a.*, 
        c.name AS client_name, 
        s.name AS stylist_name, 
        sv.name AS service_name
    FROM appointments a
    JOIN users c ON a.client_id = c.id
    JOIN users s ON a.stylist_id = s.id
    JOIN services sv ON a.service_id = sv.id
    ORDER BY a.created_at DESC LIMIT 5
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - BeautyBoss</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container">
    <div class="page-title">Admin Dashboard</div>

    <div class="dashboard-cards">
        <div class="card">
            <h3><?= $totalClients ?></h3>
            <p>Total Clients</p>
        </div>
        <div class="card">
            <h3><?= $totalStylists ?></h3>
            <p>Total Stylists</p>
        </div>
        <div class="card">
            <h3><?= $totalAppts ?></h3>
            <p>Total Appointments</p>
        </div>
        <div class="card">
            <h3><?= $pendingAppts ?></h3>
            <p>Pending Appointments</p>
        </div>
    </div>

    <div class="page-title" style="font-size:18px;">Recent Appointments</div>
    <table>
        <thead>
            <tr>
                <th>Client</th>
                <th>Stylist</th>
                <th>Service</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recentAppts as $a): ?>
            <tr>
                <td><?= htmlspecialchars($a['client_name']) ?></td>
                <td><?= htmlspecialchars($a['stylist_name']) ?></td>
                <td><?= htmlspecialchars($a['service_name']) ?></td>
                <td><?= $a['appointment_date'] ?></td>
                <td><?= $a['appointment_time'] ?></td>
                <td><span class="badge badge-<?= $a['status'] ?>"><?= ucfirst($a['status']) ?></span></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($recentAppts)): ?>
            <tr><td colspan="6" style="text-align:center;color:#999;">No appointments yet.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
