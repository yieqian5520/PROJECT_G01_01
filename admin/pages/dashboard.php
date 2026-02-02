<?php

session_start();
if(!isset($_SESSION['email'])) {
    header('Location: index1.php');
    exit();
}

$db = require __DIR__ . "/../config/config.php";

$userId = $_SESSION['id'] ?? null;

if ($userId) {
    $stmt = $db->prepare(
        "SELECT id, name, email, phone, role, profile_image
         FROM user 
         WHERE id = ?"
    );
    $stmt->bind_param("i", $userId);
} else {
    $email = $_SESSION['email'];
    $stmt = $db->prepare(
        "SELECT id, name, email, phone, role, profile_image
         FROM user 
         WHERE email = ?"
    );
    $stmt->bind_param("s", $email);
}

$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
  session_destroy();
  header("Location: index1.php");
  exit();
}

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
}

// --- Date picker support (Dashboard) ---
$selectedDate = $_GET['date'] ?? date('Y-m-d');

// Basic validation (prevents weird values)
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $selectedDate)) {
    $selectedDate = date('Y-m-d');
}

$todayTotals = [
  'total_sales' => 0,
  'total_expenses' => 0,
  'total_income' => 0
];

$todayStmt = $db->prepare("
  SELECT
    COALESCE(SUM(o.total),0) AS total_sales,

    COALESCE(SUM(GREATEST(oi.price - 4, 0) * oi.quantity),0) AS total_expenses,

    COALESCE(
      (SUM(o.total) - SUM(GREATEST(oi.price - 4, 0) * oi.quantity)),
      0
    ) AS total_income

  FROM orders o
  JOIN order_items oi ON oi.order_id = o.id
  WHERE DATE(o.created_at) = ?
  AND UPPER(TRIM(o.payment_status)) = 'PAID'
");

$todayStmt->bind_param("s", $selectedDate);
$todayStmt->execute();
$todayTotals = $todayStmt->get_result()->fetch_assoc();

$sales    = (float)($todayTotals['total_sales'] ?? 0);
$expenses = (float)($todayTotals['total_expenses'] ?? 0);
$income   = (float)($todayTotals['total_income'] ?? 0);

/**
 * DAILY TARGETS (edit these to your goals)
 * You can also load these from DB later if you want.
 */
$salesTarget    = 700;  // RM per day (example)
$expensesLimit  = 280;  // RM max expenses per day (example)
$incomeTarget   = 420;  // RM per day (example)

// Percent helper
$calcPct = function(float $value, float $target): int {
    if ($target <= 0) return 0;
    $pct = (int) round(($value / $target) * 100);
    return max(0, min(100, $pct));
};

$salesPct    = $calcPct($sales, $salesTarget);
$expensesPct = $calcPct($expenses, $expensesLimit);
$incomePct   = $calcPct($income, $incomeTarget);

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
while ($fb = $latestFeedbackRes->fetch_assoc()) {
    $latestFeedback[] = $fb;
}

$allowedTabs = ['dashboard','customers','orders','staff','feedback','reports','profile'];

$activeTab = $_GET['tab'] ?? 'dashboard';
if (!in_array($activeTab, $allowedTabs, true)) {
    $activeTab = 'dashboard';
}

$focusOrderId = isset($_GET['focus']) ? (int)$_GET['focus'] : 0;
// If user is searching, force customers tab
if (isset($_GET['search']) && trim($_GET['search']) !== '') {
    $activeTab = 'customers';
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Symbols+Sharp"
      rel="stylesheet">
    <link rel="stylesheet" href="../css/style_db.css">
</head>
<body>
    <div class="container">
        
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
                <a href="dashboard.php?tab=dashboard">
                    <span class="material-symbols-sharp">grid_view</span>
                    <h3>Dashboard</h3>
                </a>

                <a href="dashboard.php?tab=customers">
                    <span class="material-symbols-sharp">person</span>
                    <h3>Customers</h3>
                </a>

                <a href="dashboard.php?tab=orders">
                    <span class="material-symbols-sharp">receipt_long</span>
                    <h3>Orders</h3>
                </a>

                <a href="dashboard.php?tab=staff">
                    <span class="material-symbols-sharp">person_3</span>
                    <h3>Staff</h3>
                </a>

                <a href="dashboard.php?tab=reports">
                    <span class="material-symbols-sharp">report_gmailerrorred</span>
                    <h3>Reports</h3>
                </a>

                <a href="dashboard.php?tab=feedback">
                    <span class="material-symbols-sharp">reviews</span>
                    <h3>Feedback</h3>
                </a>

                <a href="dashboard.php?tab=profile">
                    <span class="material-symbols-sharp">account_circle</span>
                    <h3>Profile</h3>
                </a>

                <a href="logout1.php" id="logout-link">
                    <span class="material-symbols-sharp">logout</span>
                    <h3>Logout</h3>
                </a>
            </div>

        </aside>
        <!-- END OF ASIDE -->

        <main>
            <div id="dashboard" class="tab-content <?= $activeTab === 'dashboard' ? 'active' : '' ?>">
                <h1>Dashboard</h1>
                <div class="date">
                <form method="GET">
                    <input type="hidden" name="tab" value="dashboard">
                    <input
                    type="date"
                    name="date"
                    value="<?= htmlspecialchars($selectedDate) ?>"
                    onchange="this.form.submit()"
                    >
                </form>
                </div>

                <div class="insights">
                    <div class="sales">
                        <span class="material-symbols-sharp">analytics</span>
                        <div class="middle">
                            <div class="left">
                                <h3>Total Sales</h3>
                                <h1>RM<?= number_format((float)$todayTotals['total_sales'], 2) ?></h1>
                                <small class="text-muted">Target: RM<?= number_format($salesTarget, 2) ?></small>
                            </div>
                            <div class="progress" style="--p: <?= $salesPct ?>;">
                                <svg viewBox="0 0 80 80">
                                    <circle class="bg"  cx="40" cy="40" r="32"></circle>
                                    <circle class="bar" cx="40" cy="40" r="32"></circle>
                                </svg>
                                <div class="number">
                                    <p><?= $salesPct ?>%</p>
                                </div>
                            </div>
                        </div>
                        <small class="text-muted">Last 24 Hours</small>
                    </div>
                    <!-- END OF SALES -->
                    <div class="expenses">
                        <span class="material-symbols-sharp">bar_chart</span>
                        <div class="middle">
                            <div class="left">
                                <h3>Total Expenses</h3>
                                <h1>RM<?= number_format((float)$todayTotals['total_expenses'], 2) ?></h1>
                                <small class="text-muted">Limit: RM<?= number_format($expensesLimit, 2) ?></small>
                            </div>
                            <div class="progress" style="--p: <?= $expensesPct ?>;">
                                <svg viewBox="0 0 80 80">
                                    <circle class="bg"  cx="40" cy="40" r="32"></circle>
                                    <circle class="bar" cx="40" cy="40" r="32"></circle>
                                </svg>
                                <div class="number">
                                    <p><?= $expensesPct ?>%</p>
                                </div>
                            </div>
                        </div>
                        <small class="text-muted">Last 24 Hours</small>
                    </div>
                    <!-- END OF EXPENSES -->
                    <div class="income">
                        <span class="material-symbols-sharp">trending_up</span>
                        <div class="middle">
                            <div class="left">
                                <h3>Total Income</h3>
                                <h1>RM<?= number_format((float)$todayTotals['total_income'], 2) ?></h1>
                                <small class="text-muted">Target: RM<?= number_format($incomeTarget, 2) ?></small>
                            </div>
                            <div class="progress" style="--p: <?= $incomePct ?>;">
                                <svg viewBox="0 0 80 80">
                                    <circle class="bg"  cx="40" cy="40" r="32"></circle>
                                    <circle class="bar" cx="40" cy="40" r="32"></circle>
                                </svg>
                                <div class="number">
                                    <p><?= $incomePct ?>%</p>
                                </div>
                            </div>
                        </div>
                        <small class="text-muted">Last 24 Hours</small>
                    </div>
                    <!-- END OF INCOME -->
                </div>
                <!-- END OF INSIGHTS -->
                <?php
                $recentOrders = [];

                $recentStmt = $db->prepare("
                    SELECT 
                        o.id,
                        o.order_code,
                        o.total,
                        o.status,
                        o.created_at,
                        u.name AS customer_name
                    FROM orders o
                    JOIN users u ON u.id = o.user_id
                    ORDER BY o.created_at DESC
                    LIMIT 5
                ");

                $recentStmt->execute();
                $recentRes = $recentStmt->get_result();
                while ($r = $recentRes->fetch_assoc()) {
                    $recentOrders[] = $r;
                }
                ?>
                <div class="recent-orders">
                    <h2>Recent Orders</h2>
                    <table>
                        <thead>
                        <tr>
                            <th>Customer Name</th>
                            <th>Order Code</th>
                            <th>Total</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($recentOrders)): ?>
                            <tr>
                                <td colspan="6" style="text-align:center; padding:16px;">No recent orders.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentOrders as $ro): ?>
                                <?php
                                    $oid = (int)$ro['id'];
                                    $status = trim($ro['status'] ?? 'Confirmed');

                                    // map status to your CSS class names (edit if needed)
                                    $statusClass = 'pending';
                                    if (strcasecmp($status, 'Ready') === 0) $statusClass = 'delivered';
                                    if (strcasecmp($status, 'Cancelled') === 0) $statusClass = 'cancelled';
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($ro['customer_name']) ?></td>
                                    <td><small class="text-muted"><?= htmlspecialchars($ro['order_code']) ?></small></td>
                                    <td>RM<?= number_format((float)$ro['total'], 2) ?></td>
                                    <td><?= htmlspecialchars($ro['created_at']) ?></td>
                                    <td><span class="status <?= $statusClass ?>"><?= htmlspecialchars($status) ?></span></td>
                                    
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>

                    </table>
                    <a href="dashboard.php?tab=orders">Show All</a>
                </div>
            </div>
            <div id="customers" class="tab-content <?= $activeTab === 'customers' ? 'active' : '' ?>">
                <h1>Customers</h1>

                <?php
                // Simple search
                $search = trim($_GET['search'] ?? '');
                $like = "%{$search}%";

                if ($search !== '') {
                    $stmt = $db->prepare("
                        SELECT id, name, phone, email, address, verify_status, created_at
                        FROM users
                        WHERE name LIKE ?
                        ORDER BY created_at DESC
                    ");
                    $stmt->bind_param("s", $like);
                } else {
                    $stmt = $db->prepare("
                        SELECT id, name, phone, email, address, verify_status, created_at
                        FROM users
                        ORDER BY created_at DESC
                    ");
                }

                $stmt->execute();
                $customers = $stmt->get_result();

                // CSRF token for delete
                if (empty($_SESSION['csrf'])) {
                    $_SESSION['csrf'] = bin2hex(random_bytes(16));
                }
                ?>

                <form method="GET" style="margin: 12px 0; display:flex; gap:10px; align-items:center;">
                    <input type="hidden" name="tab" value="customers">
                    <input
                        type="text"
                        name="search"
                        placeholder="Search customer name..."
                        value="<?= htmlspecialchars($search) ?>"
                        style="padding:10px; border-radius:10px; border:1px solid #333; width: 320px;"
                    />
                    <button type="submit" class="btn-primary" style="padding:10px 14px; border-radius:10px; border:none; cursor:pointer;">
                        Search
                    </button>

                    <?php if ($search !== ''): ?>
                        <a href="dashboard.php?tab=customers" style="margin-left:6px;">Clear</a>
                    <?php endif; ?>
                </form>

                <div class="recent-orders">
                    <h2>Customer List</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Address</th>
                                <th>Verified</th>
                                <th>Joined</th>
                                <th style="text-align:right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if ($customers->num_rows === 0): ?>
                            <tr>
                                <td colspan="8" style="text-align:center; padding:16px;">No customers found.</td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; ?>
                            <?php while ($row = $customers->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['name']) ?></td>
                                    <td><?= htmlspecialchars($row['phone']) ?></td>
                                    <td><?= htmlspecialchars($row['email']) ?></td>
                                    <td><?= htmlspecialchars($row['address']) ?></td>
                                    <td>
                                        <span class="status <?= ((int)$row['verify_status'] === 1) ? 'delivered' : 'pending' ?>">
                                            <?= ((int)$row['verify_status'] === 1) ? 'Yes' : 'No' ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($row['created_at']) ?></td>

                                    <td style="text-align:right;">
                                        <a class="primary" href="edit-customer.php?id=<?= (int)$row['id'] ?>">Edit</a>

                                        <form method="POST" action="delete-customer.php" style="display:inline;" onsubmit="return confirm('Delete this customer?');">
                                            <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                                            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                                            <button type="submit" class="primary" style="background:none;border:none;color:#ff5c5c;cursor:pointer;">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="orders" class="tab-content <?= $activeTab === 'orders' ? 'active' : '' ?>">
                <h1>Orders</h1>

                <?php
                // Filters
                $order_search = trim($_GET['order_search'] ?? '');
                $status_filter = trim($_GET['status'] ?? '');

                $like = "%{$order_search}%";

                // Allowed statuses (adjust to your flow)
                $statusSteps = [
                    'Confirmed' => 1,
                    'Preparing' => 2,
                    'Ready'     => 3,
                ];

                $statusOptions = array_keys($statusSteps);

                // Query orders + customer (users table is used in your customers/feedback code)
                $sql = "
                    SELECT 
                        o.id, o.user_id, o.order_code, o.total, o.status, o.created_at,
                        o.payment_status, o.payment_method, o.paid_at,
                        u.name AS customer_name, u.email AS customer_email
                    FROM orders o
                    JOIN users u ON u.id = o.user_id
                    WHERE 1=1
                ";

                $types = "";
                $params = [];

                if ($order_search !== '') {
                    $sql .= " AND (o.order_code LIKE ? OR u.name LIKE ? OR u.email LIKE ? OR o.id LIKE ?) ";
                    $types .= "ssss";
                    $params[] = $like;
                    $params[] = $like;
                    $params[] = $like;
                    $params[] = $like;
                }

                if ($status_filter !== '' && in_array($status_filter, $statusOptions, true)) {
                    $sql .= " AND o.status = ? ";
                    $types .= "s";
                    $params[] = $status_filter;
                }

                $sql .= " ORDER BY o.created_at DESC";

                $stmt = $db->prepare($sql);
                if ($types !== "") $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $ordersRes = $stmt->get_result();

                $orders = [];
                $orderIds = [];
                while ($r = $ordersRes->fetch_assoc()) {
                    $orders[] = $r;
                    $orderIds[] = (int)$r['id'];
                }

                // Load items for all shown orders
                $itemsByOrder = [];
                if (!empty($orderIds)) {
                    $in = implode(',', array_fill(0, count($orderIds), '?'));
                    $itemSql = "
                        SELECT id, order_id, menu_name, price, quantity, temp, milk, syrup, addons
                        FROM order_items
                        WHERE order_id IN ($in)
                        ORDER BY order_id DESC, id ASC
                    ";
                    $itemStmt = $db->prepare($itemSql);
                    $itemTypes = str_repeat("i", count($orderIds));
                    $itemStmt->bind_param($itemTypes, ...$orderIds);
                    $itemStmt->execute();
                    $itemRes = $itemStmt->get_result();

                    while ($it = $itemRes->fetch_assoc()) {
                        $oid = (int)$it['order_id'];
                        if (!isset($itemsByOrder[$oid])) $itemsByOrder[$oid] = [];
                        $itemsByOrder[$oid][] = $it;
                    }
                }

                // Ensure CSRF exists
                if (empty($_SESSION['csrf'])) {
                    $_SESSION['csrf'] = bin2hex(random_bytes(16));
                }
                ?>

                <form method="GET" style="margin: 12px 0; display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                    <input type="hidden" name="tab" value="orders">

                    <input
                        type="text"
                        name="order_search"
                        placeholder="Search order code / customer name / email..."
                        value="<?= htmlspecialchars($order_search) ?>"
                        style="padding:10px; border-radius:10px; border:1px solid #333; width: 360px;"
                    />

                    <select name="status" style="padding:10px; border-radius:10px; border:1px solid #333; min-width:180px;">
                        <option value="">All Status</option>
                        <?php foreach ($statusOptions as $s): ?>
                            <option value="<?= htmlspecialchars($s) ?>" <?= ($status_filter === $s) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($s) ?>
                            </option>

                        <?php endforeach; ?>
                    </select>

                    <button type="submit" class="btn-primary"
                            style="padding:10px 14px; border-radius:10px; border:none; cursor:pointer;">
                        Filter
                    </button>

                    <?php if ($order_search !== '' || $status_filter !== ''): ?>
                        <a href="dashboard.php?tab=orders" style="margin-left:6px;">Clear</a>
                    <?php endif; ?>
                </form>

                <div class="recent-orders">
                    <h2>Order List</h2>
                    <form method="POST" action="bulk-update-order-status.php" id="bulkStatusForm">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
    <input type="hidden" name="return_to" value="dashboard.php?tab=orders">                   
    <div style="margin: 8px 0 14px 0; display:flex; justify-content:flex-end; gap:10px;">
        <button type="submit"
                class="btn-primary"
                style="padding:10px 14px; border-radius:10px; border:none; cursor:pointer;">
            Update All
        </button>
    </div>

    <table>
        <thead>
        <tr>
            <th>Order</th>
            <th>Customer</th>
            <th>Total</th>
            <th>Date</th>
            <th>Status</th>
            <th>Payment</th>
            <th style="text-align:right;">Actions</th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($orders as $o): ?>
            <?php
                $oid = (int)$o['id'];
                $status = trim($o['status'] ?? 'Confirmed');
            ?>
            <tr>
                <td>
                    <b>#<?= $oid ?></b><br>
                    <small class="text-muted"><?= htmlspecialchars($o['order_code']) ?></small>
                </td>

                <td>
                    <?= htmlspecialchars($o['customer_name']) ?><br>
                    <small class="text-muted"><?= htmlspecialchars($o['customer_email']) ?></small>
                </td>

                <td>RM<?= number_format((float)$o['total'], 2) ?></td>

                <td><?= htmlspecialchars($o['created_at']) ?></td>

                <td>
                <select name="statuses[<?= $oid ?>]"
                        style="padding:6px 8px; border-radius:8px; border:1px solid #333;">
                    <?php foreach ($statusOptions as $s): ?>
                    <option value="<?= htmlspecialchars($s) ?>" <?= ($status === $s) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($s) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                </td>

                <td>
                <?php
                    $pay = strtoupper(trim($o['payment_status'] ?? 'UNPAID'));
                    $payClass = ($pay === 'PAID') ? 'delivered' : 'pending';
                ?>
                <span class="status <?= $payClass ?>"><?= htmlspecialchars($pay) ?></span>
                </td>

                <td style="text-align:right; white-space:nowrap;">
                    <button type="button"
                            onclick="toggleDetails('od-<?= $oid ?>')"
                            class="primary"
                            style="background:none;border:none;cursor:pointer;">
                        Details
                    </button>

                    <!-- Delete WITHOUT nested form -->
                    <button type="submit"
                            name="order_id"
                            value="<?= $oid ?>"
                            formaction="delete-order.php"
                            formmethod="POST"
                            onclick="return confirm('Delete this order? This will also delete all its items.');"
                            class="primary"
                            style="background:none;border:none;color:#ff5c5c;cursor:pointer;">
                        Delete
                    </button>
                </td>
            </tr>

                                <!-- Details row -->
                                <tr id="od-<?= $oid ?>" style="display:none; background: rgba(255,255,255,0.02);">
                                    <td colspan="7" style="padding:14px 16px;">
                                        <div style="display:flex; gap:18px; flex-wrap:wrap;">
                                            <div style="min-width:260px;">
                                                <b>Order Code</b><br>
                                                <?= htmlspecialchars($o['order_code']) ?><br><br>
                                                <b>Customer</b><br>
                                                <?= htmlspecialchars($o['customer_name']) ?><br>
                                                <small class="text-muted"><?= htmlspecialchars($o['customer_email']) ?></small>
                                            </div>

                                            <div style="flex:1; min-width:360px;">
                                                <b>Items</b>
                                                <div style="margin-top:8px;">
                                                    <?php $items = $itemsByOrder[$oid] ?? []; ?>
                                                    <?php if (empty($items)): ?>
                                                        <small class="text-muted">No items found for this order.</small>
                                                    <?php else: ?>
                                                        <table style="width:100%; border-collapse:collapse;">
                                                            <thead>
                                                            <tr>
                                                                <th style="text-align:left; padding:6px 0;">Menu</th>
                                                                <th style="text-align:left; padding:6px 0;">Options</th>
                                                                <th style="text-align:left; padding:6px 0;">Qty</th>
                                                                <th style="text-align:left; padding:6px 0;">Price</th>
                                                                <th style="text-align:left; padding:6px 0;">Subtotal</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <?php foreach ($items as $it): ?>
                                                                <?php
                                                                $qty = (int)$it['quantity'];
                                                                $price = (float)$it['price'];
                                                                $sub = $qty * $price;

                                                                $optParts = [];
                                                                if (!empty($it['temp'])) $optParts[] = "Temp: " . $it['temp'];
                                                                if (!empty($it['milk'])) $optParts[] = "Milk: " . $it['milk'];
                                                                if (!empty($it['syrup'])) $optParts[] = "Syrup: " . $it['syrup'];
                                                                if (!empty($it['addons'])) $optParts[] = "Add-ons: " . $it['addons'];
                                                                $opts = !empty($optParts) ? implode(" | ", array_map('htmlspecialchars', $optParts)) : "-";
                                                                ?>

                                                                <tr>
                                                                    <td style="padding:6px 0;"><?= htmlspecialchars($it['menu_name']) ?></td>
                                                                    <td style="padding:6px 0;"><small class="text-muted"><?= $opts ?></small></td>
                                                                    <td style="padding:6px 0;"><?= $qty ?></td>
                                                                    <td style="padding:6px 0;">RM<?= number_format($price, 2) ?></td>
                                                                    <td style="padding:6px 0;">RM<?= number_format($sub, 2) ?></td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                            </tbody>
                                                        </table>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <div style="min-width:240px;">
                                            <b>Total</b><br>
                                            RM<?= number_format((float)$o['total'], 2) ?><br><br>

                                            <b>Status</b><br>
                                            <?= htmlspecialchars($status) ?><br><br>

                                            <b>Payment Status</b><br>
                                            <?= htmlspecialchars($o['payment_status'] ?? 'UNPAID') ?><br><br>

                                            <b>Payment Method</b><br>
                                            <?= !empty($o['payment_method']) ? htmlspecialchars($o['payment_method']) : '-' ?><br><br>

                                            <b>Paid At</b><br>
                                            <?= !empty($o['paid_at']) ? htmlspecialchars($o['paid_at']) : '-' ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    </form>
                </div>

                <script>
                    function toggleDetails(id) {
                        const row = document.getElementById(id);
                        if (!row) return;
                        row.style.display = (row.style.display === 'none' || row.style.display === '') ? 'table-row' : 'none';
                    }
                </script>
            </div>

            <div id="staff"    class="tab-content <?= $activeTab === 'staff' ? 'active' : '' ?>">
                <h1>Staff</h1>
                <p>Staff management content goes here.</p>
            </div>
            <div id="feedback" class="tab-content <?= $activeTab === 'feedback' ? 'active' : '' ?>">
                <h1>Feedback</h1>

                <?php
                $fb_search = trim($_GET['fb_search'] ?? '');
                $fb_like = "%{$fb_search}%";

                if ($fb_search !== '') {
                    $fbStmt = $db->prepare("
                        SELECT fm.id, fm.rating, fm.comment, fm.created_at,
                            u.id AS user_id, u.name, u.email, u.phone
                        FROM feedback_message fm
                        JOIN users u ON u.id = fm.user_id
                        WHERE u.name LIKE ?
                        ORDER BY fm.created_at DESC
                    ");
                    $fbStmt->bind_param("s", $fb_like);
                } else {
                    $fbStmt = $db->prepare("
                        SELECT fm.id, fm.rating, fm.comment, fm.created_at,
                            u.id AS user_id, u.name, u.email, u.phone
                        FROM feedback_message fm
                        JOIN users u ON u.id = fm.user_id
                        ORDER BY fm.created_at DESC
                    ");
                }

                $fbStmt->execute();
                $fbRes = $fbStmt->get_result();
                ?>

                <form method="GET" style="margin: 12px 0; display:flex; gap:10px; align-items:center;">
                    <input type="hidden" name="tab" value="feedback">
                    <input
                        type="text"
                        name="fb_search"
                        placeholder="Search customer name..."
                        value="<?= htmlspecialchars($fb_search) ?>"
                        style="padding:10px; border-radius:10px; border:1px solid #333; width: 360px;"
                    />
                    <button type="submit" class="btn-primary" style="padding:10px 14px; border-radius:10px; border:none; cursor:pointer;">
                        Search
                    </button>

                    <?php if ($fb_search !== ''): ?>
                        <a href="dashboard.php?tab=feedback" style="margin-left:6px;">Clear</a>
                    <?php endif; ?>
                </form>

                <div class="recent-orders">
                    <h2>All Feedback</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Customer</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Rating</th>
                                <th>Comment</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($fbRes->num_rows === 0): ?>
                                <tr>
                                    <td colspan="7" style="text-align:center; padding:16px;">No customer found.</td>
                                </tr>
                            <?php else: ?>
                                <?php $fbNo = 1; ?>
                                <?php while ($row = $fbRes->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $fbNo++ ?></td>
                                        <td><?= htmlspecialchars($row['name']) ?></td>
                                        <td><?= htmlspecialchars($row['email']) ?></td>
                                        <td><?= htmlspecialchars($row['phone']) ?></td>
                                        <td>
                                            <span class="status delivered">
                                                <?= (int)$row['rating'] ?>/5
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($row['comment']) ?></td>
                                        <td><?= htmlspecialchars($row['created_at']) ?></td>
                                        <td style="text-align:right;">
                                            <form method="POST" action="delete-feedback.php"
                                                style="display:inline;"
                                                onsubmit="return confirm('Delete this feedback?');">
                                                <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                                                <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                                                <button type="submit"
                                                        class="primary"
                                                        style="background:none;border:none;color:#ff5c5c;cursor:pointer;">
                                                    Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div id="reports"  class="tab-content <?= $activeTab === 'reports' ? 'active' : '' ?>">
                <h1>Reports</h1>
                <p>Reports content goes here.</p>
            </div>           
            <div id="profile"  class="tab-content <?= $activeTab === 'profile' ? 'active' : '' ?>">
                <h1>Profile</h1>
                <div class="profile-container">
                    <div class="profile-header">
                        <div class="profile-avatar">
                            <img src="<?= htmlspecialchars($user['profile_image'] ?: '../assets/img/profile-1.jpg') ?>?t=<?= time() ?>" alt="Profile Picture" id="profile-img">
                        </div>
                        <div class="profile-info">
                            <h2><?= htmlspecialchars($user['name']) ?></h2>
                            <p>Admin</p>
                            <button type="button" class="change-photo-btn" onclick="document.getElementById('profile_photo').click()">
                                <span class="material-symbols-sharp">camera_alt</span> Change Profile Picture
                            </button>
                        </div>
                    </div>
                    <div class="profile-card">
                        <h3><span class="material-symbols-sharp">edit</span> Edit Your Details</h3>
                        <form method="POST" action="update-profile.php" enctype="multipart/form-data" class="profile-form">
                            <input type="hidden" name="return_to" value="dashboard.php?tab=profile">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="name"><span class="material-symbols-sharp">person</span> Full Name</label>
                                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="email"><span class="material-symbols-sharp">email</span> Email</label>
                                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" readonly>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="phone"><span class="material-symbols-sharp">phone</span> Phone Number</label>
                                    <input
                                    type="tel"
                                    id="phone"
                                    name="phone"
                                    value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                                    required
                                    inputmode="numeric"
                                    maxlength="11"
                                    pattern="^01[0-9]{8,9}$"
                                    title="Enter a valid phone number"
                                    oninput="this.value = this.value.replace(/\D/g,'').slice(0,11);"
                                    />
                                </div>
                                <div class="form-group">
                                    <label for="role"><span class="material-symbols-sharp">badge</span> Role</label>
                                    <input type="text" id="role" name="role" value="<?= htmlspecialchars($user['role']) ?>" readonly>
                                </div>
                            </div>
                            <input type="file" id="profile_photo" name="profile_photo" accept="image/*" style="display: none;">
                            <button type="submit" class="btn-primary"><span class="material-symbols-sharp">save</span> Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </main>

        <!-----------------------END OF MAIN --------------------->

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
                        <img
                        src="<?= htmlspecialchars($user['profile_image'] ?: '../assets/img/profile-1.jpg') ?>?t=<?= time() ?>"
                        alt=""
                        >
                    </div>
                </div>
            </div>
            <!--------END OF TOP------------>
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
                                        <small class="text-muted">
                                            <?= htmlspecialchars($fb['comment']) ?>
                                        </small>
                                        <br>
                                        <small class="text-muted">
                                            <?= htmlspecialchars($fb['created_at']) ?>
                                        </small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
        </div>

    <script src="../js/index.js"></script>
</body>
</html>
