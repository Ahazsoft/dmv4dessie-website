// import causes from "./donation-causes.js";

const herosection = document.querySelector(".donation-detail-hero-section");
const aboutsection = document.querySelector("#donation-detail-about-section");
const summarysection = document.querySelector(
  "#donation-detail-summary-section",
);
const challengesection = document.querySelector(
  ".donation-detail-challenge-section",
);

function injectHeroSection(cause) {
  const percentage = calculatePercentage(cause.raised, cause.goal);

  const herosection_content = `
    <div class="hero-container">
        <div class="hero-container-left">
          <img src="${cause.image}" alt="${cause.title}" />
        </div>
        <div class="hero-container-right">
          <h2>${cause.title}</h2>
          <p data-aos="fade-up" data-aos-delay="300">
            ${cause.description}
          </p>
          <div class="donation-progress-status">
            <div class="progress-tracker">
              <div class="percentage-display" style="left: ${percentage - 5}%">
                ${percentage.toFixed(0)}%
              </div>
              <div class="progress-bar">
                <div class="progress-fill" style="width:${percentage}%"></div>
              </div>
              <div class="progress-tracker-texts">
                <h2>Raised ${cause.raised}</h2>
                <h2>Goal ${cause.goal}</h2>
              </div>
            </div>
            <button class="donate-btn">
              Donate Now <span class="custom-btn-arrow" />
            </button>
          </div>
        </div>
      </div>
    `;

  herosection.innerHTML = herosection_content;
}

function injectAboutSection(cause) {
  const aboutsection_content1 = `<h2>About</h2>`;
  const aboutsection_content2 = `<p>${cause.about}.
      </p>`;
  const aboutsection_content3 = `<div class="image-containers">
        <img
          src="assets/images/donation/Detail1.png"
          alt=""
          srcset=""
          data-aos="fade-right"
          data-aos-delay="200"
        />
        <img
          src="assets/images/donation/Detail2.png"
          alt=""
          srcset=""
          data-aos="fade-left"
          data-aos-delay="200"
        />
      </div>`;

  aboutsection.innerHTML = `${aboutsection_content1} ${aboutsection_content2} ${aboutsection_content3}`;
}

function injectSummarySection(cause) {
  summarysection_content1 = `<h2>Summary</h2>`;
  summarysection_content2 = `<p>${cause.summary}</p>`;

  summarysection.innerHTML = `${summarysection_content1} ${summarysection_content2}`;
}

function injectChallengeSection(cause) {
  challengesection_content1 = `<h2>Challenge</h2>`;
  challengesection_content2 = `<p>${cause.challenge}</p>`;
  challengesection_content3 = `<div class="image-checklist-container">
        <div class="image-container" data-aos="fade-right">
          <img src="${cause.image}" alt="${cause.title}" srcset="" />
        </div>
        <div class="checklist-container" id="checklist-container" data-aos="fade-left">
          
          <div class="checklist-element">
            <img src="assets/images/icon/circle-check.png" alt="" srcset="" />
            <p>We help companies develop social responsibilities</p>
          </div>
          <!-- checklist element ends -->
          <div class="checklist-element">
            <img src="assets/images/icon/circle-check.png" alt="" srcset="" />
            <p>Community-driven and transparent giving</p>
          </div>
          <!-- checklist element ends -->
          <div class="checklist-element">
            <img src="assets/images/icon/circle-check.png" alt="" srcset="" />
            <p>Help restore dignity and rebuild lives</p>
          </div>
          <!-- checklist element ends -->
          <div class="checklist-element">
            <img src="assets/images/icon/circle-check.png" alt="" srcset="" />
            <p>Share compassion with your community</p>
          </div>
          <!-- checklist element ends -->
        </div>
      </div>
      `;

  challengesection.innerHTML = `${challengesection_content1}${challengesection_content2}${challengesection_content3}`;
  const checklist_container = document.querySelector("#checklist-container");

  cause.challeges.forEach((element) => {
    checklistelement = `<div class="checklist-element">
            <img src="assets/images/icon/circle-check.png" alt="" srcset="" />
            <p>${element}</p>
          </div>`;
    checklist_container.appendChild(checklistelement);
  });
}

function calculatePercentage(raised, goal) {
  return (raised / goal) * 100;
}

document.addEventListener("DOMContentLoaded", async () => {
  try {
    const params = new URLSearchParams(window.location.search);
    const id = parseInt(params.get("id"));

    const response = await fetch("./assets/js/donation-causes.json");

    if (!response.ok) {
      throw new Error("Failed to load donation data");
    }

    const causes = await response.json();

    const cause = causes.find((c) => c.id === id);

    if (!cause) {
      window.location.href = "donation.html";
      return;
    }

    injectHeroSection(cause);
    injectAboutSection(cause);
    injectSummarySection(cause);
    injectChallengeSection(cause);
    
  } catch (error) {
    console.error("Error:", error);
  }
});
