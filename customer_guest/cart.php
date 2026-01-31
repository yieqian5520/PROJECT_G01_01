<?php
session_start();
include_once "dbcon.php";

$sid = session_id();

/* AJAX update & remove */
if(isset($_POST['action'])){
    $id = intval($_POST['id']);

    if($_POST['action']=="update"){
        $qty = intval($_POST['qty']);
        if($qty <= 0){
            mysqli_query($con,"DELETE FROM cart_items WHERE id=$id");
        }else{
            mysqli_query($con,"UPDATE cart_items SET quantity=$qty WHERE id=$id");
        }
        exit;
    }

    if($_POST['action']=="remove"){
        mysqli_query($con,"DELETE FROM cart_items WHERE id=$id");
        exit;
    }
}

/* Order type */
if(isset($_POST['type'])){
    $_SESSION['order_type'] = $_POST['type'];
}
$type = $_SESSION['order_type'] ?? 'Dine In';

/* Fetch cart */
$q = mysqli_query($con,"
SELECT c.id,c.quantity,m.name,m.price,m.image
FROM cart_items c
JOIN menu_items m ON c.menu_id=m.id
WHERE c.session_id='$sid'
");

$total = 0;
$items = [];
while($r = mysqli_fetch_assoc($q)){
    $r['line'] = $r['price'] * $r['quantity'];
    $total += $r['line'];
    $items[] = $r;
}
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Your Cart</title>
<link rel="stylesheet" href="style1.css">
</head>

<body>

<div class="order-shell">

<div class="order-top">Your Order</div>

<form method="POST" class="order-type">
  <button name="type" value="Dine In" class="<?= $type=='Dine In'?'active':'' ?>">Dine In</button>
  <button name="type" value="Take Away" class="<?= $type=='Take Away'?'active':'' ?>">Take Away</button>
</form>

<div class="order-list">
<?php if(!$items): ?>
<p class="empty">Your cart is empty ☕</p>
<?php endif; ?>

<?php foreach($items as $i): ?>
<div class="order-card" data-id="<?= $i['id'] ?>">
  <img src="<?= $i['image'] ?>">

  <div class="card-body">
    <div class="card-title"><?= $i['name'] ?></div>
    <div class="card-price">RM <?= number_format($i['line'],2) ?></div>

    <div class="qty-row">
      <div class="qty-control">
        <button class="qty-btn minus"><i class="bi bi-dash"></i></button>

        <span class="qty-num"><?= $i['quantity'] ?></span>
        <button class="qty-btn plus"><i class="bi bi-plus"></i></button>
      </div>

      <button class="remove-btn">✖</button>
    </div>
  </div>
</div>
<?php endforeach; ?>
</div>

<div class="order-bottom">
  <div class="total-row">
    <span>Total</span>
    <strong>RM <?= number_format($total,2) ?></strong>
  </div>

  <?php if($items): ?>
  <form action="place_order.php" method="POST">
      <button class="place-btn">Place Order</button>
  </form>
  <?php endif; ?>
</div>

</div>

<script>
document.querySelectorAll(".order-card").forEach(card=>{
  const id = card.dataset.id;

  card.querySelector(".plus").onclick = ()=>update(id,1);
  card.querySelector(".minus").onclick = ()=>update(id,-1);
  card.querySelector(".remove-btn").onclick = ()=>removeItem(id);
});

function update(id,change){
  const qtyEl=document.querySelector(`[data-id="${id}"] .qty-num`);
  let qty=parseInt(qtyEl.innerText)+change;
  if(qty<0)qty=0;

  fetch("cart.php",{
    method:"POST",
    headers:{"Content-Type":"application/x-www-form-urlencoded"},
    body:`action=update&id=${id}&qty=${qty}`
  }).then(()=>location.reload());
}

function removeItem(id){
  fetch("cart.php",{
    method:"POST",
    headers:{"Content-Type":"application/x-www-form-urlencoded"},
    body:`action=remove&id=${id}`
  }).then(()=>location.reload());
}
</script>

</body>
</html>
