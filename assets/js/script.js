const hamburger = document.getElementById("hamburger");
const navMenu = document.getElementById("navMenu");
const overlay = document.getElementById("overlay");
const closeBtn = document.getElementById("closeBtn");
const navLinks = document.querySelectorAll("#navMenu a");

function openDrawer() {
  navMenu.classList.add("active");
  overlay.classList.add("active");
}

function closeDrawer() {
  navMenu.classList.remove("active");
  overlay.classList.remove("active");
}

hamburger.addEventListener("click", openDrawer);
closeBtn.addEventListener("click", closeDrawer);
overlay.addEventListener("click", closeDrawer);

navLinks.forEach((link) => {
  link.addEventListener("click", closeDrawer);
});
