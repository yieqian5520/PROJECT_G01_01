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
        (COALESCE(s.sales,0)    + COALESCE(a.sales,0))    AS total_sales,
        (COALESCE(e.expenses,0) + COALESCE(a.expenses,0)) AS total_expenses,
        (COALESCE(s.sales,0) - COALESCE(e.expenses,0) + COALESCE(a.income,0)) AS total_income
    FROM
        (
        SELECT COALESCE(SUM(o.total),0) AS sales
        FROM orders o
        WHERE DATE(o.created_at) = ?
            AND UPPER(TRIM(o.payment_status)) = 'PAID'
        ) s
    CROSS JOIN
        (
        SELECT COALESCE(SUM(GREATEST(oi.price - 4, 0) * oi.quantity),0) AS expenses
        FROM order_items oi
        JOIN orders o ON o.id = oi.order_id
        WHERE DATE(o.created_at) = ?
            AND UPPER(TRIM(o.payment_status)) = 'PAID'
        ) e
    LEFT JOIN dashboard_daily_totals a
        ON a.day = ?
    ");
    $todayStmt->bind_param("sss", $selectedDate, $selectedDate, $selectedDate);

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

                    <!-- Search + Delete toggle -->
                    <form method="GET" id="customerSearchForm" style="margin: 12px 0; display:flex; gap:10px; align-items:center;">
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

                        <!-- SINGLE delete button beside search -->
                        <button
                            type="button"
                            id="toggleBulkDelete"
                            class="btn-primary"
                            style="padding:10px 14px; border-radius:10px; border:none; cursor:pointer; background:#ff5c5c;"
                        >
                            Delete
                        </button>

                        <span id="selectedCounter"
                            style="display:none; margin-left:6px; font-size:13px; opacity:.85;">
                            0 selected
                        </span>

                        <?php if ($search !== ''): ?>
                            <a href="dashboard.php?tab=customers" style="margin-left:6px;">Clear</a>
                        <?php endif; ?>
                    </form>

                    <!-- Bulk delete form wraps the table -->
                    <form method="POST" action="bulk-delete-customer.php" id="customersActionForm">
                        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">

                        <div class="recent-orders" id="customersTableWrap">
                            <h2 style="display:flex; align-items:center; justify-content:space-between; gap:10px;">
                                <span>Customer List</span>

                                <!-- Optional: cancel button (only shows in delete mode) -->
                                <button type="button" id="cancelBulkDelete"
                                        style="display:none; background:none; border:1px solid rgba(255,255,255,.2); color:#fff; padding:8px 12px; border-radius:10px; cursor:pointer;">
                                    Cancel
                                </button>
                            </h2>

                            <table id="customersTable">
                                <thead>
                                    <tr>
                                        <!-- Checkbox column (hidden until delete mode) -->
                                        <th class="select-col" style="width:46px; text-align:center; display:none;">
                                            <input type="checkbox" id="checkAll">
                                        </th>

                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Address</th>
                                        <th>Verified</th>
                                        <th>Joined</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                <?php if ($customers->num_rows === 0): ?>
                                    <tr>
                                        <td colspan="9" style="text-align:center; padding:16px;">No customers found.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php $no = 1; ?>
                                    <?php while ($row = $customers->fetch_assoc()): ?>
                                        <tr class="cust-row" data-id="<?= (int)$row['id'] ?>">
                                            <!-- Checkbox column (hidden until delete mode) -->
                                            <td class="select-col" style="text-align:center; display:none;">
                                                <input type="checkbox" class="row-check" name="ids[]" value="<?= (int)$row['id'] ?>">
                                            </td>

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

                                            <!-- Action: ONLY Edit now -->
                                            <td style="text-align:right;">
                                                <a class="primary" href="edit-customer-admin.php?id=<?= (int)$row['id'] ?>">Edit</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </form>

                    

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

                    <form method="GET" id="orderFilterForm"
                        style="margin: 12px 0; display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
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

                        <!-- NEW: single delete button like customers -->
                        <button type="button"
                                id="toggleBulkDeleteOrders"
                                class="btn-primary"
                                style="padding:10px 14px; border-radius:10px; border:none; cursor:pointer; background:#ff5c5c;">
                            Delete
                        </button>

                        <span id="selectedCounterOrders"
                            style="display:none; margin-left:6px; font-size:13px; opacity:.85;">
                            0 selected
                        </span>

                        <?php if ($order_search !== '' || $status_filter !== ''): ?>
                            <a href="dashboard.php?tab=orders" style="margin-left:6px;">Clear</a>
                        <?php endif; ?>
                    </form>

                    <div class="recent-orders">
                        <h2 style="display:flex; align-items:center; justify-content:space-between; gap:10px;">
                            <span>Order List</span>

                            <!-- Cancel button (only shows in delete mode) -->
                            <button type="button" id="cancelBulkDeleteOrders"
                            style="display:none; background:none; border:1px solid rgba(255,255,255,.2); color:#fff; padding:8px 12px; border-radius:10px; cursor:pointer;">
                            Cancel
                            </button>
                        </h2>
                        <form id="ordersDeleteForm" method="POST" action="bulk-delete-orders.php">
  <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
</form>
                        <!-- ONE form only (no nesting) -->
  <form method="POST" action="update-order-status.php" id="ordersActionForm">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
    <input type="hidden" name="return_to" value="dashboard.php?tab=orders">

    <div style="margin: 8px 0 14px 0; display:flex; justify-content:flex-end; gap:10px;">
  <button type="submit"
          class="btn-primary"
          formaction="update-order-status.php"
          style="padding:10px 14px; border-radius:10px; border:none; cursor:pointer;">
    Update All
  </button>
</div>

    <table id="ordersTable">
      <thead>
        <tr>
          <!-- Checkbox column (hidden until delete mode) -->
          <th class="select-col-orders" style="width:46px; text-align:center; display:none;">
            <input type="checkbox" id="checkAllOrders">
          </th>

          <th>Order</th>
          <th>Customer</th>
          <th>Total</th>
          <th>Date</th>
          <th>Status</th>
          <th>Payment</th>
          <th style>Actions</th>
        </tr>
      </thead>

      <tbody>
        <?php foreach ($orders as $o): ?>
          <?php
            $oid = (int)$o['id'];
            $status = trim($o['status'] ?? 'Confirmed');
          ?>

          <tr class="order-row" data-id="<?= $oid ?>">
            <!-- Checkbox column (hidden until delete mode) -->
            <td class="select-col-orders" style="text-align:center; display:none;">
              <input type="checkbox" class="order-check" name="order_ids[]" value="<?= $oid ?>" form="ordersDeleteForm">
            </td>

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
            <a class="primary"
            href="order-details.php?id=<?= $oid ?>&return_to=<?= urlencode('dashboard.php?tab=orders') ?>">
            Details
            </a>
            </td>
          </tr>

                                    <!-- Details row -->
          <tr id="od-<?= $oid ?>" style="display:none; background: rgba(255,255,255,0.02);">
                                        <td colspan="8" style="padding:14px 16px;">
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

                <div id="staff" class="tab-content <?= $activeTab === 'staff' ? 'active' : '' ?>">
                    <h1>Staff</h1>

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

                    <?php
                    $staff_search = trim($_GET['staff_search'] ?? '');
                    $staff_role_filter = trim($_GET['staff_role'] ?? '');

                    $staff_like = "%{$staff_search}%";

                    // must match your ENUM exactly:
                    $staffRoles = ['admin','staff'];

                    $sql = "
                    SELECT id, name, email, phone, role, profile_image, joined_date
                    FROM user
                    WHERE role IN ('admin','staff')
                    ";

                    $types = "";
                    $params = [];

                    if ($staff_search !== '') {
                    $sql .= " AND (name LIKE ? OR email LIKE ? OR phone LIKE ?) ";
                    $types .= "sss";
                    $params[] = $staff_like;
                    $params[] = $staff_like;
                    $params[] = $staff_like;
                    }

                    if ($staff_role_filter !== '' && in_array($staff_role_filter, $staffRoles, true)) {
                    $sql .= " AND role = ? ";
                    $types .= "s";
                    $params[] = $staff_role_filter;
                    }

                    $sql .= " ORDER BY id DESC";

                    $stStmt = $db->prepare($sql);
                    if ($types !== "") $stStmt->bind_param($types, ...$params);
                    $stStmt->execute();
                    $staffRes = $stStmt->get_result();

                    if (empty($_SESSION['csrf'])) {
                    $_SESSION['csrf'] = bin2hex(random_bytes(16));
                    }
                    ?>

                    <!-- Filter Bar -->
                    <form method="GET" id="staffFilterForm"
                            style="margin: 12px 0; display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                        <input type="hidden" name="tab" value="staff">

                        <input
                        type="text"
                        name="staff_search"
                        placeholder="Search staff name / email / phone..."
                        value="<?= htmlspecialchars($staff_search) ?>"
                        style="padding:10px; border-radius:10px; border:1px solid #333; width: 360px;"
                        />

                        <select name="staff_role" style="padding:10px; border-radius:10px; border:1px solid #333; min-width:180px;">
                        <option value="">All Roles</option>
                        <?php foreach ($staffRoles as $r): ?>
                            <option value="<?= htmlspecialchars($r) ?>" <?= ($staff_role_filter === $r) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($r) ?>
                            </option>
                        <?php endforeach; ?>
                        </select>

                        <button type="submit" class="btn-primary"
                                style="padding:10px 14px; border-radius:10px; border:none; cursor:pointer;">
                        Filter
                        </button>

                        <!-- Add Staff -->
                        <button type="button"
                                id="openAddStaffModal"
                                class="btn-primary"
                                style="padding:10px 14px; border-radius:10px; border:none; cursor:pointer;">
                        + Add Staff
                        </button>

                        <!-- Bulk Delete toggle -->
                        <button type="button"
                                id="toggleBulkDeleteStaff"
                                class="btn-primary"
                                style="padding:10px 14px; border-radius:10px; border:none; cursor:pointer; background:#ff5c5c;">
                        Delete
                        </button>

                        <span id="selectedCounterStaff"
                            style="display:none; margin-left:6px; font-size:13px; opacity:.85;">
                        0 selected
                        </span>

                        <?php if ($staff_search !== '' || $staff_role_filter !== ''): ?>
                        <a href="dashboard.php?tab=staff" style="margin-left:6px;">Clear</a>
                        <?php endif; ?>
                    </form>

                    <div class="recent-orders">
                        <h2 style="display:flex; align-items:center; justify-content:space-between; gap:10px;">
                        <span>Staff List</span>

                        <button type="button" id="cancelBulkDeleteStaff"
                                style="display:none; background:none; border:1px solid rgba(255,255,255,.2); color:#fff; padding:8px 12px; border-radius:10px; cursor:pointer;">
                            Cancel
                        </button>
                        </h2>

                        <!-- Separate form for delete (IMPORTANT to avoid form nesting) -->
                        <form id="staffDeleteForm" method="POST" action="bulk-delete-staff.php">
                        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                        </form>

                        <table id="staffTable">
                        <thead>
                            <tr>
                            <th class="select-col-staff" style="width:46px; text-align:center; display:none;">
                                <input type="checkbox" id="checkAllStaff">
                            </th>

                            <th>#</th>
                            <th>Staff</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Joined</th>
                            <th style="text-align:right;">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                        <?php if ($staffRes->num_rows === 0): ?>
                            <tr>
                            <td colspan="8" style="text-align:center; padding:16px;">No staff found.</td>
                            </tr>
                        <?php else: ?>
                            <?php $sno = 1; ?>
                            <?php while ($s = $staffRes->fetch_assoc()): ?>
                            <?php $sid = (int)$s['id']; ?>
                            <tr class="staff-row" data-id="<?= $sid ?>">
                                <td class="select-col-staff" style="text-align:center; display:none;">
                                <input type="checkbox"
                                        class="staff-check"
                                        name="staff_ids[]"
                                        value="<?= $sid ?>"
                                        form="staffDeleteForm">
                                </td>

                                <td><?= $sno++ ?></td>

                                <td style="text-align:left;">
                                <div style="display:flex; gap:10px; align-items:center;">
                                    <img
                                    src="<?= htmlspecialchars($s['profile_image'] ?: '../assets/img/Default_pfp.jpg') ?>"
                                    alt=""
                                    style="width:34px; height:34px; border-radius:50%; object-fit:cover;"
                                    >
                                    <div>
                                    <b><?= htmlspecialchars($s['name']) ?></b><br>
                                    <small class="text-muted">#<?= $sid ?></small>
                                    </div>
                                </div>
                                </td>

                                <td><?= htmlspecialchars($s['email']) ?></td>
                                <td><?= htmlspecialchars($s['phone'] ?? '-') ?></td>

                                <td>
                                <span class="status delivered"><?= htmlspecialchars($s['role'] ?? '-') ?></span>
                                </td>

                                <td><?= htmlspecialchars($s['joined_date'] ?? '-') ?></td>

                                <td style="text-align:right; white-space:nowrap;">
                                <button type="button"
                                        class="primary"
                                        data-edit-staff
                                        data-id="<?= $sid ?>"
                                        data-name="<?= htmlspecialchars($s['name']) ?>"
                                        data-email="<?= htmlspecialchars($s['email']) ?>"
                                        data-phone="<?= htmlspecialchars($s['phone'] ?? '') ?>"
                                        data-role="<?= htmlspecialchars($s['role'] ?? 'Staff') ?>"
                                        style="background:none; cursor:pointer;">
                                    Edit
                                </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                        </tbody>
                        </table>
                    </div>

                    <!-- ===== Add Staff Modal ===== -->
                    <div id="addStaffModal" class="modal" style="display:none;">
                        <div class="modal-content" style="max-width:560px;">
                        <div style="display:flex; justify-content:space-between; align-items:center; gap:12px;">
                            <h2 style="margin:0;">Add Staff</h2>
                            <button type="button" id="closeAddStaffModal" style="background:none; cursor:pointer;">
                            <span class="material-symbols-sharp">close</span>
                            </button>
                        </div>

                        <form method="POST" action="add-staff.php" style="margin-top:14px;">
                            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                            <input type="hidden" name="return_to" value="dashboard.php?tab=staff">

                            <div class="form-row" style="display:flex; gap:12px;">
                            <div class="form-group" style="flex:1;">
                                <label>Name</label>
                                <input type="text" name="name" required>
                            </div>
                            <div class="form-group" style="flex:1;">
                                <label>Phone</label>
                                <input type="text" name="phone" maxlength="11" oninput="this.value=this.value.replace(/\\D/g,'').slice(0,11);">
                            </div>
                            </div>

                            <div class="form-row" style="display:flex; gap:12px; margin-top:12px;">
                            <div class="form-group" style="flex:1;">
                                <label>Email</label>
                                <input type="email" name="email" required>
                            </div>
                            <div class="form-group" style="flex:1;">
                                <label>Role</label>
                                <select name="role" required>
                                <?php foreach ($staffRoles as $r): ?>
                                    <option value="<?= htmlspecialchars($r) ?>"><?= htmlspecialchars($r) ?></option>
                                <?php endforeach; ?>
                                </select>
                            </div>
                            </div>

                            <div class="form-row" style="display:flex; gap:12px; margin-top:12px;">
                            <div class="form-group" style="flex:1;">
                                <label>Password</label>
                                <input type="password" name="password" required minlength="8">
                                <small class="text-muted">Min 8 characters</small>
                            </div>
                            </div>

                            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:16px;">
                            <button type="button" id="cancelAddStaffModal"
                                    style="background:none; border:1px solid rgba(255,255,255,.2); padding:10px 14px; border-radius:10px; cursor:pointer;">
                                Cancel
                            </button>
                            <button type="submit" class="btn-primary" style="padding:10px 14px; border-radius:10px; cursor:pointer;">
                                Save
                            </button>
                            </div>
                        </form>
                        </div>
                    </div>

                    <!-- ===== Edit Staff Modal ===== -->
                    <div id="editStaffModal" class="modal" style="display:none;">
                <div class="modal-content" style="max-width:560px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; gap:12px;">
                    <h2 style="margin:0;">Edit Staff</h2>
                    <button type="button" id="closeEditStaffModal" style="background:none; cursor:pointer;">
                        <span class="material-symbols-sharp">close</span>
                    </button>
                    </div>

                    <form method="POST" action="edit-staff.php" style="margin-top:14px;">
                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                    <input type="hidden" name="return_to" value="dashboard.php?tab=staff">
                    <input type="hidden" name="id" id="staff_edit_id">

                    <div class="form-row" style="display:flex; gap:12px;">
                        <div class="form-group" style="flex:1;">
                        <label>Name</label>
                        <input type="text" name="name" id="staff_edit_name" required>
                        </div>

                        <div class="form-group" style="flex:1;">
                        <label>Phone</label>
                        <input type="text" name="phone" id="staff_edit_phone" maxlength="11"
                                oninput="this.value=this.value.replace(/\D/g,'').slice(0,11);">
                        </div>
                    </div>

                    <div class="form-row" style="display:flex; gap:12px; margin-top:12px;">
                        <div class="form-group" style="flex:1;">
                        <label>Email</label>
                        <!-- ✅ NO name="email" so it won't be submitted -->
                        <input type="email" id="staff_edit_email" readonly
                                style="opacity:.75; cursor:not-allowed;">
                        </div>

                        <div class="form-group" style="flex:1;">
                        <label>Role</label>
                        <!-- ✅ readonly doesn't work for select; use disabled -->
                        <!-- ✅ NO name="role" so it won't be submitted -->
                        <select id="staff_edit_role" disabled style="opacity:.75; cursor:not-allowed;">
                            <?php foreach ($staffRoles as $r): ?>
                            <option value="<?= htmlspecialchars($r) ?>"><?= htmlspecialchars($r) ?></option>
                            <?php endforeach; ?>
                        </select>
                        </div>
                    </div>

                    <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:16px;">
                        <button type="button" id="cancelEditStaffModal"
                                style="background:none; border:1px solid rgba(255,255,255,.2); padding:10px 14px; border-radius:10px; cursor:pointer;">
                        Cancel
                        </button>
                        <button type="submit" class="btn-primary"
                                style="padding:10px 14px; border-radius:10px; cursor:pointer;">
                        Update
                        </button>
                    </div>
                    </form>
                </div>
                </div>

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

                    <form method="GET" style="margin: 12px 0; display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                    <input type="hidden" name="tab" value="feedback">

                    <input
                        type="text"
                        name="fb_search"
                        placeholder="Search customer name..."
                        value="<?= htmlspecialchars($fb_search) ?>"
                        style="padding:10px; border-radius:10px; border:1px solid #333; width: 360px;"
                    />

                    <button type="submit" class="btn-primary"
                            style="padding:10px 14px; border-radius:10px; border:none; cursor:pointer;">
                        Search
                    </button>

                    <!-- Delete toggle -->
                    <button type="button"
                            id="toggleBulkDeleteFeedback"
                            class="btn-primary"
                            style="padding:10px 14px; border-radius:10px; border:none; cursor:pointer; background:#ff5c5c;">
                        Delete
                    </button>

                    <span id="selectedCounterFeedback"
                            style="display:none; margin-left:6px; font-size:13px; opacity:.85;">
                        0 selected
                    </span>

                    <?php if ($fb_search !== ''): ?>
                        <a href="dashboard.php?tab=feedback" style="margin-left:6px;">Clear</a>
                    <?php endif; ?>
                    </form>


                    <div class="recent-orders">
                        <h2 style="display:flex; align-items:center; justify-content:space-between; gap:10px;">
                            <span>All Feedback</span>

                            <button type="button" id="cancelBulkDeleteFeedback"
                            style="display:none; background:none; border:1px solid rgba(255,255,255,.2); color:#fff; padding:8px 12px; border-radius:10px; cursor:pointer;">
                            Cancel
                            </button>
                        </h2>

                        <!-- Separate form for deleting feedback -->
                        <form id="feedbackDeleteForm" method="POST" action="bulk-delete-feedback.php">
                            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                        </form>

                        <table id="feedbackTable">
                            <thead>
                            <tr>
                                <!-- checkbox col hidden until delete mode -->
                                <th class="select-col-feedback" style="width:46px; text-align:center; display:none;">
                                <input type="checkbox" id="checkAllFeedback">
                                </th>

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
                                <td colspan="8" style="text-align:center; padding:16px;">No feedback found.</td>
                                </tr>
                            <?php else: ?>
                                <?php $fbNo = 1; ?>
                                <?php while ($row = $fbRes->fetch_assoc()): ?>
                                <tr class="feedback-row" data-id="<?= (int)$row['id'] ?>">

                                    <!-- checkbox hidden until delete mode -->
                                    <td class="select-col-feedback" style="text-align:center; display:none;">
                                    <!-- IMPORTANT: use form="feedbackDeleteForm" so checkbox belongs to delete form -->
                                    <input type="checkbox"
                                            class="feedback-check"
                                            name="feedback_ids[]"
                                            value="<?= (int)$row['id'] ?>"
                                            form="feedbackDeleteForm">
                                    </td>

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
                                </tr>
                                <?php endwhile; ?>
                            <?php endif; ?>
                            </tbody>
                        </table>
                        </div>

                </div>
                <div id="reports" class="tab-content <?= $activeTab === 'reports' ? 'active' : '' ?>">
                    <h1>Sales Report</h1>

                    <?php
                    // ====== Report controls ======
                    $period = $_GET['period'] ?? 'week'; // week | month | year
                    $allowedPeriods = ['week','month','year'];
                    if (!in_array($period, $allowedPeriods, true)) $period = 'week';

                    // Anchor date (default today)
                    $reportDate = $_GET['report_date'] ?? date('Y-m-d');
                    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $reportDate)) $reportDate = date('Y-m-d');

                    // Build date range [start, end)
                    $dt = new DateTime($reportDate);

                    if ($period === 'week') {
                        $start = (clone $dt)->modify('monday this week')->setTime(0,0,0);
                        $end   = (clone $start)->modify('+7 days');
                        $label = "Weekly (" . $start->format('Y-m-d') . " to " . (clone $end)->modify('-1 day')->format('Y-m-d') . ")";
                    } elseif ($period === 'month') {
                        $start = (clone $dt)->modify('first day of this month')->setTime(0,0,0);
                        $end   = (clone $start)->modify('first day of next month');
                        $label = "Monthly (" . $start->format('F Y') . ")";
                    } else { // year
                        $start = (clone $dt)->setDate((int)$dt->format('Y'), 1, 1)->setTime(0,0,0);
                        $end   = (clone $start)->modify('+1 year');
                        $label = "Yearly (" . $start->format('Y') . ")";
                    }

                    $startStr = $start->format('Y-m-d H:i:s');
                    $endStr   = $end->format('Y-m-d H:i:s');

                    // ====== Item breakdown report ======
                    $reportSql = "
                        SELECT
                        oi.menu_name AS item_name,
                        oi.price AS unit_price,
                        SUM(oi.quantity) AS qty_sold,
                        SUM(oi.price * oi.quantity) AS total_revenue,
                        -- ✅ cost per item = RM4
                        (SUM(oi.quantity) * 4) AS total_expenses,
                        -- ✅ income = revenue - expenses
                        (SUM(oi.price * oi.quantity) - (SUM(oi.quantity) * 4)) AS total_income
                        FROM order_items oi
                        JOIN orders o ON o.id = oi.order_id
                        WHERE o.created_at >= ?
                        AND o.created_at < ?
                        AND UPPER(TRIM(o.payment_status)) = 'PAID'
                        GROUP BY oi.menu_name, oi.price
                        ORDER BY total_income DESC
                    ";
                    $repStmt = $db->prepare($reportSql);
                    $repStmt->bind_param("ss", $startStr, $endStr);
                    $repStmt->execute();
                    $repRes = $repStmt->get_result();

                    $rows = [];
                    $grandRevenue = 0.0;
                    $grandExpenses = 0.0;
                    $grandIncome = 0.0;
                    $totalItemsSold = 0;

                    while ($r = $repRes->fetch_assoc()) {
                        $rows[] = $r;
                        $grandRevenue  += (float)$r['total_revenue'];
                        $grandExpenses += (float)$r['total_expenses'];
                        $grandIncome   += (float)$r['total_income'];
                        $totalItemsSold += (int)$r['qty_sold'];
                    }

                    // ====== KPI: Orders count + AOV ======
                    $kpiSql = "
                    SELECT
                        COUNT(DISTINCT o.id) AS total_orders,
                        COALESCE(SUM(o.total),0) AS orders_revenue,
                        COALESCE(SUM(oi.quantity),0) AS items_sold,
                        (COALESCE(SUM(oi.quantity),0) * 4) AS orders_expenses,
                        (COALESCE(SUM(o.total),0) - (COALESCE(SUM(oi.quantity),0) * 4)) AS orders_income
                    FROM orders o
                    JOIN order_items oi ON oi.order_id = o.id
                    WHERE o.created_at >= ?
                        AND o.created_at < ?
                        AND UPPER(TRIM(o.payment_status)) = 'PAID'
                    ";
                    $kpiStmt = $db->prepare($kpiSql);
                    $kpiStmt->bind_param("ss", $startStr, $endStr);
                    $kpiStmt->execute();
                    $kpi = $kpiStmt->get_result()->fetch_assoc();

                    $totalOrders     = (int)($kpi['total_orders'] ?? 0);
                    $ordersRevenue   = (float)($kpi['orders_revenue'] ?? 0);
                    $totalItemsSold  = (int)($kpi['items_sold'] ?? 0);
                    $grandExpenses   = (float)($kpi['orders_expenses'] ?? 0);
                    $grandIncome     = (float)($kpi['orders_income'] ?? 0);

                    // Avg order value
                    $avgOrderValue = ($totalOrders > 0) ? ($ordersRevenue / $totalOrders) : 0;
                    $kpiStmt = $db->prepare($kpiSql);
                    $kpiStmt->bind_param("ss", $startStr, $endStr);
                    $kpiStmt->execute();
                    $kpi = $kpiStmt->get_result()->fetch_assoc();

                    $totalOrders = (int)($kpi['total_orders'] ?? 0);
                    $ordersRevenue = (float)($kpi['orders_revenue'] ?? 0);
                    $avgOrderValue = ($totalOrders > 0) ? ($ordersRevenue / $totalOrders) : 0;

                    // ====== Trend chart data ======
                    // week/month -> daily; year -> monthly
                    if ($period === 'year') {
                        $trendSql = "
                        SELECT
                            DATE_FORMAT(o.created_at, '%Y-%m') AS bucket,
                            COALESCE(SUM(o.total),0) AS revenue,
                            (COALESCE(SUM(oi.quantity),0) * 4) AS expenses
                        FROM orders o
                        JOIN order_items oi ON oi.order_id = o.id
                        WHERE o.created_at >= ?
                            AND o.created_at < ?
                            AND UPPER(TRIM(o.payment_status)) = 'PAID'
                        GROUP BY DATE_FORMAT(o.created_at, '%Y-%m')
                        ORDER BY bucket ASC
                        ";
                    } else {
                        $trendSql = "
                        SELECT
                            DATE(o.created_at) AS bucket,
                            COALESCE(SUM(o.total),0) AS revenue,
                            (COALESCE(SUM(oi.quantity),0) * 4) AS expenses
                        FROM orders o
                        JOIN order_items oi ON oi.order_id = o.id
                        WHERE o.created_at >= ?
                            AND o.created_at < ?
                            AND UPPER(TRIM(o.payment_status)) = 'PAID'
                        GROUP BY DATE(o.created_at)
                        ORDER BY bucket ASC
                        ";
                    }

                    $trendStmt = $db->prepare($trendSql);
                    $trendStmt->bind_param("ss", $startStr, $endStr);
                    $trendStmt->execute();
                    $trendRes = $trendStmt->get_result();

                    $trendLabels = [];
                    $trendRevenue = [];
                    $trendExpenses = [];
                    $trendIncome = [];

                    while ($t = $trendRes->fetch_assoc()) {
                        $rev = (float)$t['revenue'];
                        $exp = (float)$t['expenses'];
                        $trendLabels[] = (string)$t['bucket'];
                        $trendRevenue[] = $rev;
                        $trendExpenses[] = $exp;
                        $trendIncome[] = $rev - $exp;
                    }

                    // ====== Top 5 items (by income) ======
                    $top5 = array_slice($rows, 0, 5);
                    ?>

                    <!-- ====== Controls ====== -->
                    <form method="GET" style="margin: 12px 0; display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                        <input type="hidden" name="tab" value="reports">

                        <select name="period" style="padding:10px; border-radius:10px; border:1px solid #333; min-width:180px;">
                        <option value="week"  <?= $period==='week' ? 'selected' : '' ?>>Weekly</option>
                        <option value="month" <?= $period==='month' ? 'selected' : '' ?>>Monthly</option>
                        <option value="year"  <?= $period==='year' ? 'selected' : '' ?>>Yearly</option>
                        </select>

                        <input type="date" name="report_date"
                        value="<?= htmlspecialchars($reportDate) ?>"
                        style="padding:10px; border-radius:10px; border:1px solid #333;" />

                        <button type="submit" class="btn-primary"
                        style="padding:10px 14px; border-radius:10px; border:none; cursor:pointer;">
                        View Report
                        </button>

                        <!-- Export CSV -->
                        <button type="button" id="exportCsvBtn"
                        class="btn-primary"
                        style="padding:10px 14px; border-radius:10px; border:none; cursor:pointer;">
                        Export CSV
                        </button>
                    </form>

                    <!-- ====== KPI Cards ====== -->
                    <div class="insights" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));">

                        <div class="sales">
                        <span class="material-symbols-sharp">payments</span>
                        <div class="middle">
                            <div class="left">
                            <h3>Total Revenue</h3>
                            <h1>RM<?= number_format($grandRevenue, 2) ?></h1>
                            <small class="text-muted"><?= htmlspecialchars($label) ?></small>
                            </div>
                        </div>
                        </div>

                        <div class="expenses">
                        <span class="material-symbols-sharp">receipt</span>
                        <div class="middle">
                            <div class="left">
                            <h3>Total Expenses</h3>
                            <h1>RM<?= number_format($grandExpenses, 2) ?></h1>
                            </div>
                        </div>
                        </div>

                        <div class="income">
                        <span class="material-symbols-sharp">trending_up</span>
                        <div class="middle">
                            <div class="left">
                            <h3>Total Income</h3>
                            <h1>RM<?= number_format($grandIncome, 2) ?></h1>
                            </div>
                        </div>
                        </div>

                        <div class="sales">
                        <span class="material-symbols-sharp">shopping_cart</span>
                        <div class="middle">
                            <div class="left">
                            <h3>Paid Orders</h3>
                            <h1><?= number_format($totalOrders) ?></h1>
                            </div>
                        </div>
                        </div>

                        <div class="sales">
                        <span class="material-symbols-sharp">inventory_2</span>
                        <div class="middle">
                            <div class="left">
                            <h3>Items Sold</h3>
                            <h1><?= number_format($totalItemsSold) ?></h1>
                            <small class="text-muted">Total quantity sold</small>
                            </div>
                        </div>
                        </div>

                        <div class="sales">
                        <span class="material-symbols-sharp">calculate</span>
                        <div class="middle">
                            <div class="left">
                            <h3>Avg Order Value</h3>
                            <h1>RM<?= number_format($avgOrderValue, 2) ?></h1>
                            <small class="text-muted">Revenue / Orders</small>
                            </div>
                        </div>
                        </div>

                    </div>

                    <!-- ====== Trend Chart + Top 5 ====== -->
                    <div style="display:grid; grid-template-columns: 2fr 1fr; gap:16px; margin-top:16px;">
                        <div class="recent-orders" style="padding:16px;">
                        <h2 style="display:flex; justify-content:space-between; align-items:center;">
                            <span>Trend</span>
                            <small class="text-muted"><?= htmlspecialchars($period === 'year' ? 'Monthly buckets' : 'Daily buckets') ?></small>
                        </h2>

                        <canvas id="trendChart" height="110"></canvas>
                        <small class="text-muted">Revenue vs Expenses vs Income</small>
                        </div>

                        <div class="recent-orders" style="padding:16px;">
                        <h2>Top 5 Items</h2>
                        <?php if (empty($top5)): ?>
                            <p class="text-muted" style="margin-top:10px;">No data.</p>
                        <?php else: ?>
                            <table style="width:100%;">
                            <thead>
                                <tr>
                                <th style="text-align:left;">Item</th>
                                <th style="text-align:right;">Income</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($top5 as $t): ?>
                                <tr>
                                    <td style="text-align:left;"><?= htmlspecialchars($t['item_name']) ?></td>
                                    <td style="text-align:right;"><b>RM<?= number_format((float)$t['total_income'], 2) ?></b></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            </table>
                        <?php endif; ?>
                        </div>
                    </div>

                    <!-- ====== Full Item Table ====== -->
                    <div class="recent-orders" style="margin-top:16px;">
                        <h2 style="display:flex; justify-content:space-between; align-items:center; gap:10px;">
                        <span><?= htmlspecialchars($label) ?> — Item Breakdown</span>
                        <small class="text-muted">Paid orders only</small>
                        </h2>

                        <table id="salesReportTable">
                        <thead>
                            <tr>
                            <th>Item Name</th>
                            <th>Unit Price</th>
                            <th>Quantity</th>
                            <th>Total Revenue</th>
                            <th>Expenses</th>
                            <th>Income</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (empty($rows)): ?>
                            <tr>
                                <td colspan="6" style="text-align:center; padding:16px;">No sales found for this period.</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($rows as $r): ?>
                                <tr>
                                <td><?= htmlspecialchars($r['item_name']) ?></td>
                                <td>RM<?= number_format((float)$r['unit_price'], 2) ?></td>
                                <td><?= number_format((int)$r['qty_sold']) ?></td>
                                <td>RM<?= number_format((float)$r['total_revenue'], 2) ?></td>
                                <td style="color:#ff6b6b;">RM<?= number_format((float)$r['total_expenses'], 2) ?></td>
                                <td style="color:#4caf50;"><b>RM<?= number_format((float)$r['total_income'], 2) ?></b></td>
                                </tr>
                            <?php endforeach; ?>

                            <tr style="background:rgba(255,255,255,0.05);">
                                <td colspan="5" style="text-align:right; padding-top:14px;">
                                <b>Total Cafe Income:</b>
                                </td>
                                <td style="padding-top:14px; color:#4caf50;">
                                <b>RM<?= number_format($grandIncome, 2) ?></b>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                        </table>
                    </div>

                    <!-- ====== Chart.js (CDN) + Export CSV ====== -->
                    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                    <script>
                        // Chart data from PHP
                        const trendLabels   = <?= json_encode($trendLabels) ?>;
                        const trendRevenue  = <?= json_encode($trendRevenue) ?>;
                        const trendExpenses = <?= json_encode($trendExpenses) ?>;
                        const trendIncome   = <?= json_encode($trendIncome) ?>;

                        const ctx = document.getElementById('trendChart');
                        if (ctx) {
                        new Chart(ctx, {
                            type: 'line',
                            data: {
                            labels: trendLabels,
                            datasets: [
                                { label: 'Revenue', data: trendRevenue, tension: 0.25 },
                                { label: 'Expenses', data: trendExpenses, tension: 0.25 },
                                { label: 'Income', data: trendIncome, tension: 0.25 },
                            ]
                            },
                            options: {
                            responsive: true,
                            plugins: {
                                legend: { display: true }
                            },
                            scales: {
                                y: { beginAtZero: true }
                            }
                            }
                        });
                        }

                        // Export CSV (from the table)
                        document.getElementById('exportCsvBtn')?.addEventListener('click', () => {
                        const table = document.getElementById('salesReportTable');
                        if (!table) return;

                        let csv = [];
                        const rows = table.querySelectorAll('tr');

                        rows.forEach(row => {
                            const cols = row.querySelectorAll('th, td');
                            const data = Array.from(cols).map(col => {
                            let text = col.innerText.replace(/\n/g, ' ').trim();
                            text = text.replace(/"/g, '""');
                            return `"${text}"`;
                            });
                            csv.push(data.join(','));
                        });

                        const blob = new Blob([csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
                        const url = URL.createObjectURL(blob);

                        const a = document.createElement('a');
                        a.href = url;
                        a.download = 'sales-report.csv';
                        document.body.appendChild(a);
                        a.click();
                        a.remove();
                        URL.revokeObjectURL(url);
                        });
                    </script>
                    </div>          
                <div id="profile"  class="tab-content <?= $activeTab === 'profile' ? 'active' : '' ?>">
                    <h1>Profile</h1>
                    <div class="profile-container">
                        <div class="profile-header">
                            <div class="profile-avatar">
                                <img src="<?= htmlspecialchars($user['profile_image'] ?: '../assets/img/Default_pfp.jpg') ?>?t=<?= time() ?>" alt="Profile Picture" id="profile-img">
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
                            src="<?= htmlspecialchars($user['profile_image'] ?: '../assets/img/Default_pfp.jpg') ?>?t=<?= time() ?>"
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
                                            <img src="<?= htmlspecialchars($fb['profile_image'] ?: '../assets/img/Default_pfp.jpg') ?>" alt="">
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
