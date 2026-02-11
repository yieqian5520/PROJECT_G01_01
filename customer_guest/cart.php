<?php
session_start();
include_once "dbcon.php";

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

$sid = session_id();

/* Save order type */
if(isset($_POST['type'])){
    $_SESSION['order_type'] = $_POST['type'];
}
$type = $_SESSION['order_type'] ?? 'Dine In';

/* ===== AJAX API ===== */
if(isset($_POST['action'])){
    header('Content-Type: application/json');

    // Force order type if provided
    if(isset($_POST['type'])){
        $_SESSION['order_type'] = $_POST['type'];
        $type = $_SESSION['order_type'];
    }

    // Fetch full cart
    if($_POST['action'] === "fetch"){
        $q = mysqli_query($con,"
            SELECT c.id, c.quantity, m.name, m.price, m.image
            FROM cart_items c
            JOIN menu_items m ON c.menu_id=m.id
            WHERE c.session_id='$sid'
              AND c.order_type='$type'
            ORDER BY c.id DESC
        ");

        $items = [];
        $total = 0;

        while($r=mysqli_fetch_assoc($q)){
            $r['price'] = (float)$r['price'];
            $r['quantity'] = (int)$r['quantity'];
            $r['line'] = $r['price'] * $r['quantity'];
            $total += $r['line'];
            $items[] = $r;
        }

        echo json_encode([
            'type' => $type,
            'items' => $items,
            'total' => $total
        ]);
        exit;
    }

    // Update qty
    if($_POST['action']=="update"){
        $id  = intval($_POST['id']);
        $qty = intval($_POST['qty']);

        if($qty <= 0){
            mysqli_query($con,"DELETE FROM cart_items WHERE id=$id");
        }else{
            mysqli_query($con,"UPDATE cart_items SET quantity=$qty WHERE id=$id");
        }

        // Return refreshed cart
        $q = mysqli_query($con,"
            SELECT c.id, c.quantity, m.name, m.price, m.image
            FROM cart_items c
            JOIN menu_items m ON c.menu_id=m.id
            WHERE c.session_id='$sid'
              AND c.order_type='$type'
            ORDER BY c.id DESC
        ");

        $items = [];
        $total = 0;
        while($r=mysqli_fetch_assoc($q)){
            $r['price'] = (float)$r['price'];
            $r['quantity'] = (int)$r['quantity'];
            $r['line'] = $r['price'] * $r['quantity'];
            $total += $r['line'];
            $items[] = $r;
        }

        echo json_encode(['type'=>$type,'items'=>$items,'total'=>$total]);
        exit;
    }

    // Remove
    if($_POST['action']=="remove"){
        $id = intval($_POST['id']);
        mysqli_query($con,"DELETE FROM cart_items WHERE id=$id");

        // Return refreshed cart
        $q = mysqli_query($con,"
            SELECT c.id, c.quantity, m.name, m.price, m.image
            FROM cart_items c
            JOIN menu_items m ON c.menu_id=m.id
            WHERE c.session_id='$sid'
              AND c.order_type='$type'
            ORDER BY c.id DESC
        ");

        $items = [];
        $total = 0;
        while($r=mysqli_fetch_assoc($q)){
            $r['price'] = (float)$r['price'];
            $r['quantity'] = (int)$r['quantity'];
            $r['line'] = $r['price'] * $r['quantity'];
            $total += $r['line'];
            $items[] = $r;
        }

        echo json_encode(['type'=>$type,'items'=>$items,'total'=>$total]);
        exit;
    }

    echo json_encode(['error'=>'unknown action']);
    exit;
}

/* ===== Normal page load ===== */
$q = mysqli_query($con,"
    SELECT c.id,c.quantity,m.name,m.price,m.image
    FROM cart_items c
    JOIN menu_items m ON c.menu_id=m.id
    WHERE c.session_id='$sid'
      AND c.order_type='$type'
    ORDER BY c.id DESC
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
body { margin:0; font-family: Arial, sans-serif; background:#fff; }

/* ✅ Qty control design (match modern rounded look) */
.qty-control{
  display:inline-flex;
  align-items:center;
  gap:10px;
  padding:6px 10px;
  border:1px solid rgba(0,0,0,.12);
  border-radius:999px;
  background:#fff;
}
.qty-btn{
  width:30px;
  height:30px;
  border-radius:999px;
  border:1px solid rgba(0,0,0,.12);
  background:#1f1f1f;
  font-size:18px;
  line-height:1;
  cursor:pointer;
  display:flex;
  align-items:center;
  justify-content:center;
  user-select:none;
  transition:transform .05s ease, background .15s ease;
}
.qty-btn:active{ transform:scale(.96); }
.qty-btn:hover{ background:#efefef; }
.qty-btn:disabled{
  opacity:.55;
  cursor:not-allowed;
}

.qty-num{
  min-width:24px;
  text-align:center;
  font-weight:700;
}

.remove-btn{
  border:none;
  background:transparent;
  cursor:pointer;
  font-size:16px;
  opacity:.85;
}
.remove-btn:hover{ opacity:1; }

.order-card img{
  object-fit:cover;
}
</style>
</head>

<body>
<div class="order-shell">
  <div class="order-top">Your Order (<span id="cartTypeLabel"><?= htmlspecialchars($type) ?></span>)</div>

  <form method="POST" class="order-type" id="orderTypeForm">
    <button type="button" data-type="Dine In" class="<?= $type=='Dine In'?'active':'' ?>">Dine In</button>
    <button type="button" data-type="Take Away" class="<?= $type=='Take Away'?'active':'' ?>">Take Away</button>
  </form>

  <div class="order-list" id="orderList">
    <?php if(!$items): ?>
      <p class="empty">Your cart is empty ☕</p>
    <?php endif; ?>

    <?php foreach($items as $i): ?>
      <div class="order-card" data-id="<?= (int)$i['id'] ?>">
        <img src="<?= htmlspecialchars($i['image']) ?>">

        <div class="card-body">
          <div class="card-title"><?= htmlspecialchars($i['name']) ?></div>
          <div class="card-price">
            RM <span class="line-total"><?= number_format($i['line'],2) ?></span>
          </div>

          <div class="qty-row">
            <div class="qty-control">
              <button type="button" class="qty-btn minus">−</button>
              <span class="qty-num"><?= (int)$i['quantity'] ?></span>
              <button type="button" class="qty-btn plus">+</button>
            </div>
            <button type="button" class="remove-btn">✖</button>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="order-bottom">
    <div class="total-row">
      <span>Total</span>
      <strong>RM <span id="cartTotal"><?= number_format($total,2) ?></span></strong>
    </div>

<button class="place-btn" type="button" onclick="placeOrder()">
    Place Order
</button>

<script>
function placeOrder() {
    // Redirect the **full browser window** to place_order.php
    window.top.location.href = 'place_order.php';
}
</script>



<script>
const orderList = document.getElementById("orderList");
const cartTotal = document.getElementById("cartTotal");
const cartTypeLabel = document.getElementById("cartTypeLabel");
const orderTypeForm = document.getElementById("orderTypeForm");

let currentType = cartTypeLabel.textContent || "Dine In";
let isBusy = false;
let lastSnapshot = ""; // prevent rerender when no change

function bindCardButtons(){
  document.querySelectorAll(".order-card").forEach(card=>{
    const id = card.dataset.id;
    card.querySelector(".plus").onclick  = ()=>updateQty(id, +1);
    card.querySelector(".minus").onclick = ()=>updateQty(id, -1);
    card.querySelector(".remove-btn").onclick = ()=>removeItem(id);
  });
}

function refreshCart(type){
  if(isBusy) return;
  isBusy = true;

  const body = new URLSearchParams();
  body.append("action","fetch");
  if(type) body.append("type", type);

  fetch("cart.php",{
    method:"POST",
    headers:{"Content-Type":"application/x-www-form-urlencoded"},
    body: body.toString(),
    credentials:"include",
    cache:"no-store"
  })
  .then(r=>r.json())
  .then(data=>{
    const snap = JSON.stringify(data);
    if(snap !== lastSnapshot){
      lastSnapshot = snap;
      renderCart(data);
    }
  })
  .finally(()=> isBusy = false);
}

function renderCart(data){
  if(data.type){
    currentType = data.type;
    cartTypeLabel.textContent = data.type;
    orderTypeForm.querySelectorAll("button").forEach(b=>{
      b.classList.toggle("active", b.dataset.type === data.type);
    });
  }

  if(!data.items || data.items.length === 0){
    orderList.innerHTML = '<p class="empty">Your cart is empty ☕</p>';
    cartTotal.textContent = "0.00";
    return;
  }

  let html = "";
  data.items.forEach(i=>{
    html += `
      <div class="order-card" data-id="${i.id}">
        <img src="${i.image}">
        <div class="card-body">
          <div class="card-title">${escapeHtml(i.name)}</div>
          <div class="card-price">RM <span class="line-total">${Number(i.line).toFixed(2)}</span></div>
          <div class="qty-row">
            <div class="qty-control">
              <button type="button" class="qty-btn minus">−</button>
              <span class="qty-num">${i.quantity}</span>
              <button type="button" class="qty-btn plus">+</button>
            </div>
            <button type="button" class="remove-btn">✖</button>
          </div>
        </div>
      </div>
    `;
  });

  orderList.innerHTML = html;
  cartTotal.textContent = Number(data.total).toFixed(2);
  bindCardButtons();
}

function setButtonsDisabled(card, disabled){
  if(!card) return;
  card.querySelectorAll("button").forEach(b=> b.disabled = disabled);
}

function updateQty(id,change){
  const card = document.querySelector(`.order-card[data-id="${id}"]`);
  const qtyEl = card?.querySelector(".qty-num");
  if(!qtyEl) return;

  let qty = parseInt(qtyEl.textContent || "0") + change;
  if(qty < 0) qty = 0;

  setButtonsDisabled(card, true);

  fetch("cart.php",{
    method:"POST",
    headers:{"Content-Type":"application/x-www-form-urlencoded"},
    body:`action=update&type=${encodeURIComponent(currentType)}&id=${id}&qty=${qty}`,
    credentials:"include",
    cache:"no-store"
  })
  .then(r=>r.json())
  .then(data=>{
    lastSnapshot = ""; // force update
    renderCart(data);
  })
  .finally(()=> setButtonsDisabled(card, false));
}

function removeItem(id){
  const card = document.querySelector(`.order-card[data-id="${id}"]`);
  setButtonsDisabled(card, true);

  fetch("cart.php",{
    method:"POST",
    headers:{"Content-Type":"application/x-www-form-urlencoded"},
    body:`action=remove&type=${encodeURIComponent(currentType)}&id=${id}`,
    credentials:"include",
    cache:"no-store"
  })
  .then(r=>r.json())
  .then(data=>{
    lastSnapshot = "";
    renderCart(data);
  })
  .finally(()=> setButtonsDisabled(card, false));
}

// ✅ change type WITHOUT reload
orderTypeForm.addEventListener("click", function(e){
  const btn = e.target.closest("button");
  if(!btn) return;
  currentType = btn.dataset.type;
  lastSnapshot = "";
  refreshCart(currentType);
});

// ✅ real-time polling (every 1 second)
setInterval(()=> refreshCart(currentType), 1000);

// ✅ allow menu.php to push refresh (optional)
window.addEventListener("message", (e)=>{
  if(e.origin !== window.location.origin) return;
  if(!e.data) return;

  if(e.data.type === "set_type"){
    currentType = e.data.value || "Dine In";
    lastSnapshot = "";
    refreshCart(currentType);
  }
  if(e.data.type === "refresh"){
    lastSnapshot = "";
    refreshCart(currentType);
  }
});

function escapeHtml(str){
  return String(str)
    .replaceAll("&","&amp;")
    .replaceAll("<","&lt;")
    .replaceAll(">","&gt;")
    .replaceAll('"',"&quot;")
    .replaceAll("'","&#039;");
}

bindCardButtons();

document.getElementById('placeOrderBtn')?.addEventListener('click', function() {
    // Close the cart drawer first (optional)
    if (window.parent && window.parent.closeCart) {
        window.parent.closeCart();
    }
    // Redirect main window to order_status.php
    window.parent.location.href = 'order_status.php?latest=1';
});

</script>
</body>
</html>