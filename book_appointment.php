<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: login.php"); exit;
}
require 'database_config.php';

$msg = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stylist_id = $_POST['stylist_id'];
    $service_id = $_POST['service_id'];
    $date       = $_POST['appointment_date'];
    $time       = $_POST['appointment_time'];
    $notes      = trim($_POST['notes']);

    // Check for conflict
    $conflict = $pdo->prepare("SELECT id FROM appointments WHERE stylist_id=? AND appointment_date=? AND appointment_time=? AND status != 'cancelled'");
    $conflict->execute([$stylist_id, $date, $time]);
    if ($conflict->fetch()) {
        $error = "That stylist is already booked at that time. Please choose another slot.";
    } else {
        $pdo->prepare("INSERT INTO appointments (client_id,stylist_id,service_id,appointment_date,appointment_time,notes) VALUES (?,?,?,?,?,?)")
            ->execute([$_SESSION['user_id'], $stylist_id, $service_id, $date, $time, $notes]);
        $msg = "Appointment booked successfully!";
    }
}

$stylists = $pdo->query("SELECT id, name FROM users WHERE role='stylist' ORDER BY name")->fetchAll();
$services = $pdo->query("SELECT * FROM services ORDER BY name")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Appointment - BeautyBoss</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container">
    <div class="page-title">Book an Appointment</div>

    <?php if ($msg): ?>
        <div class="alert alert-success"><?= $msg ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-error"><?= $error ?></div>
    <?php endif; ?>

    <div class="form-card" style="max-width:600px;margin:0;">
        <form method="POST">
            <div class="form-group">
                <label>Select Service</label>
                <select name="service_id" required>
                    <option value="">-- Choose a service --</option>
                    <?php foreach ($services as $s): ?>
                    <option value="<?= $s['id'] ?>">
                        <?= htmlspecialchars($s['name']) ?> - &#8369;<?= number_format($s['price'],2) ?> (<?= $s['duration'] ?> mins)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Select Stylist</label>
                <select name="stylist_id" required>
                    <option value="">-- Choose a stylist --</option>
                    <?php foreach ($stylists as $s): ?>
                    <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Date</label>
                <input type="date" name="appointment_date" min="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="form-group">
                <label>Time</label>
                <input type="time" name="appointment_time" required>
            </div>
            <div class="form-group">
                <label>Notes (optional)</label>
                <textarea name="notes" rows="3" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px;"></textarea>
            </div>
            <button type="submit" class="btn">Book Appointment</button>
        </form>
    </div>
</div>
</body>
</html>
