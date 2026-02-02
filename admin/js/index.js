const sidebarLinks = document.querySelectorAll("aside .sidebar a");
const tabContents = document.querySelectorAll(".tab-content");
const themeToggler = document.querySelector(".theme-toggler");
const themeTogglerSpans = document.querySelectorAll(".theme-toggler span");
const savedTheme = localStorage.getItem("theme");

if (savedTheme === "dark") {
  document.body.classList.add("dark-theme-variables");
  themeToggler.querySelector("span:nth-child(1)").classList.remove("active");
  themeToggler.querySelector("span:nth-child(2)").classList.add("active");
} else {
  document.body.classList.remove("dark-theme-variables");
  themeToggler.querySelector("span:nth-child(1)").classList.add("active");
  themeToggler.querySelector("span:nth-child(2)").classList.remove("active");
}

sidebarLinks.forEach((link) => {
  link.addEventListener("click", (e) => {
    if (link.id === "logout-link") return;

    e.preventDefault();
    const targetId = link
      .querySelector("h3")
      .textContent.toLowerCase()
      .replace(" ", "");

    sidebarLinks.forEach((l) => l.classList.remove("active"));
    tabContents.forEach((tab) => tab.classList.remove("active"));

    link.classList.add("active");
    document.getElementById(targetId).classList.add("active");
  });
});

themeToggler.addEventListener("click", () => {
  const isDark = document.body.classList.toggle("dark-theme-variables");

  themeToggler.querySelector("span:nth-child(1)").classList.toggle("active");
  themeToggler.querySelector("span:nth-child(2)").classList.toggle("active");

  localStorage.setItem("theme", isDark ? "dark" : "light");
});

function openTabById(tabId) {
  if (!tabId) return;

  // remove active from all
  sidebarLinks.forEach((l) => l.classList.remove("active"));
  tabContents.forEach((tab) => tab.classList.remove("active"));

  // activate tab content
  const target = document.getElementById(tabId);
  if (target) target.classList.add("active");

  // activate sidebar link (match by h3 text)
  sidebarLinks.forEach((link) => {
    const text = link.querySelector("h3")?.textContent?.toLowerCase().replace(" ", "");
    if (text === tabId) link.classList.add("active");
  });
}

// On page load: read tab= from URL
const params = new URLSearchParams(window.location.search);
const tabFromUrl = params.get("tab");
openTabById(tabFromUrl);

// ---- Customers AJAX (Edit/Delete) ----
const modal = document.getElementById("customerModal");
const closeModalBtn = document.getElementById("closeCustomerModal");
const editForm = document.getElementById("customerEditForm");

function showModal() {
  if (!modal) return;
  modal.style.display = "flex";
}
function hideModal() {
  if (!modal) return;
  modal.style.display = "none";
}

closeModalBtn?.addEventListener("click", hideModal);
modal?.addEventListener("click", (e) => {
  if (e.target === modal) hideModal();
});

// Open modal and fill data
document.addEventListener("click", (e) => {
  const btn = e.target.closest("[data-edit-customer]");
  if (!btn) return;

  document.getElementById("edit_id").value = btn.dataset.id;
  document.getElementById("edit_name").value = btn.dataset.name;
  document.getElementById("edit_phone").value = btn.dataset.phone;
  document.getElementById("edit_email").value = btn.dataset.email;
  document.getElementById("edit_address").value = btn.dataset.address;
  document.getElementById("edit_verify").value = btn.dataset.verify;

  showModal();
});

function autoHideAlerts() {
  const alerts = document.querySelectorAll(".alert.success, .alert.error");
  if (!alerts.length) return;

  const HIDE_AFTER_MS = 3000;
  const FADE_MS = 350;

  alerts.forEach((alertBox) => {
    // prevent double timer
    if (alertBox.dataset.autohide === "1") return;
    alertBox.dataset.autohide = "1";

    alertBox.style.opacity = "1";
    alertBox.style.transform = "translateY(0)";
    alertBox.style.transition = `opacity ${FADE_MS}ms ease, transform ${FADE_MS}ms ease`;

    setTimeout(() => {
      alertBox.style.opacity = "0";
      alertBox.style.transform = "translateY(-8px)";

      setTimeout(() => alertBox.remove(), FADE_MS + 50);
    }, HIDE_AFTER_MS);
  });
}

/**
 * Some alerts appear after tab switch / redirect.
 * This will retry a few times.
 */
function runAutoHideAlertsWithRetry() {
  let tries = 0;
  const maxTries = 10;

  const timer = setInterval(() => {
    autoHideAlerts();
    tries++;
    if (tries >= maxTries) clearInterval(timer);
  }, 300); // check every 0.3s for ~3s
}

document.addEventListener("DOMContentLoaded", runAutoHideAlertsWithRetry);

// =============================
// Customers: Bulk Delete (Checkbox + Button)
// =============================
function initCustomersBulkDelete() {
  const bulkDeleteForm = document.getElementById("bulkDeleteForm");
  const checkAll = document.getElementById("checkAll");
  const btnDeleteSelected = document.getElementById("btnDeleteSelected");

  // If customers tab/table not on this page, do nothing
  if (!bulkDeleteForm || !checkAll || !btnDeleteSelected) return;

  function getRowChecks() {
    return Array.from(document.querySelectorAll(".rowCheck"));
  }

  function syncButtonState() {
    const anyChecked = getRowChecks().some((cb) => cb.checked);
    btnDeleteSelected.disabled = !anyChecked;
  }

  // Select all toggle
  checkAll.addEventListener("change", () => {
    getRowChecks().forEach((cb) => (cb.checked = checkAll.checked));
    syncButtonState();
  });

  // Row checkbox changes
  document.addEventListener("change", (e) => {
    if (!e.target.classList.contains("rowCheck")) return;

    const rows = getRowChecks();
    checkAll.checked = rows.length > 0 && rows.every((cb) => cb.checked);
    syncButtonState();
  });

  // Button click submits the form
  btnDeleteSelected.addEventListener("click", () => {
    const anyChecked = getRowChecks().some((cb) => cb.checked);
    if (!anyChecked) return;

    if (confirm("Delete selected customer(s)? This cannot be undone.")) {
      bulkDeleteForm.submit();
    }
  });

  // initial state
  syncButtonState();
}

// Run on load
document.addEventListener("DOMContentLoaded", () => {
  initCustomersBulkDelete();
});
