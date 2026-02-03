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

document.addEventListener("DOMContentLoaded", () => {
  const toggleBtn = document.getElementById("toggleBulkDelete");
  const cancelBtn = document.getElementById("cancelBulkDelete");
  const selectedCounter = document.getElementById("selectedCounter");
  const bulkForm = document.getElementById("bulkDeleteForm");
  const table = document.getElementById("customersTable");
  const checkAll = document.getElementById("checkAll");

  // Debug: confirm elements exist
  console.log("bulk delete elements:", {
    toggleBtn: !!toggleBtn,
    bulkForm: !!bulkForm,
    table: !!table,
  });

  if (!toggleBtn || !bulkForm || !table) return;

  let bulkMode = false;

  function getRowChecks() {
    return Array.from(document.querySelectorAll(".row-check"));
  }

  function setBulkMode(on) {
    bulkMode = on;

    table.querySelectorAll(".select-col").forEach((el) => {
      el.style.display = on ? "" : "none";
    });

    if (selectedCounter) selectedCounter.style.display = on ? "" : "none";
    if (cancelBtn) cancelBtn.style.display = on ? "" : "none";

    if (!on) {
      toggleBtn.textContent = "Delete";
      checkAll && (checkAll.checked = false, checkAll.indeterminate = false);
      getRowChecks().forEach((cb) => (cb.checked = false));
      document.querySelectorAll("tr.cust-row").forEach((tr) => tr.classList.remove("row-selected"));
      updateSelectedUI();
    } else {
      toggleBtn.textContent = "Delete Selected (0)";
    }
  }

  function updateSelectedUI() {
    const checks = getRowChecks();
    const selected = checks.filter((cb) => cb.checked).length;

    if (selectedCounter) selectedCounter.textContent = `${selected} selected`;
    if (bulkMode) toggleBtn.textContent = `Delete Selected (${selected})`;

    document.querySelectorAll("tr.cust-row").forEach((tr) => tr.classList.remove("row-selected"));
    checks.forEach((cb) => cb.checked && cb.closest("tr")?.classList.add("row-selected"));

    if (checkAll && checks.length) {
      checkAll.checked = selected === checks.length;
      checkAll.indeterminate = selected > 0 && selected < checks.length;
    }
  }

  toggleBtn.addEventListener("click", () => {
    if (!bulkMode) {
      setBulkMode(true);
      updateSelectedUI();
      return;
    }

    const selected = getRowChecks().filter((cb) => cb.checked).length;
    if (selected === 0) return alert("Please select at least 1 customer to delete.");
    if (!confirm(`Delete ${selected} selected customer(s)?`)) return;

    bulkForm.submit();
  });

  cancelBtn?.addEventListener("click", () => setBulkMode(false));

  checkAll?.addEventListener("change", () => {
    getRowChecks().forEach((cb) => (cb.checked = checkAll.checked));
    updateSelectedUI();
  });

  document.addEventListener("change", (e) => {
    if (!bulkMode) return;
    if (e.target.classList?.contains("row-check")) updateSelectedUI();
  });

  document.addEventListener("click", (e) => {
    if (!bulkMode) return;

    const tr = e.target.closest("tr.cust-row");
    if (!tr) return;

    if (e.target.matches("input, a, button")) return;

    const cb = tr.querySelector(".row-check");
    if (!cb) return;

    cb.checked = !cb.checked;
    updateSelectedUI();
  });
});

/**
 * Common Bulk Delete Controller
 * Works for Customers / Orders / Feedback (or any table) using config selectors.
 */
(function () {
  function $(sel, root = document) { return root.querySelector(sel); }
  function $all(sel, root = document) { return Array.from(root.querySelectorAll(sel)); }

  function initBulkDelete(cfg) {
    const toggleBtn   = $(cfg.toggleBtn);
    const cancelBtn   = cfg.cancelBtn ? $(cfg.cancelBtn) : null;
    const counterEl   = $(cfg.counter);
    const checkAll    = $(cfg.checkAll);
    const formEl      = cfg.form ? $(cfg.form) : null;

    if (!toggleBtn || !counterEl || !checkAll) return; // not on this tab/page

    let isDeleteMode = false;

    const getSelectCols = () => $all(cfg.selectCol);
    const getRowChecks  = () => $all(cfg.rowCheck);
    const getRows       = () => $all(cfg.row);

    function setDisplay(elems, show) {
      elems.forEach(el => { el.style.display = show ? '' : 'none'; });
    }

    function clearSelection() {
      getRowChecks().forEach(cb => (cb.checked = false));
      checkAll.checked = false;
      updateCounter();
      updateRowHighlight();
    }

    function updateCounter() {
      const count = getRowChecks().filter(cb => cb.checked).length;
      counterEl.textContent = `${count} selected`;
      counterEl.style.display = isDeleteMode ? '' : 'none';
      toggleBtn.textContent = isDeleteMode ? (cfg.deleteLabel || 'Delete Selected') : (cfg.toggleLabel || 'Delete');
    }

    function updateRowHighlight() {
      const rows = getRows();
      rows.forEach(row => row.classList.remove(cfg.rowActiveClass || 'row-selected'));

      getRowChecks().forEach(cb => {
        const row = cb.closest(cfg.row) || cb.closest('tr');
        if (row && cb.checked) row.classList.add(cfg.rowActiveClass || 'row-selected');
      });
    }

    function syncCheckAll() {
      const cbs = getRowChecks();
      if (cbs.length === 0) {
        checkAll.checked = false;
        return;
      }
      const checkedCount = cbs.filter(cb => cb.checked).length;
      checkAll.checked = checkedCount === cbs.length;
      updateCounter();
      updateRowHighlight();
    }

    function enterDeleteMode() {
      isDeleteMode = true;
      setDisplay(getSelectCols(), true);
      if (cancelBtn) cancelBtn.style.display = '';
      counterEl.style.display = '';
      updateCounter();
    }

    function exitDeleteMode() {
      isDeleteMode = false;
      setDisplay(getSelectCols(), false);
      if (cancelBtn) cancelBtn.style.display = 'none';
      counterEl.style.display = 'none';
      toggleBtn.textContent = (cfg.toggleLabel || 'Delete');
      clearSelection();
    }

    // Toggle click behavior:
    // - first click enters delete mode
    // - second click submits (if form exists) when something selected
    toggleBtn.addEventListener('click', function () {
      if (!isDeleteMode) {
        enterDeleteMode();
        return;
      }

      // Already in delete mode -> submit if selected
      const selected = getRowChecks().filter(cb => cb.checked);
      if (selected.length === 0) {
        alert(cfg.noSelectionMessage || 'Please select at least one item.');
        return;
      }

      const ok = confirm(cfg.confirmMessage || `Delete ${selected.length} selected item(s)?`);
      if (!ok) return;

      if (formEl) formEl.submit();
      else console.warn('Bulk delete form not found:', cfg.form);
    });

    if (cancelBtn) {
      cancelBtn.addEventListener('click', exitDeleteMode);
    }

    // Check all
    checkAll.addEventListener('change', function () {
      const checked = checkAll.checked;
      getRowChecks().forEach(cb => (cb.checked = checked));
      updateCounter();
      updateRowHighlight();
    });

    // Each checkbox change
    document.addEventListener('change', function (e) {
      if (!isDeleteMode) return;
      if (!e.target.matches(cfg.rowCheck)) return;
      syncCheckAll();
    });

    // Optional: clicking row toggles checkbox (nice UX)
    if (cfg.enableRowClick !== false) {
      document.addEventListener('click', function (e) {
        if (!isDeleteMode) return;

        const row = e.target.closest(cfg.row);
        if (!row) return;

        // Don't toggle when clicking links/buttons/inputs directly
        if (e.target.closest('a, button, input, select, textarea, label')) return;

        const cb = row.querySelector(cfg.rowCheck);
        if (cb) {
          cb.checked = !cb.checked;
          syncCheckAll();
        }
      });
    }

    // Expose exit if you want (optional)
    return { exitDeleteMode };
  }

  // ====== INIT ALL SECTIONS HERE ======

  // Customers
  initBulkDelete({
    toggleBtn: '#toggleBulkDelete',
    cancelBtn: '#cancelBulkDelete',
    counter: '#selectedCounter',
    checkAll: '#checkAll',
    selectCol: '.select-col',
    rowCheck: '.row-check',
    row: '.cust-row',
    form: '#customersActionForm',
    toggleLabel: 'Delete',
    deleteLabel: 'Delete Selected',
    confirmMessage: 'Delete selected customer(s)?\n\nNote: this may also delete related unpaid orders depending on your backend logic.',
    noSelectionMessage: 'Select at least 1 customer to delete.',
    rowActiveClass: 'row-selected',
    enableRowClick: true
  });

  // Orders
  initBulkDelete({
    toggleBtn: '#toggleBulkDeleteOrders',
    cancelBtn: '#cancelBulkDeleteOrders',
    counter: '#selectedCounterOrders',
    checkAll: '#checkAllOrders',
    selectCol: '.select-col-orders',
    rowCheck: '.order-check',
    row: '.order-row',
    // IMPORTANT: if you have a separate bulk delete form for orders, use that form id.
    // If you use update-order-status.php as shown, DON'T submit delete to it.
    // So create a separate form like id="ordersDeleteForm" that posts to bulk-delete-orders.php
    form: '#ordersDeleteForm',
    toggleLabel: 'Delete',
    deleteLabel: 'Delete Selected',
    confirmMessage: 'Delete selected order(s)?',
    noSelectionMessage: 'Select at least 1 order to delete.',
    rowActiveClass: 'row-selected',
    enableRowClick: true
  });

  // Feedback (example ids/classes — match your feedback table)
  initBulkDelete({
    toggleBtn: '#toggleBulkDeleteFeedback',
    cancelBtn: '#cancelBulkDeleteFeedback',
    counter: '#selectedCounterFeedback',
    checkAll: '#checkAllFeedback',
    selectCol: '.select-col-feedback',
    rowCheck: '.feedback-check',
    row: '.feedback-row',
    form: '#feedbackDeleteForm',
    toggleLabel: 'Delete',
    deleteLabel: 'Delete Selected',
    confirmMessage: 'Delete selected feedback(s)?',
    noSelectionMessage: 'Select at least 1 feedback to delete.',
    rowActiveClass: 'row-selected',
    enableRowClick: true
  });

})();

