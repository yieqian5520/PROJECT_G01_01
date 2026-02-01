<?php
session_start();
include_once "dbcon.php";
include_once "includes/header.php";

$order = $_GET['order'] ?? '';
$order = mysqli_real_escape_string($con, $order);

// Find order by code
$res = mysqli_query($con, "
    SELECT id, order_code, status, created_at, total
    FROM orders
    WHERE order_code='$order'
    LIMIT 1
");

if (!$res || mysqli_num_rows($res) == 0) {
    echo "<h3 style='padding:40px;text-align:center;'>Order Not Found</h3>";

    // Debug (temporarily)
    // echo "<pre>SQL error: " . mysqli_error($con) . "</pre>";

    include_once "includes/footer.php";
    exit;
}

$o = mysqli_fetch_assoc($res);

// Get order items (use your actual inserted columns)
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

// Map order status to step
$statusSteps = [
    'Confirmed' => 1,
    'Preparing' => 2,
    'Ready' => 3,
];

$currentStep = $statusSteps[$o['status']] ?? 0;

?>

<link rel="stylesheet" href="styleorder.css">


<section class="os-page">

  <!-- Top header -->
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

  <!-- Step tracker -->
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

    <?php if (!empty($o['pickup_code'])): ?>
      <div class="os-pickup">
        <div>
          <h4>Pickup code</h4>
          <p><?= htmlspecialchars($o['pickup_code']) ?></p>
        </div>
      </div>
    <?php endif; ?>
  </div>

  <!-- Body -->
  <div class="os-grid">

    <!-- Items card -->
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

    <!-- Payment card -->
    <div class="card os-pay">
      <h3>Payment</h3>

      <form action="order_history.php" method="GET" class="os-pay-form">
        <input type="hidden" name="order_id" value="<?= (int)($o['id'] ?? 0) ?>">

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
            <b>RM <?= number_format((float)($o['total'] ?? 0), 2) ?></b>
          </div>

          <button type="submit" class="os-btn">Pay now</button>
        </div>
      </form>
    </div>

  </div>
</section>


<?php include_once "includes/footer.php"; ?>