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
<style>
/* Basic cart styling */
body { font-family: Arial, sans-serif; background:#f8f8f8; }
.order-shell { max-width:400px; margin:20px auto; background:#fff; border-radius:10px; box-shadow:0 4px 10px rgba(0,0,0,0.1); overflow:hidden; }
.order-top { padding:15px; font-weight:bold; font-size:18px; border-bottom:1px solid #eee; }
.order-type { display:flex; }
.order-type button { flex:1; padding:10px 0; border:none; cursor:pointer; background:#f0f0f0; font-weight:bold; }
.order-type button.active { background:#2e7d32; color:#fff; }
.order-list { padding:10px; }
.order-card { display:flex; margin-bottom:10px; background:#fafafa; border-radius:8px; padding:10px; align-items:center; }
.order-card img { width:60px; height:60px; object-fit:cover; border-radius:5px; margin-right:10px; }
.card-body { flex:1; }
.card-title { font-weight:bold; margin-bottom:5px; }
.card-price { color:#2e7d32; margin-bottom:8px; }
.qty-row { display:flex; justify-content:space-between; align-items:center; }
.qty-control { display:flex; align-items:center; }
.qty-btn { padding:4px 10px; margin:0 5px; border:none; background:#ddd; border-radius:4px; cursor:pointer; font-size:16px; }
.remove-btn { border:none; background:none; color:red; font-size:18px; cursor:pointer; }
.order-bottom { padding:15px; border-top:1px solid #eee; }
.total-row { display:flex; justify-content:space-between; font-weight:bold; margin-bottom:10px; }
.place-btn { width:100%; padding:12px; background:#2e7d32; color:#fff; border:none; border-radius:6px; font-size:16px; cursor:pointer; }
.empty { text-align:center; padding:20px; color:#888; }
</style>
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
        <button class="qty-btn minus">−</button>
        <span class="qty-num"><?= $i['quantity'] ?></span>
        <button class="qty-btn plus">+</button>
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
    <button class="place-btn" onclick="placeOrder()">Place Order</button>
<?php endif; ?>

<script>
function placeOrder() {
    // Force the entire window to navigate, not just the modal
    window.top.location.href = 'place_order.php';
}
</script>



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
