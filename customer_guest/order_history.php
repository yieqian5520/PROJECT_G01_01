<?php
session_start();
include_once "dbcon.php";
include_once "includes/header.php";

$user_id = $_SESSION['auth_user']['id'];

$res = mysqli_query($con, "
    SELECT * FROM orders
    WHERE user_id=$user_id
    ORDER BY created_at DESC
");
?>

<section class="container" style="padding:60px 16px;">
  <h2 class="section-title">My Orders History</h2>

  <table class="table table-bordered">
    <tr>
      <th>Order Code</th>
      <th>Total</th>
      <th>Status</th>
      <th>Date</th>
    </tr>

    <?php while ($o = mysqli_fetch_assoc($res)): ?>
      <tr>
        <td><?= $o['order_code'] ?></td>
        <td>RM <?= number_format($o['total'],2) ?></td>
        <td><?= $o['status'] ?></td>
        <td><?= $o['created_at'] ?></td>
      </tr>
    <?php endwhile; ?>
  </table>
</section>

<?php include_once "includes/footer.php"; ?>
