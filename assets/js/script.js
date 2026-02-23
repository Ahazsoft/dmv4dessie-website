const hamburger = document.getElementById("hamburger");
const navMenu = document.getElementById("navMenu");
const overlay = document.getElementById("overlay");
const closeBtn = document.getElementById("closeBtn");
const navLinks = document.querySelectorAll("#navMenu a");
const donateButton = document.getElementById("dontate-button");

// donate button

donateButton.addEventListener("click", () => {
  window.location.href = "donation.html";
});

// mobile responsive drawer
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

// Highlight active nav link
const currentPath = window.location.pathname.split("/").pop();
const navLinksActive = document.querySelectorAll("#navMenu a");
navLinksActive.forEach((link) => {
  if (link.getAttribute("href") === currentPath) {
    link.classList.add("active");
  }
});

function injectFooter() {
  // JavaScript to inject HTML into footer
  const footerHTML = `
    <div class="footer-container-top">
        <div class="footer-container-top-left">
            <div class="footer-container-top-left-title">
                <img src="assets/images/Logo.png" alt="DMV Logo" />
                <h2>DMV For Dessie</h2>
            </div>
            <div class="footer-container-top-left-description">
                <p>A nonprofit organization committed to humanitarian aid and community recovery.</p>
            </div>
            <div class="footer-container-top-left-socials">
                <div class="icon-box">
                    <a href=""><img src="assets/images/icon/instagram-100.png" alt="instagram" /></a>
                </div>
                <div class="icon-box">
                    <a href=""><img src="assets/images/icon/facebook-100.png" alt="facebook" /></a>
                </div>
                <div class="icon-box">
                    <a href=""><img src="assets/images/icon/telegram-100.png" alt="telegram" /></a>
                </div>
                <div class="icon-box">
                    <a href=""><img src="assets/images/icon/tiktok-100.png" alt="tiktok" /></a>
                </div>
            </div>
        </div>
        <div class="footer-container-top-middle">
            <div class="footer-container-top-middle-title">Get Involved</div>
            <div class="footer-container-top-middle-links">
                <a href="#">About Us</a>
                <a href="#">Volunteer</a>
                <a href="#">Causes</a>
                <a href="#">Projects</a>
                <a href="#">Team</a>
            </div>
        </div>
        <div class="footer-container-top-right">
            <div class="footer-container-top-right-title">Contacts</div>
            <div class="footer-container-top-right-addresses">
                <p>654 Town Street, 88 United States</p>
                <p>contact_us@dmvfordessie.org</p>
                <p>0769791991</p>
            </div>
        </div>
    </div>
    <div class="footer-container-copyright">
        <p>
            Copyright &copy; Dmv For Dessie | Designed by <a href="https://www.ahaz.io/">Ahaz Solutions</a>          
        </p>
    </div>
`;

  // Injecting the HTML into the footer
  document.querySelector(".footer-container").innerHTML = footerHTML;
}

function displayDimensions() {
  const width = window.innerWidth;
  const height = window.innerHeight;

  const h2 = document.createElement("h2");
  h2.innerText = `Current Width: ${width}px, Current Height: ${height}px`;

  document.querySelector(".debug").appendChild(h2);
}

// displayDimensions();
injectFooter();
// 1580 px
// 1280 px
