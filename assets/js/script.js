function injectHeader() {
  // JavaScript to inject the header HTML
  const headerHTML = `
    <div class="header-logo-title-container">
        <img src="assets/images/Logo.png" alt="DMV Logo" />
        <h2>DMV For Dessie</h2>
    </div>

    <!-- HAMBURGER -->
    <div class="hamburger" id="hamburger">
        <span></span>
        <span></span>
        <span></span>
    </div>

    <!-- OVERLAY -->
    <div class="overlay" id="overlay"></div>

    <!-- DRAWER NAV -->
    <ul class="header-nav-container" id="navMenu">
        <div class="drawer-header">
            <button class="close-btn" id="closeBtn">&times;</button>
        </div>
        <li><a href="index.html" class="navlinks">Home</a></li>
        <li><a href="about.html" class="navlinks">About us</a></li>
        <li><a href="index.html#events-section" class="navlinks">Event</a></li>
        <li><a href="contact.html" class="navlinks">Contact</a></li>
    </ul>

    <div class="header-donate-button">
        <button class="btn" id="donate-button">Donate</button>
    </div>
`;

  // Injecting the HTML into the header
  document.querySelector(".header-container").innerHTML = headerHTML;
}

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

function initHeaderLogic() {
  const hamburger = document.getElementById("hamburger");
  const navMenu = document.getElementById("navMenu");
  const overlay = document.getElementById("overlay");
  const closeBtn = document.getElementById("closeBtn");
  const donateButton = document.getElementById("donate-button");
  const navLinks = document.querySelectorAll("#navMenu a");

  // Donate
  donateButton.addEventListener("click", () => {
    window.location.href = "donation.html";
  });

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

  // Active link highlight
  const currentPath = window.location.pathname.split("/").pop();
  navLinks.forEach((link) => {
    if (link.getAttribute("href") === currentPath) {
      link.classList.add("active");
    }
  });
}

function displayDimensions() {
  const width = window.innerWidth;
  const height = window.innerHeight;

  const h2 = document.createElement("h2");
  h2.innerText = `Current Width: ${width}px, Current Height: ${height}px`;

  document.querySelector(".debug").appendChild(h2);
}


/* === Proper Execution Order === */
document.addEventListener("DOMContentLoaded", () => {
  injectHeader();
  injectFooter();
  initHeaderLogic();
  displayDimensions();
});