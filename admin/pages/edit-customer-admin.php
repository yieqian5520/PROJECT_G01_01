<?php
session_start();
if (!isset($_SESSION['email'])) { header("Location: index1.php"); exit(); }

$db = require __DIR__ . "/../config/config.php";

/* Load admin user (same as dashboard.php) */
$userId = $_SESSION['id'] ?? null;

if ($userId) {
  $stmt = $db->prepare("SELECT id, name, email, phone, role, profile_image FROM user WHERE id=?");
  $stmt->bind_param("i", $userId);
} else {
  $email = $_SESSION['email'];
  $stmt = $db->prepare("SELECT id, name, email, phone, role, profile_image FROM user WHERE email=?");
  $stmt->bind_param("s", $email);
}

$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
if (!$user) { session_destroy(); header("Location: index1.php"); exit(); }

if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));

/* Load customer */
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) die("Invalid customer id");

$cStmt = $db->prepare("SELECT id, name, phone, email, address, verify_status FROM users WHERE id=?");
$cStmt->bind_param("i", $id);
$cStmt->execute();
$customer = $cStmt->get_result()->fetch_assoc();
if (!$customer) die("Customer not found");

$isVerified = ((int)$customer['verify_status'] === 1);

/* Right panel feedback (same as dashboard.php) */
$latestFeedback = [];
$latestFbStmt = $db->prepare("
  SELECT fm.id, fm.rating, fm.comment, fm.created_at, u.name, u.profile_image
  FROM feedback_message fm
  JOIN users u ON u.id = fm.user_id
  ORDER BY fm.created_at DESC
  LIMIT 6
");
$latestFbStmt->execute();
$res = $latestFbStmt->get_result();
while ($fb = $res->fetch_assoc()) $latestFeedback[] = $fb;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Customer</title>
  <link href="https://fonts.googleapis.com/icon?family=Material+Symbols+Sharp" rel="stylesheet">
  <link rel="stylesheet" href="../css/style_db.css">
  <style>
    :root{ --color-dark-variant:#677483; }

    .edit-card{
      background: var(--color-white);
      padding: var(--card-padding);
      border-radius: var(--card-border-radius);
      box-shadow: var(--box-shadow);
      transition: all 300ms ease;
      margin-top: 1rem;
      max-width: 900px;
    }
    .edit-card:hover{ box-shadow:none; }

    .edit-head{
      display:flex;
      align-items:flex-start;
      justify-content:space-between;
      gap: 1rem;
      flex-wrap:wrap;
      margin-top: .6rem;
    }
    .sub{ margin-top:.35rem; color: var(--color-info-dark); font-weight:500; }

    .pill{
      display:inline-flex; align-items:center; gap:.45rem;
      padding:.5rem .85rem; border-radius: var(--border-radius-2);
      background: var(--color-light); font-weight:700; font-size:.85rem; white-space:nowrap;
    }
    .pill.success{ color: var(--color-success); }
    .pill.danger{ color: var(--color-danger); }

    .divider{ height:1px; background: var(--color-light); margin: 1rem 0 1.2rem; }

    .form-row{ display:flex; gap:1.5rem; margin-bottom:1.2rem; flex-wrap:wrap; }
    .form-group{ flex:1; min-width:260px; }

    label{ display:block; margin-bottom:.5rem; font-weight:600; color: var(--color-dark); }
    input, select{
      width:100%; padding:.85rem;
      border:1px solid var(--color-light);
      border-radius: var(--border-radius-1);
      font-size:.9rem; color: var(--color-dark);
      background: var(--color-background);
      transition: all 300ms ease;
    }
    input:focus, select:focus{
      border-color: var(--color-primary);
      box-shadow: 0 0 0 3px rgba(115,128,236,0.12);
    }
    input[readonly]{ background: var(--color-light); cursor:not-allowed; }

    .hint{ margin-top:.4rem; font-size:.78rem; color: var(--color-info-dark); display:block; }

    .actions{ display:flex; justify-content:flex-end; gap:.8rem; flex-wrap:wrap; }

    .btn{
      padding:.85rem 1.4rem; border-radius: var(--border-radius-1);
      font-weight:600; cursor:pointer; transition: all 300ms ease;
      display:inline-flex; align-items:center; justify-content:center;
      text-decoration:none;
    }
    .btn-primary{
      background: linear-gradient(135deg, var(--color-primary), var(--color-primary-variant));
      color: var(--color-white);
      box-shadow: 0 4px 8px rgba(0,0,0,0.10);
    }
    .btn-primary:hover{
      background: linear-gradient(135deg, var(--color-primary-variant), var(--color-primary));
      transform: translateY(-2px);
      box-shadow: 0 6px 12px rgba(0,0,0,0.15);
    }
    .btn-outline{
      background: transparent;
      border: 1px solid var(--color-light);
      color: var(--color-dark);
    }
    .btn-outline:hover{
      color: var(--color-primary);
      border-color: var(--color-primary);
      transform: translateY(-1px);
    }

    /* Flash alerts already exist in your css (.alert.success/.alert.error) */
  </style>
</head>

<body>
<div class="container">

  <!-- ASIDE (admin) -->
  <aside>
    <div class="top">
      <div class="logo">
        <img src="../assets/img/puckslogo.jpg" alt="">
        <h2>PUCKS COFFEE <span class="danger">Admin</span></h2>
      </div>
      <div class="close" id="close-btn">
        <span class="material-symbols-sharp">close</span>
      </div>
    </div>

    <div class="sidebar">
      <a href="dashboard.php?tab=dashboard"><span class="material-symbols-sharp">grid_view</span><h3>Dashboard</h3></a>
      <a class="active" href="dashboard.php?tab=customers"><span class="material-symbols-sharp">person</span><h3>Customers</h3></a>
      <a href="dashboard.php?tab=orders"><span class="material-symbols-sharp">receipt_long</span><h3>Orders</h3></a>
      <a href="dashboard.php?tab=staff"><span class="material-symbols-sharp">person_3</span><h3>Staff</h3></a>
      <a href="dashboard.php?tab=reports"><span class="material-symbols-sharp">report_gmailerrorred</span><h3>Reports</h3></a>
      <a href="dashboard.php?tab=feedback"><span class="material-symbols-sharp">reviews</span><h3>Feedback</h3></a>
      <a href="dashboard.php?tab=profile"><span class="material-symbols-sharp">account_circle</span><h3>Profile</h3></a>
      <a href="logout1.php" id="logout-link"><span class="material-symbols-sharp">logout</span><h3>Logout</h3></a>
    </div>
  </aside>

  <!-- MAIN -->
  <main>
    <div class="edit-head">
      <div>
        <h1>Edit Customer</h1>
        <p class="sub">Admin can update details and verification status.</p>
      </div>
      <span class="pill <?= $isVerified ? 'success' : 'danger' ?>">
        <?= $isVerified ? 'Verified' : 'Not Verified' ?> • ID #<?= (int)$customer['id'] ?>
      </span>
    </div>

    <?php if (!empty($_SESSION['flash_success'])): ?>
      <div class="alert success" style="margin-top:12px;">
        <span class="material-symbols-sharp">check_circle</span>
        <?= htmlspecialchars($_SESSION['flash_success']) ?>
      </div>
      <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['flash_error'])): ?>
      <div class="alert error" style="margin-top:12px;">
        <span class="material-symbols-sharp">error</span>
        <?= htmlspecialchars($_SESSION['flash_error']) ?>
      </div>
      <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

    <div class="edit-card">
      <form method="POST" action="update-customer-admin.php" autocomplete="off">
        <input type="hidden" name="id" value="<?= (int)$customer['id'] ?>">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">

        <div class="form-row">
          <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" value="<?= htmlspecialchars($customer['name']) ?>" required>
          </div>

          <div class="form-group">
            <label>Phone</label>
            <input type="text" name="phone" value="<?= htmlspecialchars($customer['phone']) ?>" required>
            <small class="hint">Example: 0123456789</small>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Email (read-only)</label>
            <input type="email" value="<?= htmlspecialchars($customer['email']) ?>" readonly>
            <small class="hint">Email cannot be changed.</small>
          </div>

          <div class="form-group">
            <label>Verify Status</label>
            <select name="verify_status" required>
              <option value="0" <?= ((int)$customer['verify_status'] === 0) ? 'selected' : '' ?>>Not Verified</option>
              <option value="1" <?= ((int)$customer['verify_status'] === 1) ? 'selected' : '' ?>>Verified</option>
            </select>
            <small class="hint">Set Verified only after confirmation.</small>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group" style="min-width:100%;">
            <label>Address</label>
            <input type="text" name="address" value="<?= htmlspecialchars($customer['address']) ?>" required>
          </div>
        </div>

        <div class="divider"></div>

        <div class="actions">
          <a class="btn btn-outline" href="dashboard.php?tab=customers">Back</a>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </main>

  <!-- RIGHT (admin) -->
  <div class="right">
    <div class="top">
      <div class="theme-toggler">
        <span class="material-symbols-sharp active">light_mode</span>
        <span class="material-symbols-sharp">dark_mode</span>
      </div>
      <div class="profile">
        <div class="info">
          <p>Hey, <?= htmlspecialchars($user['name']) ?></p>
          <small class="text-muted"><?= htmlspecialchars($user['role']) ?></small>
        </div>
        <div class="profile-photo">
          <img src="<?= htmlspecialchars($user['profile_image'] ?: '../assets/img/profile-1.jpg') ?>?t=<?= time() ?>" alt="">
        </div>
      </div>
    </div>

    <div class="feedback">
      <div style="display:flex; justify-content:space-between; align-items:center;">
        <h2 style="margin:0;">Feedback</h2>
        <a href="dashboard.php?tab=feedback" class="primary" style="font-size:14px;">See all</a>
      </div>

      <div class="fback" style="margin-top:10px;">
        <?php if (empty($latestFeedback)): ?>
          <p class="text-muted" style="padding:10px 0;">No feedback yet.</p>
        <?php else: ?>
          <?php foreach ($latestFeedback as $fb): ?>
            <div class="fb">
              <div class="profile-photo">
                <img src="<?= htmlspecialchars($fb['profile_image'] ?: '../assets/img/profile-1.jpg') ?>" alt="">
              </div>
              <div class="message">
                <p><b><?= htmlspecialchars($fb['name']) ?></b> rated <?= (int)$fb['rating'] ?> star<?= ((int)$fb['rating'] !== 1) ? 's' : '' ?></p>
                <small class="text-muted"><?= htmlspecialchars($fb['comment']) ?></small><br>
                <small class="text-muted"><?= htmlspecialchars($fb['created_at']) ?></small>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>

</div>
<script src="../js/index.js"></script>
</body>
</html>
