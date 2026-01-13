<?php
session_start();
include_once __DIR__ . "/dbcon.php";

// update quantity
if (isset($_POST['update_cart'])) {
    $cart_id  = (int)($_POST['cart_id'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 1);
    $sid = session_id();

    if ($cart_id > 0) {
        if ($quantity <= 0) {
            $stmt = mysqli_prepare($con, "DELETE FROM cart_items WHERE id=? AND session_id=?");
            mysqli_stmt_bind_param($stmt, "is", $cart_id, $sid);
            mysqli_stmt_execute($stmt);
        } else {
            $stmt = mysqli_prepare($con, "UPDATE cart_items SET quantity=? WHERE id=? AND session_id=?");
            mysqli_stmt_bind_param($stmt, "iis", $quantity, $cart_id, $sid);
            mysqli_stmt_execute($stmt);
        }
    }

    header("Location: cart.php");
    exit();
}

// remove item
if (isset($_GET['remove'])) {
    $cart_id = (int)($_GET['remove'] ?? 0);
    $sid = session_id();

    if ($cart_id > 0) {
        $stmt = mysqli_prepare($con, "DELETE FROM cart_items WHERE id=? AND session_id=?");
        mysqli_stmt_bind_param($stmt, "is", $cart_id, $sid);
        mysqli_stmt_execute($stmt);
    }

    header("Location: cart.php");
    exit();
}

$session_id = session_id();

$sql = "
    SELECT c.id AS cart_id, c.quantity, m.name, m.price, m.image
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
      <div class="table-responsive">
        <table class="table table-bordered align-middle">
          <thead class="table-dark">
            <tr>
              <th>Item</th>
              <th>Image</th>
              <th>Price (RM)</th>
              <th>Qty</th>
              <th>Subtotal (RM)</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = mysqli_fetch_assoc($cart_res)):
              $price = (float)$row['price'];
              $qty = (int)$row['quantity'];
              $sub = $price * $qty;
              $total += $sub;
            ?>
              <tr>
                <td><?= htmlspecialchars($row['name']); ?></td>
                <td>
                  <img src="<?= htmlspecialchars($row['image']); ?>" width="80" style="border-radius:10px;">
                </td>
                <td><?= number_format($price, 2); ?></td>
                <td>
                  <form method="POST" style="display:flex; gap:6px;">
                    <input type="hidden" name="cart_id" value="<?= (int)$row['cart_id']; ?>">
                    <input type="number" name="quantity" value="<?= $qty; ?>" min="1" class="form-control" style="width:90px;">
                    <button type="submit" name="update_cart" class="btn btn-warning">Update</button>
                  </form>
                </td>
                <td><?= number_format($sub, 2); ?></td>
                <td>
                  <a class="btn btn-danger" href="cart.php?remove=<?= (int)$row['cart_id']; ?>">-</a>
                </td>
              </tr>
            <?php endwhile; ?>
            
            <tr>
              <td colspan="4" class="text-end">Total</td>
              <td colspan="2">RM <?= number_format($total, 2); ?></td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="text-end mt-3">
        <a href="menu.php" class="btn btn-secondary">Continue Shopping</a>
      </div>
    <?php endif; ?>
  </div>
</section>



<?php include_once __DIR__ . "/includes/footer.php"; ?>