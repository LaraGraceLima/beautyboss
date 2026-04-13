<?php
session_start();
?>
<nav class="navbar">
    <div class="nav-brand">
        <span class="brand-icon">&#9997;</span> BeautyBoss
    </div>
    <div class="nav-links">
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="admin_dashboard.php">Dashboard</a>
                <a href="manage_stylists.php">Stylists</a>
                <a href="manage_clients.php">Clients</a>
                <a href="manage_appointments.php">Appointments</a>
                <a href="manage_services.php">Services</a>
            <?php elseif ($_SESSION['role'] === 'stylist'): ?>
                <a href="stylist_dashboard.php">Dashboard</a>
                <a href="stylist_appointments.php">My Appointments</a>
            <?php elseif ($_SESSION['role'] === 'client'): ?>
                <a href="client_dashboard.php">Dashboard</a>
                <a href="book_appointment.php">Book Appointment</a>
                <a href="my_appointments.php">My Appointments</a>
            <?php endif; ?>
            <a href="logout.php" class="btn-logout">Logout (<?= htmlspecialchars($_SESSION['name']) ?>)</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php" class="btn-register">Register</a>
        <?php endif; ?>
    </div>
</nav>
