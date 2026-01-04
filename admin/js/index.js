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
