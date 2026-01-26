<?php
session_start();
if (!isset($_SESSION['email'])) { header("Location: index1.php"); exit(); }

$db = require __DIR__ . "/../config/config.php";

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { die("Invalid customer id"); }

$stmt = $db->prepare("SELECT id, name, phone, email, address, verify_status FROM users WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$customer = $stmt->get_result()->fetch_assoc();

if (!$customer) { die("Customer not found"); }

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit Customer</title>
    <link rel="stylesheet" href="../css/style_db.css">
</head>
<body style="padding:20px;">
    <h1>Edit Customer</h1>

    <form method="POST" action="update-customer.php" style="max-width:560px;">
        <input type="hidden" name="id" value="<?= (int)$customer['id'] ?>">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">

        <label>Name</label>
        <input type="text" name="name" value="<?= htmlspecialchars($customer['name']) ?>" required>

        <label>Phone</label>
        <input type="text" name="phone" value="<?= htmlspecialchars($customer['phone']) ?>" required>

        <label>Email (read-only)</label>
        <input type="email" value="<?= htmlspecialchars($customer['email']) ?>" readonly>

        <label>Address</label>
        <input type="text" name="address" value="<?= htmlspecialchars($customer['address']) ?>" required>

        <label>Verify Status</label>
        <select name="verify_status" required>
            <option value="0" <?= ((int)$customer['verify_status'] === 0) ? 'selected' : '' ?>>Not Verified</option>
            <option value="1" <?= ((int)$customer['verify_status'] === 1) ? 'selected' : '' ?>>Verified</option>
        </select>

        <button type="submit" class="btn-primary" style="margin-top:12px;">Save</button>
        <a href="staff_dashboard.php#customers" style="margin-left:10px;">Back</a>
    </form>
</body>
</html>
