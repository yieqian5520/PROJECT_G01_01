// admin/js/dashboard-ui.js
(function () {
  function wireTableSearch() {
    document.querySelectorAll('[data-table-search]').forEach((input) => {
      const tableSelector = input.getAttribute('data-table-search');
      const table = document.querySelector(tableSelector);
      if (!table) return;

      const tbody = table.querySelector('tbody');
      if (!tbody) return;

      input.addEventListener('input', () => {
        const q = input.value.trim().toLowerCase();
        tbody.querySelectorAll('tr').forEach((tr) => {
          const text = tr.innerText.toLowerCase();
          tr.style.display = text.includes(q) ? '' : 'none';
        });
      });
    });
  }

  document.addEventListener('DOMContentLoaded', () => {
    wireTableSearch();
  });
})();
