<?php
// cart.php
session_start();
include_once __DIR__ . "/dbcon.php";

/* ==========================
   BULK UPDATE
========================== */
if (isset($_POST['update_cart_all'])) {
    $sid = session_id();
    $qtyMap = $_POST['qty'] ?? [];

    if (is_array($qtyMap)) {
        foreach ($qtyMap as $cart_id => $quantity) {
            $cart_id  = (int)$cart_id;
            $quantity = (int)$quantity;

            if ($cart_id <= 0) continue;

            if ($quantity <= 0) {
                $stmt = mysqli_prepare($con, "DELETE FROM cart_items WHERE id=? AND session_id=?");
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "is", $cart_id, $sid);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                }
            } else {
                $stmt = mysqli_prepare($con, "UPDATE cart_items SET quantity=? WHERE id=? AND session_id=?");
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "iis", $quantity, $cart_id, $sid);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                }
            }
        }
    }

    header("Location: cart.php");
    exit();
}

/* ==========================
   REMOVE ITEM
========================== */
if (isset($_GET['remove'])) {
    $cart_id = (int)($_GET['remove'] ?? 0);
    $sid = session_id();

    if ($cart_id > 0) {
        $stmt = mysqli_prepare($con, "DELETE FROM cart_items WHERE id=? AND session_id=?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "is", $cart_id, $sid);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    header("Location: cart.php");
    exit();
}

/* ==========================
   FETCH CART (with options)
========================== */
$session_id = session_id();

$sql = "
    SELECT c.id AS cart_id, c.quantity, m.name, m.price, m.image,
           c.temp, c.milk, c.syrup, c.addons
    FROM cart_items c
    JOIN menu_items m ON c.menu_id = m.id
    WHERE c.session_id = ?
    ORDER BY c.added_at DESC
";

$stmt = mysqli_prepare($con, $sql);
if (!$stmt) {
    die("SQL prepare failed: " . mysqli_error($con));
}
mysqli_stmt_bind_param($stmt, "s", $session_id);
mysqli_stmt_execute($stmt);
$cart_res = mysqli_stmt_get_result($stmt);

$total = 0;
?>

<?php include_once __DIR__ . "/includes/header.php"; ?>

<section class="register-section">
  <div class="container">
    <h2 class="section-title">Your Cart</h2>

    <?php if (!$cart_res || mysqli_num_rows($cart_res) == 0): ?>
      <div class="alert alert-info text-center">
        Your cart is empty. <a href="menu.php">Go to Menu</a>
      </div>
    <?php else: ?>

      <form method="POST">
        <div class="table-responsive">
          <table class="table table-bordered align-middle">
            <thead class="table-dark">
              <tr>
                <th>Item</th>
                <th>Image</th>
                <th>Price (RM)</th>
                <th>Qty</th>
                <th>Option</th>
                <th>Add-ons</th>
                <th>Subtotal (RM)</th>
              </tr>
            </thead>

            <tbody>
              <?php while ($row = mysqli_fetch_assoc($cart_res)):
                $price  = (float)$row['price'];
                $qty    = (int)$row['quantity'];
                $sub    = $price * $qty;
                $total += $sub;
                $cartId = (int)$row['cart_id'];

                // Prepare option strings
                $temp = htmlspecialchars($row['temp'] ?? '');
                $milk = htmlspecialchars($row['milk'] ?? '');
                $syrup = htmlspecialchars($row['syrup'] ?? '');
                $addons = htmlspecialchars($row['addons'] ?? '');
              ?>
                <tr>
                  <td><?= htmlspecialchars($row['name']); ?></td>

                  <td>
                    <img src="<?= htmlspecialchars($row['image']); ?>" width="80" style="border-radius:10px;">
                  </td>

                  <td><?= number_format($price, 2); ?></td>

                  <td style="max-width:120px;">
                    <input
                      type="number"
                      name="qty[<?= $cartId ?>]"
                      value="<?= $qty ?>"
                      min="1"
                      class="form-control"
                    >
                  </td>

                  <td>
                    <?= $temp ?>
                    <?php if ($milk): ?>
                      <br><small>Milk: <?= $milk ?></small>
                    <?php endif; ?>
                    <?php if ($syrup): ?>
                      <br><small>Syrup: <?= $syrup ?></small>
                    <?php endif; ?>
                  </td>

                  <td>
                    <?= $addons ?>
                  </td>

                  <td><?= number_format($sub, 2); ?></td>
                </tr>

                <!-- Remove button outside table row -->
                <tr>
                  <td colspan="7" class="text-end">
                    <a href="cart.php?remove=<?= $cartId; ?>" class="btn btn-danger btn-sm">
                      Remove
                    </a>
                  </td>
                </tr>

              <?php endwhile; ?>

              <tr>
                <td colspan="5" class="text-end">Total</td>
                <td colspan="2">RM <?= number_format($total, 2); ?></td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-3">
          <button type="submit" name="update_cart_all" class="btn btn-warning">
            Update Cart
          </button>

          <a href="menu.php" class="btn btn-secondary">
            Continue Shopping
          </a>
        </div>
      </form>

    <?php endif; ?>
  </div>
</section>

<?php include_once __DIR__ . "/includes/footer.php"; ?>