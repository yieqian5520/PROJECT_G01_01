<?php
session_start();
include_once "dbcon.php";
include_once "includes/header.php";

$sid = session_id();

$stmt = mysqli_prepare($con, "
    SELECT 
        c.id AS cart_id,
        c.quantity,
        c.temp,
        c.milk,
        c.syrup,
        c.addons,
        m.name,
        m.price,
        m.image
    FROM cart_items c
    JOIN menu_items m ON c.menu_id = m.id
    WHERE c.session_id = ?
");

mysqli_stmt_bind_param($stmt, "s", $sid);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$total = 0;
?>

<section class="container" style="padding:60px 16px;">
    <h2 class="section-title">Your Cart</h2>

    <?php if (mysqli_num_rows($result) == 0): ?>
        <p>Your cart is empty ☕</p>
    <?php else: ?>

        <?php while ($row = mysqli_fetch_assoc($result)): 
            $subtotal = $row['price'] * $row['quantity'];
            $total += $subtotal;
        ?>

        <div class="card mb-3 p-3">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <img src="<?= $row['image'] ?>" class="img-fluid rounded">
                </div>

                <div class="col-md-6">
                    <h5><?= $row['name'] ?></h5>
                    <p>
                        Temp: <?= $row['temp'] ?><br>
                        Milk: <?= $row['milk'] ?: 'None' ?><br>
                        Syrup: <?= $row['syrup'] ?: 'None' ?><br>
                        Add-ons: <?= $row['addons'] ?: 'None' ?>
                    </p>
                    <p>Quantity: <?= $row['quantity'] ?></p>
                </div>

                <div class="col-md-3 text-end">
                    <strong>RM <?= number_format($subtotal, 2) ?></strong>
                    <br><br>
                    <a href="remove_cart.php?id=<?= $row['cart_id'] ?>" 
                       class="btn btn-danger btn-sm">
                        Remove
                    </a>
                </div>
            </div>
        </div>

        <?php endwhile; ?>

        <h4 class="text-end">Total: RM <?= number_format($total, 2) ?></h4>

        <div class="text-end mt-3">
            <a href="checkout.php" class="btn btn-warning">
                Proceed to Checkout
            </a>
        </div>

    <?php endif; ?>
</section>

<?php include_once "includes/footer.php"; ?>
