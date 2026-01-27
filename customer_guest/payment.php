<?php include_once "includes/header.php"; ?>

<section class="register-section">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">

        <div class="register-card text-center">
          <h5>Payment</h5>

          <p>Order Code:</p>
          <h4><?= $_GET['order'] ?></h4>

          <p class="mt-3">Payment Method</p>
          <select class="form-control mb-3">
            <option>Credit / Debit Card</option>
            <option>Cash</option>
          </select>

          <a href="payment_success.php?order=<?= $_GET['order'] ?>"
             class="register-btn w-100">
            Pay Now
          </a>
        </div>

      </div>
    </div>
  </div>
</section>

<?php include_once "includes/footer.php"; ?>
