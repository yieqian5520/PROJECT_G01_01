const sidebarLinks = document.querySelectorAll('aside .sidebar a');
const tabContents = document.querySelectorAll('.tab-content');
const themeToggler = document.querySelector(".theme-toggler");
const themeTogglerSpans = document.querySelectorAll(".theme-toggler span");

sidebarLinks.forEach(link => {
  link.addEventListener('click', (e) => {
    if (link.id === 'logout-link') return;

    e.preventDefault();
    const targetId = link.querySelector('h3').textContent.toLowerCase().replace(' ', '');

    sidebarLinks.forEach(l => l.classList.remove('active'));
    tabContents.forEach(tab => tab.classList.remove('active'));

    link.classList.add('active');
    document.getElementById(targetId).classList.add('active');
  });
});

themeToggler.addEventListener('click', () => {
  document.body.classList.toggle('dark-theme-variables');

  themeToggler.querySelector('span:nth-child(1)').classList.toggle('active');
  themeToggler.querySelector('span:nth-child(2)').classList.toggle('active');
});


