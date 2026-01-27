<?php
session_start();
include_once "dbcon.php";
include_once "includes/header.php";

$sid = session_id();

// Get cart items
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

// Calculate total quantity for cart-badge
$stmt2 = mysqli_prepare($con, "
    SELECT SUM(quantity) as total_qty
    FROM cart_items
    WHERE session_id = ?
");
mysqli_stmt_bind_param($stmt2, "s", $sid);
mysqli_stmt_execute($stmt2);
$res2 = mysqli_stmt_get_result($stmt2);
$row2 = mysqli_fetch_assoc($res2);
$cart_quantity = $row2['total_qty'] ?? 0;

$total = 0;
?>

<!-- ================= CART PAGE ================= -->
<section class="container" style="padding:60px 16px;">
    <h2 class="section-title">Your Cart</h2>

    <?php if (mysqli_num_rows($result) == 0): ?>
        <!-- Empty cart design -->
        <div class="order-card empty-cart text-center" style="padding:40px;">
            <h4>Your cart is empty ☕</h4>
            <p>Add some delicious drinks and snacks to your cart!</p>
            <a href="menu.php" class="btn mt-3">Browse Menu</a>
        </div>
    <?php else: ?>
        <?php while ($row = mysqli_fetch_assoc($result)):
            $subtotal = $row['price'] * $row['quantity'];
            $total += $subtotal;
        ?>
        <div class="order-card mb-3">
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

<!-- ================= CART BADGE JS ================= -->
<script>
    const cartBadge = document.querySelector('.cart-badge');
    if(cartBadge){
        const qty = <?= $cart_quantity ?>;
        cartBadge.textContent = qty;
        cartBadge.style.display = qty > 0 ? 'flex' : 'none';
    }
</script>

<?php include_once "includes/footer.php"; ?>
