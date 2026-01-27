<?php
session_start();
include_once "dbcon.php";
include_once "includes/header.php";

$sid = session_id();
$user_id = $_SESSION['auth_user']['id'] ?? 0;

if (!$user_id) {
    header("Location: login.php");
    exit;
}

$stmt = mysqli_prepare($con, "
    SELECT c.*, m.name, m.price
    FROM cart_items c
    JOIN menu_items m ON c.menu_id = m.id
    WHERE c.session_id = ?
");
mysqli_stmt_bind_param($stmt, "s", $sid);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$total = 0;
?>

<section class="register-section">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">

        <div class="register-card">
          <h5>Confirm Your Order</h5>

          <?php while ($row = mysqli_fetch_assoc($result)):
            $sub = $row['price'] * $row['quantity'];
            $total += $sub;
          ?>
            <p>
              <?= $row['name'] ?> × <?= $row['quantity'] ?>
              <span style="float:right;">RM <?= number_format($sub,2) ?></span>
            </p>
          <?php endwhile; ?>

          <hr>
          <h5>Total: RM <?= number_format($total,2) ?></h5>

          <form action="place_order.php" method="POST">
            <button class="register-btn w-100 mt-3">
              Place Order
            </button>
          </form>
        </div>

      </div>
    </div>
  </div>
</section>

<?php include_once "includes/footer.php"; ?>
