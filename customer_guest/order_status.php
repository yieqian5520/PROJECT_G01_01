<?php
include_once "dbcon.php";
include_once "includes/header.php";

if (!isset($_GET['order']) || empty($_GET['order'])) {
    echo "<h3 style='padding:40px;text-align:center;'>Invalid Order</h3>";
    include_once "includes/footer.php";
    exit;
}

$order = $_GET['order'];

$res = mysqli_query($con, "
    SELECT * FROM orders 
    WHERE order_code='$order'
    LIMIT 1
");

if (mysqli_num_rows($res) == 0) {
    echo "<h3 style='padding:40px;text-align:center;'>Order Not Found</h3>";
    include_once "includes/footer.php";
    exit;
}

$o = mysqli_fetch_assoc($res);
?>

<section class="register-section">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">

        <div class="register-card text-center">
          <h5>Order Status</h5>

          <p>Order Code</p>
          <h4><?= htmlspecialchars($o['order_code']) ?></h4>

          <h3 style="color:#ffca28; margin-top:15px;">
            <?= htmlspecialchars($o['status']) ?>
          </h3>

          <a href="order_history.php" class="btn mt-3">
            View My Orders
          </a>
        </div>

      </div>
    </div>
  </div>
</section>

<?php include_once "includes/footer.php"; ?>
