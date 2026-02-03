<?php
session_start();
include_once "dbcon.php";
include_once "includes/header.php";

if (!isset($_SESSION['auth_user'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['auth_user']['id'];

$res = mysqli_query($con, "
    SELECT * FROM orders
    WHERE user_id = $user_id
    ORDER BY created_at DESC
");
?>

<style>
/* ===== PAGE ===== */
.orders-wrapper {
    max-width: 1100px;
    margin: auto;
}

/* ===== CARD ===== */
.orders-card {
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.08);
    padding: 25px;
}

/* ===== TABLE ===== */
.orders-table thead th {
    background: #f9fafb;
    font-weight: 600;
    font-size: 14px;
    text-transform: uppercase;
    color: #555;
    border-bottom: 2px solid #eee;
}

.orders-table tbody tr {
    transition: all 0.25s ease;
}

.orders-table tbody tr:hover {
    background: #f4fdf7;
}

.orders-table td {
    vertical-align: middle;
    font-size: 15px;
}

/* ===== STATUS ===== */
.status {
    padding: 6px 14px;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 600;
}

.status-pending { background:#fff3cd; color:#856404; }
.status-paid { background:#d4edda; color:#155724; }
.status-completed { background:#cce5ff; color:#004085; }
.status-cancelled { background:#f8d7da; color:#721c24; }

/* ===== ACTION BUTTON ===== */
.view-order {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e8f5e9;
    color: #2e7d32;
    font-size: 18px;
    transition: 0.25s;
}

.view-order:hover {
    background: #2e7d32;
    color: #fff;
}

/* ===== EMPTY STATE ===== */
.empty-orders {
    text-align: center;
    padding: 60px 20px;
    color: #777;
}

.empty-orders i {
    font-size: 48px;
    color: #ccc;
    margin-bottom: 15px;
}
</style>

<section class="container" style="padding:80px 16px;">
    <div class="orders-wrapper">

        <h2 class="section-title text-center mb-2">Order History</h2>
        <p class="text-center text-muted mb-4">
            Track your previous orders and view details anytime
        </p>

        <div class="orders-card">
            <div class="table-responsive">
                <table class="table orders-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Order Code</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Order Date</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>

                    <?php if (mysqli_num_rows($res) > 0): ?>
                        <?php while ($o = mysqli_fetch_assoc($res)):
                            $statusClass = match(strtolower($o['status'])) {
                                'pending' => 'status-pending',
                                'paid' => 'status-paid',
                                'completed' => 'status-completed',
                                'cancelled' => 'status-cancelled',
                                default => 'status-pending'
                            };
                        ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($o['order_code']) ?></strong>
                            </td>

                            <td>
                                RM <?= number_format($o['total'], 2) ?>
                            </td>

                            <td>
                                <span class="status <?= $statusClass ?>">
                                    <?= htmlspecialchars($o['status']) ?>
                                </span>
                            </td>

                            <td>
                                <?= date("d M Y", strtotime($o['created_at'])) ?><br>
                                <small class="text-muted">
                                    <?= date("h:i A", strtotime($o['created_at'])) ?>
                                </small>
                            </td>

                            <td class="text-center">
                                <a href="order_status.php?order=<?= urlencode($o['order_code']) ?>"
                                   class="view-order"
                                   title="View Order Details">
                                    <i class="bi bi-card-list"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>

                    <?php else: ?>
                        <tr>
                            <td colspan="5">
                                <div class="empty-orders">
                                    <i class="bi bi-bag-x"></i>
                                    <h5>No orders yet</h5>
                                    <p>Your order history will appear here</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<?php include_once "includes/footer.php"; ?>
