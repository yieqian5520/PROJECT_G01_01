<?php
// menu.php
session_start();
include_once __DIR__ . "/includes/header.php";

$isCustomer = isset($_SESSION['authenticated']); // logged-in customer
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
                data-image="image/Batik Indulgence.jpg">
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
                data-image="image/Matcha Batik Indulgence.jpg">
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
                data-image="image/Biscoff Cake.jpg">
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
                data-image="image/Japanese Cream Puff.jpg">
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
                data-image="image/Cookies.jpg">
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
                data-image="image/Banana Choc Loaf.jpg">
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
                data-image="image/Banana Walnut Loaf.jpg">
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

          <label style="display:block;margin-top:12px;">Option</label>
          <select name="temp" class="form-select" style="max-width:220px;">
            <option value="Hot">Hot</option>
            <option value="Cold">Cold</option>
          </select>

          <label style="display:block;margin-top:12px;">Add-ons</label>
          <div style="display:grid;gap:8px;margin-top:6px;">
            <label><input type="checkbox" name="addons[]" value="Extra Shot"> Extra Shot</label>

            <div>
              <div style="font-weight:600;margin-bottom:4px;">Milk</div>
              <label style="margin-right:12px;"><input type="radio" name="milk" value="Oat Milk"> Oat Milk</label>
              <label style="margin-right:12px;"><input type="radio" name="milk" value="Soy Milk"> Soy Milk</label>
              <label style="margin-right:12px;"><input type="radio" name="milk" value="Almond Milk"> Almond Milk</label>
              <label><input type="radio" name="milk" value="" checked> Normal</label>
            </div>

            <div>
              <div style="font-weight:600;margin-bottom:4px;">Syrup</div>
              <label style="margin-right:12px;"><input type="radio" name="syrup" value="Caramel"> Caramel</label>
              <label style="margin-right:12px;"><input type="radio" name="syrup" value="Hazelnut"> Hazelnut</label>
              <label style="margin-right:12px;"><input type="radio" name="syrup" value="Vanilla"> Vanilla</label>
              <label><input type="radio" name="syrup" value="" checked> None</label>
            </div>
          </div>

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
     MINI CART (ORDER-SHELL STYLE)
================================================== -->
<div id="miniCart" class="mini-cart" style="display:none;">
  <div class="mini-cart-header">
    <h4>Your Order</h4>
    <button id="closeMiniCart">&times;</button>
  </div>
  <div id="miniCartBody"></div>
  <div class="mini-cart-footer">
    <strong>Total: RM <span id="miniCartTotal">0.00</span></strong>
    <a href="order_status.php?latest=1" class="btn btn-warning w-100 mt-2">Place Order</a>
  </div>
</div>

<style>
.mini-cart {
  position: fixed;
  top: 0;
  right: -400px;
  width: 400px;
  height: 100%;
  background: #fff;
  box-shadow: -4px 0 20px rgba(0,0,0,.2);
  transition: right 0.3s;
  z-index: 9999;
  display: flex;
  flex-direction: column;
}
.mini-cart.show { right: 0; }
.mini-cart-header { display:flex; justify-content:space-between; align-items:center; padding:10px; border-bottom:1px solid #ddd; }
#miniCartBody { flex:1; overflow-y:auto; padding:10px; }
.mini-cart-footer { padding:10px; border-top:1px solid #ddd; }
#closeMiniCart { background:none; border:none; font-size:1.5rem; cursor:pointer; }
</style>

<script>
  // Tabs switching
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

  // Modal logic
  const modal = document.getElementById("cartModal");
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
      modal.classList.add("show");
      modal.setAttribute("aria-hidden","false");
    }

    function closeModal(){
      modal.classList.remove("show");
      modal.setAttribute("aria-hidden","true");
    }

    document.querySelectorAll(".open-modal").forEach(btn=>btn.addEventListener("click",()=>openModal(btn)));
    document.getElementById("closeModal").addEventListener("click", closeModal);
    document.getElementById("cancelModal").addEventListener("click", closeModal);
    modal.addEventListener("click", (e)=>{ if(e.target===modal) closeModal(); });
  }

  // Mini-cart
  const miniCart = document.getElementById("miniCart");
  const miniCartBody = document.getElementById("miniCartBody");
  const miniCartTotal = document.getElementById("miniCartTotal");
  document.getElementById("closeMiniCart").addEventListener("click", ()=> miniCart.classList.remove("show"));

  function showMiniCart(){
    fetch('fetch_cart.php').then(res=>res.json()).then(data=>{
      miniCartBody.innerHTML = '';
      let total=0;
      data.items.forEach(item=>{
        let line=item.price*item.quantity;
        total+=line;
        miniCartBody.innerHTML+=`
          <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
            <div>
              <strong>${item.name}</strong><br>
              ${item.quantity} x RM ${item.price.toFixed(2)}
            </div>
            <div>RM ${line.toFixed(2)}</div>
          </div>`;
      });
      miniCartTotal.textContent = total.toFixed(2);
      miniCart.classList.add("show");
    });
  }

  const addCartForm = document.getElementById("addCartForm");
  if(addCartForm){
    addCartForm.addEventListener("submit", function(e){
      e.preventDefault();
      const formData = new FormData(this);
      fetch('add_to_cart.php',{
        method:'POST',
        body:formData
      }).then(res=>res.json())
        .then(res=>{
          if(res.status==='success'){
            showMiniCart();
            closeModal();
          }
        });
    });
  }
</script>

<?php include_once __DIR__ . "/includes/footer.php"; ?>
