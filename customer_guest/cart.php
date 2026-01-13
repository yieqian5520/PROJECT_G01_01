<?php
session_start();
include_once __DIR__ . "/dbcon.php";

// Handle update quantity
if(isset($_POST['update_cart'])) {
    $cart_id = (int)$_POST['cart_id'];
    $quantity = (int)$_POST['quantity'];

    if($quantity <= 0){
        // Remove item if quantity <= 0
        mysqli_query($con, "DELETE FROM cart_items WHERE id=$cart_id");
    } else {
        mysqli_query($con, "UPDATE cart_items SET quantity=$quantity WHERE id=$cart_id");
    }

    header("Location: cart.php");
    exit();
}

// Handle remove item
if(isset($_GET['remove'])) {
    $cart_id = (int)$_GET['remove'];
    mysqli_query($con, "DELETE FROM cart_items WHERE id=$cart_id");
    header("Location: cart.php");
    exit();
}

$session_id = session_id();
$cart_res = mysqli_query($con, "
    SELECT c.id as cart_id, c.quantity, m.name, m.price, m.image 
    FROM cart_items c 
    JOIN menu_items m ON c.menu_id = m.id 
    WHERE c.session_id='$session_id'
");

$total_price = 0;
?>

<?php include_once __DIR__ . "/includes/header.php"; ?>

<section class="register-section">
    <div class="container">
        <h2 class="section-title">Your Cart</h2>

        <?php if(mysqli_num_rows($cart_res) == 0): ?>
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
                            <th>Quantity</th>
                            <th>Subtotal (RM)</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($item = mysqli_fetch_assoc($cart_res)):
                            $subtotal = $item['price'] * $item['quantity'];
                            $total_price += $subtotal;
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($item['name']); ?></td>
                            <td>
                                <img src="<?= htmlspecialchars($item['image']); ?>" width="80" style="border-radius:10px;">
                            </td>
                            <td><?= number_format($item['price'], 2); ?></td>
                            <td>
                                <form method="POST" style="display:flex; gap:5px;">
                                    <input type="hidden" name="cart_id" value="<?= $item['cart_id']; ?>">
                                    <input type="number" name="quantity" value="<?= $item['quantity']; ?>" min="1" class="form-control" style="width:70px;">
                                    <button type="submit" name="update_cart" class="btn btn-warning">Update</button>
                                </form>
                            </td>
                            <td><?= number_format($subtotal, 2); ?></td>
                            <td>
                                <a href="cart.php?remove=<?= $item['cart_id']; ?>" class="btn btn-danger">Remove</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <tr>
                            <td colspan="4" class="text-end fw-bold">Total</td>
                            <td colspan="2" class="fw-bold">RM <?= number_format($total_price, 2); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="text-end mt-3">
                <a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>
                <a href="menu.php" class="btn btn-secondary">Continue Shopping</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include_once __DIR__ . "/includes/footer.php"; ?>
