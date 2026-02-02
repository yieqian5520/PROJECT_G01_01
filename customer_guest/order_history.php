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
    <thead>
      <tr>
        <th>Order Code</th>
        <th>Total</th>
        <th>Status</th>
        <th>Date</th>
        <th>Record</th> <!-- New column -->
      </tr>
    </thead>
    <tbody>
    <?php while ($o = mysqli_fetch_assoc($res)): ?>
      <tr>
        <td><?= htmlspecialchars($o['order_code']) ?></td>
        <td>RM <?= number_format($o['total'],2) ?></td>
        <td><?= htmlspecialchars($o['status']) ?></td>
        <td><?= date("d/m/Y H:i", strtotime($o['created_at'])) ?></td>
        <td style="text-align:center;">
          <!-- Details icon -->
          <a href="order_status.php?order=<?= urlencode($o['order_code']) ?>" title="View Details">
    <i class="bi bi-card-list" style="font-size:18px;color:#2e7d32;"></i>
</a>

        </td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
</section>

<?php include_once "includes/footer.php"; ?>
