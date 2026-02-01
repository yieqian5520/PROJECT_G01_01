<?php
session_start();
include_once "dbcon.php";

/* ============================
   1) LATEST ORDER REDIRECT
   MUST BE BEFORE header.php
============================ */
if (isset($_GET['latest'])) {

    if (!isset($_SESSION['auth_user']['id'])) {
        // No header included yet, so safe to echo
        echo "<h3 style='padding:40px;text-align:center;'>Please login to view your order</h3>";
        exit;
    }

    $uid = (int)$_SESSION['auth_user']['id'];

    $latest = mysqli_query($con, "
        SELECT order_code
        FROM orders
        WHERE user_id = $uid
        ORDER BY id DESC
        LIMIT 1
    ");

    if ($latest && mysqli_num_rows($latest) > 0) {
        $row = mysqli_fetch_assoc($latest);
        header("Location: order_status.php?order=" . urlencode($row['order_code']));
        exit;
    } else {
        echo "<h3 style='padding:40px;text-align:center;'>No orders found</h3>";
        exit;
    }
}

/* ============================
   2) NOW SAFE TO OUTPUT HTML
============================ */
include_once "includes/header.php";

/* ============================
   3) LOAD ORDER DETAILS
============================ */
$order = $_GET['order'] ?? '';
$order = mysqli_real_escape_string($con, $order);

$res = mysqli_query($con, "
    SELECT id, order_code, status, created_at, total, payment_status, payment_method
    FROM orders
    WHERE order_code='$order'
    LIMIT 1
");

if (!$res || mysqli_num_rows($res) == 0) {
    echo "<h3 style='padding:40px;text-align:center;'>Order Not Found</h3>";
    include_once "includes/footer.php";
    exit;
}

$o = mysqli_fetch_assoc($res);

/* ============================
   4) LOAD ORDER ITEMS
============================ */
$items_res = mysqli_query($con, "
    SELECT 
        oi.menu_name,
        oi.quantity,
        oi.price,
        oi.temp,
        oi.milk,
        oi.syrup,
        oi.addons,
        m.image
    FROM order_items oi
    LEFT JOIN menu_items m ON m.name = oi.menu_name
    WHERE oi.order_id = " . (int)$o['id'] . "
");

$items = [];
if ($items_res && mysqli_num_rows($items_res) > 0) {
    while ($row = mysqli_fetch_assoc($items_res)) {
        $img = !empty($row['image']) ? $row['image'] : 'image/default.jpg';
        $img = str_replace(' ', '%20', $img);

        $items[] = [
            'menu_name' => $row['menu_name'],
            'item_image'=> $img,
            'quantity'  => $row['quantity'],
            'price'     => $row['price'],
            'temp'      => $row['temp'] ?? '',
            'milk'      => $row['milk'] ?? '',
            'syrup'     => $row['syrup'] ?? '',
            'addons'    => $row['addons'] ?? '',
        ];
    }
}

/* ============================
   5) STATUS STEPS
============================ */
$statusSteps = [
    'Confirmed' => 1,
    'Preparing' => 2,
    'Ready' => 3,
];
$currentStep = $statusSteps[$o['status']] ?? 0;
?>

<link rel="stylesheet" href="styleorder.css">

<section class="os-page">

  <div class="os-header">
    <a class="os-back" href="javascript:history.back()">&#8592;</a>

    <div class="os-headtext">
      <h2>Order details <span>#<?= htmlspecialchars($o['order_code'] ?? 'N/A') ?></span></h2>
      <p>Date: <?= !empty($o['created_at']) ? date("d/m/Y", strtotime($o['created_at'])) : 'N/A' ?></p>
    </div>

    <div class="os-badge">
      <?= strtoupper(htmlspecialchars($o['status'] ?? '')) ?>
    </div>
  </div>

  <div class="os-tracker card">
    <div class="os-steps">

      <div class="os-step <?= $currentStep >= 1 ? 'active' : '' ?>">
        <div class="dot"><span>1</span></div>
        <div class="label">
          <b>ORDER CONFIRMED</b>
          <small><?= !empty($o['created_at']) ? date("g:i A, M j, Y", strtotime($o['created_at'])) : '' ?></small>
        </div>
      </div>

      <div class="os-line <?= $currentStep >= 2 ? 'active' : '' ?>"></div>

      <div class="os-step <?= $currentStep >= 2 ? 'active' : '' ?>">
        <div class="dot"><span>2</span></div>
        <div class="label">
          <b>PREPARING</b>
          <small>We’re making your drink</small>
        </div>
      </div>

      <div class="os-line <?= $currentStep >= 3 ? 'active' : '' ?>"></div>

      <div class="os-step <?= $currentStep >= 3 ? 'active' : '' ?>">
        <div class="dot"><span>3</span></div>
        <div class="label">
          <b>READY FOR PICKUP</b>
          <small>Collect at counter</small>
        </div>
      </div>

    </div>
  </div>

  <div class="os-grid">

    <div class="card os-items">
      <h3>Item ordered</h3>
      <div class="os-items-scroll">
      <?php if (!empty($items)): ?>
        <?php foreach ($items as $item): ?>
          <div class="os-item">
            <img src="<?= htmlspecialchars($item['item_image']) ?>" alt="<?= htmlspecialchars($item['menu_name']) ?>">
            <div class="os-item-info">
              <p class="name"><?= htmlspecialchars($item['menu_name']) ?></p>
              <p class="meta">Qty: <?= (int)$item['quantity'] ?></p>
            </div>
            <?php
              $unit = (float)$item['price'];
              $qty  = (int)$item['quantity'];
              $lineTotal = $unit * $qty;
            ?>
            <div class="os-item-price">
              RM<?= number_format($lineTotal, 2) ?>
              <small style="display:block;opacity:.7;">
                (RM<?= number_format($unit, 2) ?> × <?= $qty ?>)
              </small>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="os-empty">No items found for this order.</p>
      <?php endif; ?>
      </div>
    </div>

    <?php if (($o['payment_status'] ?? 'UNPAID') !== 'PAID'): ?>
      <div class="card os-pay">
        <h3>Payment</h3>

        <form action="pay_order.php" method="POST" class="os-pay-form">
          <input type="hidden" name="order_id" value="<?= (int)$o['id'] ?>">

          <div class="os-radio">
            <?php foreach (['Cash','TNG','Credit Card'] as $method): ?>
              <label class="radio">
                <input type="radio" name="payment_method" value="<?= $method ?>" required>
                <span><?= $method ?></span>
              </label>
            <?php endforeach; ?>
          </div>

          <div class="os-summary">
            <div class="row">
              <span>Total</span>
              <b>RM <?= number_format((float)$o['total'], 2) ?></b>
            </div>

            <button type="submit" class="os-btn">Pay now</button>
          </div>
        </form>
      </div>
    <?php else: ?>
      <div class="card os-pay" style="padding:18px;">
        <h3>Payment</h3>
        <p style="margin:0;">
          ✅ Paid<?= !empty($o['payment_method']) ? " via <b>" . htmlspecialchars($o['payment_method']) . "</b>" : "" ?>.
        </p>
      </div>
    <?php endif; ?>

  </div>
</section>

<?php include_once "includes/footer.php"; ?>
