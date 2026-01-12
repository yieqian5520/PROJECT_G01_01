<?php
session_start();
include_once __DIR__ . "/includes/header.php";
?>

<section class="about-hero">
    <div class="about-hero-overlay">
        <div class="about-hero-content">
            <h1>Our Menu </h1>
        </div>
    </div>
</section>


  <!-- Tabs -->
  <div class="menu-tabs" role="tablist" aria-label="Menu Categories">
    <button class="menu-tab active" type="button" data-target="drinks" role="tab" aria-selected="true">Drinks</button>
    <button class="menu-tab" type="button" data-target="food" role="tab" aria-selected="false">Food</button>
    <button class="menu-tab" type="button" data-target="dessert" role="tab" aria-selected="false">Dessert</button>
  </div>

  <!-- DRINKS -->
  <div class="menu-panel active" id="drinks" role="tabpanel">
    <div class="menu-grid">
      <div class="menu-item">
        <img src="image/background2.jpg" alt="Espresso">
        <h3>Espresso</h3>
        <p>Strong and bold coffee shot</p>
        <span>RM 8.00</span>
        <a href="#" class="btn">add to cart</a>
      </div>

      <div class="menu-item">
        <img src="image/background2.jpg" alt="Cappuccino">
        <h3>Cappuccino</h3>
        <p>Milk foam with rich espresso</p>
        <span>RM 10.00</span>
        <a href="#" class="btn">add to cart</a>
      </div>

      <div class="menu-item">
        <img src="image/background2.jpg" alt="Iced Latte">
        <h3>Iced Latte</h3>
        <p>Chilled coffee with milk</p>
        <span>RM 12.00</span>
        <a href="#" class="btn">add to cart</a>
      </div>
    </div>
  </div>

  <!-- FOOD -->
  <div class="menu-panel" id="food" role="tabpanel" hidden>
    <div class="menu-grid">
      <div class="menu-item">
        <img src="image/background2.jpg" alt="Chicken Wrap">
        <h3>Chicken Wrap</h3>
        <p>Grilled chicken with fresh veggies</p>
        <span>RM 14.00</span>
        <a href="#" class="btn">add to cart</a>
      </div>

      <div class="menu-item">
        <img src="image/background2.jpg" alt="Breakfast Sandwich">
        <h3>Breakfast Sandwich</h3>
        <p>Egg, cheese & turkey slice</p>
        <span>RM 13.00</span>
        <a href="#" class="btn">add to cart</a>
      </div>

      <div class="menu-item">
        <img src="image/background2.jpg" alt="Pasta">
        <h3>Creamy Pasta</h3>
        <p>Rich creamy sauce with herbs</p>
        <span>RM 18.00</span>
        <a href="#" class="btn">add to cart</a>
      </div>
    </div>
  </div>

  <!-- DESSERT -->
  <div class="menu-panel" id="dessert" role="tabpanel" hidden>
    <div class="menu-grid">
      <div class="menu-item">
        <img src="image/background2.jpg" alt="Cheesecake">
        <h3>Cheesecake</h3>
        <p>Creamy classic cheesecake</p>
        <span>RM 12.00</span>
        <a href="#" class="btn">add to cart</a>
      </div>

      <div class="menu-item">
        <img src="image/background2.jpg" alt="Brownie">
        <h3>Chocolate Brownie</h3>
        <p>Soft and fudgy chocolate</p>
        <span>RM 9.00</span>
        <a href="#" class="btn">add to cart</a>
      </div>

      <div class="menu-item">
        <img src="image/background2.jpg" alt="Croissant">
        <h3>Butter Croissant</h3>
        <p>Flaky and buttery</p>
        <span>RM 7.00</span>
        <a href="#" class="btn">add to cart</a>
      </div>

      <div class="menu-item">
        <img src="image/background2.jpg" alt="Croissant">
        <h3>Butter Croissant</h3>
        <p>Flaky and buttery</p>
        <span>RM 7.00</span>
        <a href="#" class="btn">add to cart</a>
      </div>

      <div class="menu-item">
        <img src="image/background2.jpg" alt="Croissant">
        <h3>Butter Croissant</h3>
        <p>Flaky and buttery</p>
        <span>RM 7.00</span>
        <a href="#" class="btn">add to cart</a>
      </div>

      <div class="menu-item">
        <img src="image/background2.jpg" alt="Croissant">
        <h3>Butter Croissant</h3>
        <p>Flaky and buttery</p>
        <span>RM 7.00</span>
        <a href="#" class="btn">add to cart</a>
      </div>

      <div class="menu-item">
        <img src="image/background2.jpg" alt="Croissant">
        <h3>Butter Croissant</h3>
        <p>Flaky and buttery</p>
        <span>RM 7.00</span>
        <a href="#" class="btn">add to cart</a>
      </div>

      <div class="menu-item">
        <img src="image/background2.jpg" alt="Croissant">
        <h3>Butter Croissant</h3>
        <p>Flaky and buttery</p>
        <span>RM 7.00</span>
        <a href="#" class="btn">add to cart</a>
      </div>
    </div>
  </div>




<script>
  // Simple tab switcher
  const tabs = document.querySelectorAll(".menu-tab");
  const panels = document.querySelectorAll(".menu-panel");

  tabs.forEach(tab => {
    tab.addEventListener("click", () => {
      const target = tab.dataset.target;

      tabs.forEach(t => {
        t.classList.remove("active");
        t.setAttribute("aria-selected", "false");
      });

      panels.forEach(p => {
        p.classList.remove("active");
        p.hidden = true;
      });

      tab.classList.add("active");
      tab.setAttribute("aria-selected", "true");

      const panel = document.getElementById(target);
      panel.classList.add("active");
      panel.hidden = false;
    });
  });
</script>

<?php
include_once __DIR__ . "/includes/footer.php";
?>
