<?php
session_start();
if (!isset($_SESSION['email'])) {
  header('Location: index1.php');
  exit();
}

$db = require __DIR__ . "/../config/config.php";

/** Logged-in admin user (for top-right profile + right panel) */
$userId = $_SESSION['id'] ?? null;

if ($userId) {
  $stmt = $db->prepare("SELECT id, name, email, phone, role, profile_image FROM user WHERE id = ?");
  $stmt->bind_param("i", $userId);
} else {
  $email = $_SESSION['email'];
  $stmt = $db->prepare("SELECT id, name, email, phone, role, profile_image FROM user WHERE email = ?");
  $stmt->bind_param("s", $email);
}
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
  session_destroy();
  header("Location: index1.php");
  exit();
}

$role = strtolower(trim($user['role'] ?? ''));

// adjust to match your DB role values
$isStaff = in_array($role, ['staff', 'employee'], true);
$isAdmin = in_array($role, ['admin'], true);

$dashboardBase = $isStaff ? 'staff_dashboard.php' : 'dashboard.php';
$badgeLabel    = $isStaff ? 'Staff' : 'Admin';
/** CSRF (if you later add actions like update status) */
if (empty($_SESSION['csrf'])) {
  $_SESSION['csrf'] = bin2hex(random_bytes(16));
}

/** Inputs */
$orderId   = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$return_to = $_GET['return_to'] ?? ($dashboardBase . '?tab=orders');


if ($orderId <= 0) {
  header("Location: " . $return_to);
  exit();
}

/** Load order + customer */
$orderStmt = $db->prepare("
  SELECT
    o.id, o.order_code, o.total, o.status, o.created_at,
    o.payment_status, o.payment_method, o.paid_at,
    u.id AS customer_id, u.name AS customer_name, u.email AS customer_email,
    u.phone AS customer_phone, u.address AS customer_address
  FROM orders o
  JOIN users u ON u.id = o.user_id
  WHERE o.id = ?
  LIMIT 1
");
$orderStmt->bind_param("i", $orderId);
$orderStmt->execute();
$order = $orderStmt->get_result()->fetch_assoc();

if (!$order) {
  header("Location: " . $return_to);
  exit();
}

/** Load order items */
$itemStmt = $db->prepare("
  SELECT id, menu_name, price, quantity, temp, milk, syrup, addons
  FROM order_items
  WHERE order_id = ?
  ORDER BY id ASC
");
$itemStmt->bind_param("i", $orderId);
$itemStmt->execute();
$itemRes = $itemStmt->get_result();

$items = [];
while ($row = $itemRes->fetch_assoc()) $items[] = $row;

/** Right-side latest feedback (same as your dashboard) */
$latestFeedback = [];
$latestFbStmt = $db->prepare("
  SELECT fm.id, fm.rating, fm.comment, fm.created_at,
         u.name, u.profile_image
  FROM feedback_message fm
  JOIN users u ON u.id = fm.user_id
  ORDER BY fm.created_at DESC
  LIMIT 6
");
$latestFbStmt->execute();
$latestFeedbackRes = $latestFbStmt->get_result();
while ($fb = $latestFeedbackRes->fetch_assoc()) $latestFeedback[] = $fb;

/** Helper badges */
function badgeClass($value, $type = 'status') {
  $v = strtoupper(trim((string)$value));

  if ($type === 'payment') {
    return ($v === 'PAID') ? 'delivered' : 'pending';
  }

  // order status
  if ($v === 'READY') return 'delivered';
  if ($v === 'CANCELLED') return 'cancelled';
  return 'pending';
}

function statusIcon($status) {
  $s = strtoupper(trim((string)$status));
  if ($s === 'READY') return 'check_circle';
  if ($s === 'PREPARING') return 'restaurant';
  if ($s === 'CONFIRMED') return 'schedule';
  if ($s === 'CANCELLED') return 'cancel';
  return 'info';
}

function paymentIcon($payment) {
  $p = strtoupper(trim((string)$payment));
  if ($p === 'PAID') return 'verified';
  return 'credit_card_off';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order Details</title>
  <link href="https://fonts.googleapis.com/icon?family=Material+Symbols+Sharp" rel="stylesheet">
  <link rel="stylesheet" href="../css/style_db.css">

  <!-- Small extra styling to match "Edit Customer" card feel -->
  <style>
    .page-head{
      display:flex; align-items:center; justify-content:space-between; gap:12px;
      margin-bottom: 14px;
    }
    .back-btn{
      display:inline-flex; align-items:center; gap:8px;
      padding:10px 14px; border-radius:12px;
      border:1px solid rgba(255,255,255,.12);
      color:#fff; text-decoration:none;
      background: rgba(255,255,255,.03);
    }
    .back-btn:hover{ background: rgba(255,255,255,.06); }

    .panel{
      background: rgba(255,255,255,.04);
      border: 1px solid rgba(255,255,255,.08);
      border-radius: 18px;
      padding: 18px;
      box-shadow: 0 18px 40px rgba(0,0,0,.22);
    }

    .grid-2{
      display:grid;
      grid-template-columns: 1.35fr 0.85fr;
      gap: 18px;
      align-items: start;
    }

    .field-grid{
      display:grid;
      grid-template-columns: 1fr 1fr;
      gap: 14px;
      margin-top: 10px;
    }

    .field{ display:flex; flex-direction:column; gap:6px; }
    .field label{ font-size:12px; opacity:.75; }
    .field .box{
      padding: 12px 12px;
      border-radius: 12px;
      border: 1px solid rgba(255,255,255,.10);
      background: rgba(0,0,0,.22);
      color:#fff;
      min-height: 42px;
      display:flex; align-items:center; justify-content:space-between;
      gap:10px;
    }
    .mono{ font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace; font-size: 12px; opacity:.9; }

    .items-wrap{ margin-top: 18px; }
    .items-wrap h2{ margin-bottom: 10px; }

    .mini-summary{
      display:flex; flex-direction:column; gap:10px;
    }
    .mini-row{
      display:flex; align-items:center; justify-content:space-between;
      padding: 10px 12px;
      border-radius: 12px;
      border:1px solid rgba(255,255,255,.10);
      background: rgba(0,0,0,.20);
    }
    .mini-row b{ font-size: 16px; }

    /* highlight table rows on hover similar feel */
    #itemsTable tbody tr:hover { background: rgba(255,255,255,.03); }

    /* === Cognitive badges (status + payment) === */
    .badges {
      display:flex;
      gap:10px;
      align-items:center;
      flex-wrap:wrap;
    }

    .badge-pill{
      display:inline-flex;
      align-items:center;
      gap:8px;
      padding:8px 12px;
      border-radius:999px;
      font-size:12px;
      font-weight:600;
      letter-spacing:.2px;
      border:1px solid rgba(255,255,255,.10);
      background: rgba(255,255,255,.04);
      box-shadow: 0 10px 22px rgba(0,0,0,.18);
    }

    .badge-pill .material-symbols-sharp{
      font-size:18px;
      opacity:.95;
    }

    .badge-pill .label{
      opacity:.85;
      font-weight:500;
    }

    .badge-pill .value{
      font-weight:700;
    }

    .badge-pill.pending{
      background: rgba(255,187,85,.10);
      border-color: rgba(255,187,85,.25);
    }
    .badge-pill.pending .value{ color: #ffbb55; }

    .badge-pill.delivered{
      background: rgba(65,241,182,.10);
      border-color: rgba(65,241,182,.25);
    }
    .badge-pill.delivered .value{ color: #41f1b6; }

    .badge-pill.cancelled{
      background: rgba(255,119,130,.10);
      border-color: rgba(255,119,130,.25);
    }
    .badge-pill.cancelled .value{ color: #ff7782; }

    /* subtle "focus ring" effect */
    .badge-pill:focus-within,
    .badge-pill:hover{
      transform: translateY(-1px);
      box-shadow: 0 14px 30px rgba(0,0,0,.25);
    }

  </style>
</head>

<body>
<div class="container">

  <!-- ASIDE (same as dashboard) -->
  <aside>
    <div class="top">
      <div class="logo">
        <img src="../assets/img/puckslogo.jpg" alt="">
        <h2>PUCKS COFFEE <span class="danger"><?= htmlspecialchars($badgeLabel) ?></span></h2>
      </div>
      <div class="close" id="close-btn">
        <span class="material-symbols-sharp">close</span>
      </div>
    </div>

    <div class="sidebar">
      <a href="<?= $dashboardBase ?>?tab=dashboard">
        <span class="material-symbols-sharp">grid_view</span><h3>Dashboard</h3>
      </a>
      <a href="<?= $dashboardBase ?>?tab=customers">
        <span class="material-symbols-sharp">person</span><h3>Customers</h3>
      </a>
      <a href="<?= $dashboardBase ?>?tab=orders" class="active">
        <span class="material-symbols-sharp">receipt_long</span><h3>Orders</h3>
      </a>
      <a href="<?= $dashboardBase ?>?tab=staff">
        <span class="material-symbols-sharp">person_3</span><h3>Staff</h3>
      </a>
      <a href="<?= $dashboardBase ?>?tab=reports">
        <span class="material-symbols-sharp">report_gmailerrorred</span><h3>Reports</h3>
      </a>
      <a href="<?= $dashboardBase ?>?tab=feedback">
        <span class="material-symbols-sharp">reviews</span><h3>Feedback</h3>
      </a>
      <a href="<?= $dashboardBase ?>?tab=profile">
        <span class="material-symbols-sharp">account_circle</span><h3>Profile</h3>
      </a>
      <a href="logout1.php" id="logout-link">
        <span class="material-symbols-sharp">logout</span><h3>Logout</h3>
      </a>
    </div>
  </aside>

  <!-- MAIN -->
  <main>
    <div class="page-head">
      <div>
        <h1 style="margin:0;">Order Details</h1>
        <small class="text-muted">View order, items, and payment summary.</small>
      </div>

      <a class="back-btn" href="<?= htmlspecialchars($return_to) ?>">
        <span class="material-symbols-sharp">arrow_back</span>
        Back
      </a>
    </div>

    <!-- Top panels (like edit-customer form card + summary card) -->
    <div class="grid-2">
      <div class="panel">
        <div style="display:flex; justify-content:space-between; gap:12px; align-items:flex-start;">
          <div>
            <div style="font-size:13px; opacity:.75;">Order</div>
            <div style="display:flex; gap:10px; align-items:baseline;">
              <h2 style="margin:0;">#<?= (int)$order['id'] ?></h2>
              <span class="mono">(<?= htmlspecialchars($order['order_code']) ?>)</span>
            </div>
          </div>
          <div class="badges">
  <div class="badge-pill <?= badgeClass($order['status'], 'status') ?>">
    <span class="material-symbols-sharp"><?= statusIcon($order['status']) ?></span>
    <span class="label">Order</span>
    <span class="value"><?= htmlspecialchars($order['status']) ?></span>
  </div>

  <div class="badge-pill <?= badgeClass($order['payment_status'], 'payment') ?>">
    <span class="material-symbols-sharp"><?= paymentIcon($order['payment_status']) ?></span>
    <span class="label">Payment</span>
    <span class="value"><?= htmlspecialchars(strtoupper(trim($order['payment_status'] ?? 'UNPAID'))) ?></span>
  </div>
</div>
        </div>

        <div class="field-grid">
          <div class="field">
            <label>Created At</label>
            <div class="box"><?= htmlspecialchars($order['created_at']) ?></div>
          </div>

          <div class="field">
            <label>Paid At</label>
            <div class="box"><?= !empty($order['paid_at']) ? htmlspecialchars($order['paid_at']) : '-' ?></div>
          </div>

          <div class="field">
            <label>Payment Method</label>
            <div class="box"><?= !empty($order['payment_method']) ? htmlspecialchars($order['payment_method']) : '-' ?></div>
          </div>

          <div class="field">
            <label>Customer</label>
            <div class="box" style="justify-content:flex-start; flex-direction:column; align-items:flex-start;">
              <b><?= htmlspecialchars($order['customer_name']) ?></b>
              <small class="text-muted"><?= htmlspecialchars($order['customer_email']) ?></small>
            </div>
          </div>

          <div class="field">
            <label>Phone</label>
            <div class="box"><?= htmlspecialchars($order['customer_phone'] ?? '-') ?></div>
          </div>

          
        </div>
      </div>

      <div class="panel">
        <h2 style="margin:0 0 8px 0;">Summary</h2>
        <small class="text-muted">Breakdown of item totals</small>

        <?php
          $itemsCount = 0;
          $itemsSubtotal = 0.0;
          foreach ($items as $it) {
            $q = (int)$it['quantity'];
            $p = (float)$it['price'];
            $itemsCount += $q;
            $itemsSubtotal += ($q * $p);
          }
        ?>

        <div class="mini-summary" style="margin-top:14px;">
          <div class="mini-row">
            <span class="text-muted">Order Total</span>
            <b>RM<?= number_format((float)$order['total'], 2) ?></b>
          </div>
          <div style="margin-top:8px; font-size:13px; opacity:.8;">
            Items: <b><?= (int)$itemsCount ?></b>
          </div>
        </div>
      </div>
    </div>

    <!-- Items table (like your tables, inside a card) -->
    <div class="panel items-wrap">
      <h2 style="margin:0;">Items</h2>
      <small class="text-muted">All items in this order</small>

      <div style="margin-top:12px;">
        <table id="itemsTable" style="width:100%; border-collapse:collapse;">
          <thead>
            <tr>
              <th style="text-align:left;">Menu</th>
              <th style="text-align:left;">Options</th>
              <th style="text-align:left;">Qty</th>
              <th style="text-align:left;">Price</th>
              <th style="text-align:left;">Subtotal</th>
            </tr>
          </thead>
          <tbody>
          <?php if (empty($items)): ?>
            <tr>
              <td colspan="5" style="padding:14px; text-align:center;">No items found.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($items as $it): ?>
              <?php
                $qty = (int)$it['quantity'];
                $price = (float)$it['price'];
                $sub = $qty * $price;

                $optParts = [];
                if (!empty($it['temp']))  $optParts[] = "Temp: " . $it['temp'];
                if (!empty($it['milk']))  $optParts[] = "Milk: " . $it['milk'];
                if (!empty($it['syrup'])) $optParts[] = "Syrup: " . $it['syrup'];
                if (!empty($it['addons']))$optParts[] = "Add-ons: " . $it['addons'];
                $opts = !empty($optParts)
                  ? implode(" | ", array_map('htmlspecialchars', $optParts))
                  : "-";
              ?>
              <tr>
                <td style="padding:10px 6px;"><b><?= htmlspecialchars($it['menu_name']) ?></b></td>
                <td style="padding:10px 6px;"><small class="text-muted"><?= $opts ?></small></td>
                <td style="padding:10px 6px;"><?= $qty ?></td>
                <td style="padding:10px 6px;">RM<?= number_format($price, 2) ?></td>
                <td style="padding:10px 6px;">RM<?= number_format($sub, 2) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </main>

  <!-- RIGHT (same as dashboard/edit page) -->
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
                <p>
                  <b><?= htmlspecialchars($fb['name']) ?></b>
                  rated <?= (int)$fb['rating'] ?> star<?= ((int)$fb['rating'] !== 1) ? 's' : '' ?>
                </p>
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
