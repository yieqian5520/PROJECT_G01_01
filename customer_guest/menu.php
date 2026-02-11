<?php
// menu.php
session_start();

if (isset($_POST['order_type'])) {
  $_SESSION['order_type'] = $_POST['order_type'];
}

$orderType  = $_SESSION['order_type'] ?? 'Dine In';
$isCustomer = isset($_SESSION['authenticated']); // logged-in customer

include_once __DIR__ . "/includes/header.php";
?>
<section class="menu-hero">
  <div class="menu-hero-overlay">
    <div class="menu-hero-content">
      <h1>Our Menu</h1>
    </div>
  </div>
</section>

<div class="menu-tabs" role="tablist" aria-label="Menu Categories">
  <button class="menu-tab active" type="button" data-target="coffee" role="tab" aria-selected="true">Coffee</button>
  <button class="menu-tab" type="button" data-target="non-coffee" role="tab" aria-selected="false">Non-Coffee</button>
  <button class="menu-tab" type="button" data-target="dessert" role="tab" aria-selected="false">Dessert</button>
</div>

<!-- =================================================
     COFFEE
================================================== -->
<div class="menu-panel active" id="coffee" role="tabpanel">
  <div class="menu-grid">

    <div class="menu-item">
      <img src="image/Espresso.jpeg" alt="Espresso">
      <h3>Espresso</h3>
      <p>Strong and bold coffee shot</p>
      <span>RM 8.00</span>

      <?php if ($isCustomer): ?>
        <button type="button" class="btn open-modal"
                data-menu-id="1"
                data-name="Espresso"
                data-price="8.00"
                data-image="image/Espresso.jpeg">
          Add to Cart
        </button>
      <?php else: ?>
        <a href="login.php" class="btn" onclick="alert('Please login to add items to cart.');">
          Login to Order
        </a>
      <?php endif; ?>
    </div>

    <div class="menu-item">
      <img src="image/Black.jpeg" alt="Black">
      <h3>Black</h3>
      <p>Smooth brewed coffee with a deep, robust flavour</p>
      <span>RM 7.00</span>

      <?php if ($isCustomer): ?>
        <button type="button" class="btn open-modal"
                data-menu-id="2"
                data-name="Black"
                data-price="7.00"
                data-image="image/Black.jpeg">
          Add to Cart
        </button>
      <?php else: ?>
        <a href="login.php" class="btn" onclick="alert('Please login to add items to cart.');">
          Login to Order
        </a>
      <?php endif; ?>
    </div>

    <div class="menu-item">
      <img src="image/Latte.jpeg" alt="Latte">
      <h3>Latte</h3>
      <p>Creamy espresso blended with steamed milk</p>
      <span>RM 12.00</span>

      <?php if ($isCustomer): ?>
        <button type="button" class="btn open-modal"
                data-menu-id="3"
                data-name="Latte"
                data-price="12.00"
                data-image="image/Latte.jpeg">
          Add to Cart
        </button>
      <?php else: ?>
        <a href="login.php" class="btn" onclick="alert('Please login to add items to cart.');">
          Login to Order
        </a>
      <?php endif; ?>
    </div>

    <div class="menu-item">
      <img src="image/Flat White.jpeg" alt="Flat White">
      <h3>Flat White</h3>
      <p>Velvety micro-foamed milk over rich espresso</p>
      <span>RM 12.00</span>

      <?php if ($isCustomer): ?>
        <button type="button" class="btn open-modal"
                data-menu-id="4"
                data-name="Flat White"
                data-price="12.00"
                data-image="image/Flat White.jpeg">
          Add to Cart
        </button>
      <?php else: ?>
        <a href="login.php" class="btn" onclick="alert('Please login to add items to cart.');">
          Login to Order
        </a>
      <?php endif; ?>
    </div>

    <div class="menu-item">
      <img src="image/Cappuccino.jpeg" alt="Cappuccino">
      <h3>Cappuccino</h3>
      <p>Espresso topped with thick, airy milk foam</p>
      <span>RM 10.00</span>

      <?php if ($isCustomer): ?>
        <button type="button" class="btn open-modal"
                data-menu-id="5"
                data-name="Cappuccino"
                data-price="10.00"
                data-image="image/Cappuccino.jpeg">
          Add to Cart
        </button>
      <?php else: ?>
        <a href="login.php" class="btn" onclick="alert('Please login to add items to cart.');">
          Login to Order
        </a>
      <?php endif; ?>
    </div>

    <div class="menu-item">
      <img src="image/Mocha.jpeg" alt="Mocha">
      <h3>Mocha</h3>
      <p>Espresso mixed with chocolate and milk</p>
      <span>RM 13.00</span>

      <?php if ($isCustomer): ?>
        <button type="button" class="btn open-modal"
                data-menu-id="6"
                data-name="Mocha"
                data-price="13.00"
                data-image="image/Mocha.jpeg">
          Add to Cart
        </button>
      <?php else: ?>
        <a href="login.php" class="btn" onclick="alert('Please login to add items to cart.');">
          Login to Order
        </a>
      <?php endif; ?>
    </div>

  </div>
</div>

<!-- =================================================
     NON-COFFEE
================================================== -->
<div class="menu-panel" id="non-coffee" role="tabpanel" hidden>
  <div class="menu-grid">

    <div class="menu-item">
      <img src="image/Chocolate.jpeg" alt="Chocolate">
      <h3>Chocolate</h3>
      <p>Rich and creamy chocolate indulgence</p>
      <span>RM 11.00</span>

      <?php if ($isCustomer): ?>
        <button type="button" class="btn open-modal"
                data-menu-id="7"
                data-name="Chocolate"
                data-price="11.00"
                data-image="image/Chocolate.jpeg">
          Add to Cart
        </button>
      <?php else: ?>
        <a href="login.php" class="btn" onclick="alert('Please login to add items to cart.');">
          Login to Order
        </a>
      <?php endif; ?>
    </div>

    <div class="menu-item">
      <img src="image/Matcha.jpeg" alt="Matcha">
      <h3>Matcha</h3>
      <p>Smooth Japanese green tea blended with milk</p>
      <span>RM 12.00</span>

      <?php if ($isCustomer): ?>
        <button type="button" class="btn open-modal"
                data-menu-id="8"
                data-name="Matcha"
                data-price="12.00"
                data-image="image/Matcha.jpeg">
          Add to Cart
        </button>
      <?php else: ?>
        <a href="login.php" class="btn" onclick="alert('Please login to add items to cart.');">
          Login to Order
        </a>
      <?php endif; ?>
    </div>

    <div class="menu-item">
      <img src="image/Peach Tea.jpeg" alt="Peach Tea">
      <h3>Peach Tea</h3>
      <p>Refreshing tea with sweet peach notes</p>
      <span>RM 9.00</span>

      <?php if ($isCustomer): ?>
        <button type="button" class="btn open-modal"
                data-menu-id="9"
                data-name="Peach Tea"
                data-price="9.00"
                data-image="image/Peach Tea.jpeg">
          Add to Cart
        </button>
      <?php else: ?>
        <a href="login.php" class="btn" onclick="alert('Please login to add items to cart.');">
          Login to Order
        </a>
      <?php endif; ?>
    </div>

    <div class="menu-item">
      <img src="image/Peppermint Tea.jpeg" alt="Peppermint Tea">
      <h3>Peppermint Tea</h3>
      <p>Light and refreshing herbal mint tea</p>
      <span>RM 8.00</span>

      <?php if ($isCustomer): ?>
        <button type="button" class="btn open-modal"
                data-menu-id="10"
                data-name="Peppermint Tea"
                data-price="8.00"
                data-image="image/Peppermint Tea.jpeg">
          Add to Cart
        </button>
      <?php else: ?>
        <a href="login.php" class="btn" onclick="alert('Please login to add items to cart.');">
          Login to Order
        </a>
      <?php endif; ?>
    </div>

  </div>
</div>

<!-- =================================================
     DESSERT
================================================== -->
<div class="menu-panel" id="dessert" role="tabpanel" hidden>
  <div class="menu-grid">

    <div class="menu-item">
      <img src="image/Batik Indulgence.jpg" alt="Batik Indulgence">
      <h3>Batik Indulgence</h3>
      <p>Classic chocolate biscuit cake</p>
      <span>RM 8.00</span>

      <?php if ($isCustomer): ?>
        <button type="button" class="btn open-modal"
                data-menu-id="11"
                data-name="Batik Indulgence"
                data-price="8.00"
                data-image="image/Batik Indulgence.jpg"
                data-category="dessert">
          Add to Cart
        </button>
      <?php else: ?>
        <a href="login.php" class="btn" onclick="alert('Please login to add items to cart.');">
          Login to Order
        </a>
      <?php endif; ?>
    </div>

    <div class="menu-item">
      <img src="image/Matcha Batik Indulgence.jpg" alt="Matcha Batik Indulgence">
      <h3>Matcha Batik Indulgence</h3>
      <p>Batik cake with aromatic matcha flavour</p>
      <span>RM 9.00</span>

      <?php if ($isCustomer): ?>
        <button type="button" class="btn open-modal"
                data-menu-id="12"
                data-name="Matcha Batik Indulgence"
                data-price="9.00"
                data-image="image/Matcha Batik Indulgence.jpg"
                data-category="dessert">
          Add to Cart
        </button>
      <?php else: ?>
        <a href="login.php" class="btn" onclick="alert('Please login to add items to cart.');">
          Login to Order
        </a>
      <?php endif; ?>
    </div>

    <div class="menu-item">
      <img src="image/Biscoff Cake.jpg" alt="Biscoff Cake">
      <h3>Biscoff Cake</h3>
      <p>Soft cake layered with Biscoff spread</p>
      <span>RM 12.00</span>

      <?php if ($isCustomer): ?>
        <button type="button" class="btn open-modal"
                data-menu-id="13"
                data-name="Biscoff Cake"
                data-price="12.00"
                data-image="image/Biscoff Cake.jpg"
                data-category="dessert">
          Add to Cart
        </button>
      <?php else: ?>
        <a href="login.php" class="btn" onclick="alert('Please login to add items to cart.');">
          Login to Order
        </a>
      <?php endif; ?>
    </div>

    <div class="menu-item">
      <img src="image/Japanese Cream Puff.jpg" alt="Japanese Cream Puff">
      <h3>Japanese Cream Puff</h3>
      <p>Light pastry filled with creamy custard</p>
      <span>RM 6.00</span>

      <?php if ($isCustomer): ?>
        <button type="button" class="btn open-modal"
                data-menu-id="14"
                data-name="Japanese Cream Puff"
                data-price="6.00"
                data-image="image/Japanese Cream Puff.jpg"
                data-category="dessert">
          Add to Cart
        </button>
      <?php else: ?>
        <a href="login.php" class="btn" onclick="alert('Please login to add items to cart.');">
          Login to Order
        </a>
      <?php endif; ?>
    </div>

    <div class="menu-item">
      <img src="image/Cookies.jpg" alt="Cookies">
      <h3>Cookies</h3>
      <p>Freshly baked cookies</p>
      <span>RM 4.00</span>

      <?php if ($isCustomer): ?>
        <button type="button" class="btn open-modal"
                data-menu-id="15"
                data-name="Cookies"
                data-price="4.00"
                data-image="image/Cookies.jpg"
                data-category="dessert">
          Add to Cart
        </button>
      <?php else: ?>
        <a href="login.php" class="btn" onclick="alert('Please login to add items to cart.');">
          Login to Order
        </a>
      <?php endif; ?>
    </div>

    <div class="menu-item">
      <img src="image/Banana Choc Loaf.jpg" alt="Banana Choc Loaf">
      <h3>Banana Choc Loaf</h3>
      <p>Moist banana loaf with chocolate</p>
      <span>RM 7.00</span>

      <?php if ($isCustomer): ?>
        <button type="button" class="btn open-modal"
                data-menu-id="16"
                data-name="Banana Choc Loaf"
                data-price="7.00"
                data-image="image/Banana Choc Loaf.jpg"
                data-category="dessert">
          Add to Cart
        </button>
      <?php else: ?>
        <a href="login.php" class="btn" onclick="alert('Please login to add items to cart.');">
          Login to Order
        </a>
      <?php endif; ?>
    </div>

    <div class="menu-item">
      <img src="image/Banana Walnut Loaf.jpg" alt="Banana Walnut Loaf">
      <h3>Banana Walnut Loaf</h3>
      <p>Soft banana loaf topped with walnuts</p>
      <span>RM 8.00</span>

      <?php if ($isCustomer): ?>
        <button type="button" class="btn open-modal"
                data-menu-id="17"
                data-name="Banana Walnut Loaf"
                data-price="8.00"
                data-image="image/Banana Walnut Loaf.jpg"
                data-category="dessert">
          Add to Cart
        </button>
      <?php else: ?>
        <a href="login.php" class="btn" onclick="alert('Please login to add items to cart.');">
          Login to Order
        </a>
      <?php endif; ?>
    </div>

  </div>
</div>

<!-- =================================================
     MODAL WINDOW (customer only)
================================================== -->
<?php if ($isCustomer): ?>
<div id="cartModal" class="cart-modal" aria-hidden="true">
  <div class="cart-modal-content">
    <button type="button" class="cart-modal-close" id="closeModal">&times;</button>

    <div class="cart-modal-body">
      <img id="modalImage" src="" alt="" style="width:120px;border-radius:10px;">
      <div style="flex:1;">
        <h3 id="modalName" style="margin:0 0 6px 0;"></h3>
        <div id="modalPrice" style="margin-bottom:12px;"></div>

        <form method="POST" action="add_to_cart.php" id="addCartForm">
          <input type="hidden" name="menu_id" id="modalMenuId">

          <label style="display:block;margin-top:8px;">Quantity</label>
          <input type="number" name="quantity" value="1" min="1" class="form-control" style="max-width:140px;">

          <div id="drinkOptions">
            <label style="display:block;margin-top:12px;">Option</label>
            <select name="temp" class="form-select" style="max-width:220px;">
              <option value="Hot">Hot</option>
              <option value="Cold">Cold</option>
            </select>
          </div>

          <div id="drinkAddons">
            <label style="display:block;margin-top:12px;">Add-ons</label>
            <div style="display:grid;gap:8px;margin-top:6px;">
              <label><input type="checkbox" name="addons[]" value="Extra Shot"> Extra Shot</label>

              <div>
                <div style="font-weight:600;margin-bottom:4px;">Milk</div>
                <label><input type="radio" name="milk" value="Oat Milk"> Oat Milk</label>
                <label><input type="radio" name="milk" value="Soy Milk"> Soy Milk</label>
                <label><input type="radio" name="milk" value="Almond Milk"> Almond Milk</label>
                <label><input type="radio" name="milk" value="" checked> Normal</label>
              </div>

              <div>
                <div style="font-weight:600;margin-bottom:4px;">Syrup</div>
                <label><input type="radio" name="syrup" value="Caramel"> Caramel</label>
                <label><input type="radio" name="syrup" value="Hazelnut"> Hazelnut</label>
                <label><input type="radio" name="syrup" value="Vanilla"> Vanilla</label>
                <label><input type="radio" name="syrup" value="" checked> None</label>
              </div>
            </div>
          </div>

          <label style="display:block;margin-top:8px;">Order Type</label>
          <select name="order_type" class="form-select" style="max-width:220px;">
            <option value="Dine In" <?= $orderType=='Dine In'?'selected':'' ?>>Dine In</option>
            <option value="Take Away" <?= $orderType=='Take Away'?'selected':'' ?>>Take Away</option>
          </select>

          <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:16px;">
            <button type="button" class="btn btn-secondary" id="cancelModal">Cancel</button>
            <button type="submit" class="btn btn-warning">Add to Cart</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- =================================================
     CART DRAWER (OPEN ONLY WHEN USER CLICK CART ICON)
================================================== -->
<div id="cartDrawer" style="
  position:fixed; top:0; right:-420px; width:420px; height:100%;
  background:#fff; box-shadow:-4px 0 20px rgba(0,0,0,.2);
  transition:right .3s; z-index:99999;">
  <iframe id="cartFrame" src="cart.php" style="border:0;width:100%;height:100%;"></iframe>
</div>

<script>
  // ===== Tabs switching =====
  const tabs = document.querySelectorAll(".menu-tab");
  const panels = document.querySelectorAll(".menu-panel");

  tabs.forEach(tab => {
    tab.addEventListener("click", () => {
      const target = tab.dataset.target;
      tabs.forEach(t => { t.classList.remove("active"); t.setAttribute("aria-selected","false"); });
      panels.forEach(p => { p.classList.remove("active"); p.hidden = true; });
      tab.classList.add("active");
      tab.setAttribute("aria-selected","true");
      const panel = document.getElementById(target);
      panel.classList.add("active");
      panel.hidden = false;
    });
  });

  // ===== Modal logic =====
  const modal = document.getElementById("cartModal");
  let closeModal = ()=>{};
  if(modal){
    const modalMenuId = document.getElementById("modalMenuId");
    const modalName   = document.getElementById("modalName");
    const modalPrice  = document.getElementById("modalPrice");
    const modalImage  = document.getElementById("modalImage");

    function openModal(btn){
      modalMenuId.value = btn.dataset.menuId;
      modalName.textContent = btn.dataset.name;
      modalPrice.textContent = "RM "+btn.dataset.price;
      modalImage.src = btn.dataset.image;

      const isDessert = btn.dataset.category === 'dessert';
      document.getElementById("drinkOptions").style.display = isDessert ? "none" : "block";
      document.getElementById("drinkAddons").style.display  = isDessert ? "none" : "block";

      modal.classList.add("show");
      modal.setAttribute("aria-hidden","false");
    }

    closeModal = function(){
      modal.classList.remove("show");
      modal.setAttribute("aria-hidden","true");
    }

    document.querySelectorAll(".open-modal").forEach(btn=>btn.addEventListener("click",()=>openModal(btn)));
    document.getElementById("closeModal")?.addEventListener("click", closeModal);
    document.getElementById("cancelModal")?.addEventListener("click", closeModal);
    modal.addEventListener("click", (e)=>{ if(e.target===modal) closeModal(); });
  }

  // ===== Cart Drawer (open ONLY when user clicks cart icon) =====
  const cartDrawer = document.getElementById("cartDrawer");
  const cartFrame  = document.getElementById("cartFrame");

  function openCartDrawer(orderType){
    cartDrawer.style.right = "0";

    // send order type + refresh to iframe cart.php
    const setTypeMsg = { type:"set_type", value: orderType || "Dine In" };
    const refreshMsg = { type:"refresh" };

    let tries = 0;
    const timer = setInterval(()=>{
      tries++;
      if(cartFrame && cartFrame.contentWindow){
        cartFrame.contentWindow.postMessage(setTypeMsg, window.location.origin);
        cartFrame.contentWindow.postMessage(refreshMsg, window.location.origin);
        clearInterval(timer);
      }
      if(tries >= 10) clearInterval(timer);
    }, 80);
  }

  function closeCartDrawer(){
    cartDrawer.style.right = "-420px";
  }

  // ✅ If your header has cart button id="cartBtn"
  const cartBtn = document.getElementById("cartBtn");
  if(cartBtn){
    cartBtn.addEventListener("click", ()=>{
      const currentOrderType = <?= json_encode($orderType) ?>;
      openCartDrawer(currentOrderType);
    });
  }

  // ===== Add to Cart: update badge only (NO open drawer) =====
  const addCartForm = document.getElementById("addCartForm");
  if(addCartForm){
    addCartForm.addEventListener("submit", function(e){
      e.preventDefault();

      const formData = new FormData(this);
      const selectedOrderType = this.querySelector('select[name="order_type"]').value;

      fetch('add_to_cart.php', {
        method:'POST',
        body: formData,
        credentials:"include",
        cache:"no-store"
      })
      .then(res=>res.json())
      .then(res=>{
        if(res.status === 'success'){
          closeModal();

          // ✅ update badge only
          fetch("fetch_cart.php", {
            method:"POST",
            headers:{ "Content-Type":"application/x-www-form-urlencoded" },
            body:"order_type="+encodeURIComponent(selectedOrderType),
            credentials:"include",
            cache:"no-store"
          })
          .then(r=>r.json())
          .then(data=>{
            const cartBadge = document.getElementById("cartBadge");
            if(cartBadge) cartBadge.textContent = data.total_qty ?? 0;
          });

          // ✅ optional: tell iframe to refresh (if drawer already opened)
          if(cartFrame && cartFrame.contentWindow){
            cartFrame.contentWindow.postMessage({type:"refresh"}, window.location.origin);
          }

        } else {
          alert("Failed to add to cart");
        }
      })
      .catch(()=> alert("Network error"));
    });
  }

  // If user changes order type in modal, update session immediately (optional)
  const modalOrderTypeSelect = document.querySelector('#addCartForm select[name="order_type"]');
  modalOrderTypeSelect?.addEventListener("change", e=>{
    fetch('set_order_type.php', {
      method:'POST',
      headers:{'Content-Type':'application/x-www-form-urlencoded'},
      body:'order_type='+encodeURIComponent(e.target.value),
      credentials:"include",
      cache:"no-store"
    });
  });

</script>

<?php include_once __DIR__ . "/includes/footer.php"; ?>