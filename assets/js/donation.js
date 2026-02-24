const causes = [
  {
    id: 1,
    title: "Crisis Hardship Grants for Widows & Elders",
    image: "assets/images/donation/Cause4.png",
    raised: 49500,
    goal: 55000,
  },

  {
    id: 2,
    title: "Fighting hunger with food distribution drives",
    image: "assets/images/donation/Cause2.png",
    raised: 49500,
    goal: 55000,    
  },
  {
    id: 3,
    title: "Providing medical aid to underserved areas",
    image: "assets/images/donation/Cause3.png",
    raised: 49500,
    goal: 55000,
  },
  {
    id: 4,
    title: "Helping the homeless during hopeless",
    image: "assets/images/donation/Cause1.png",
    raised: 49500,
    goal: 55000,
  },
  {
    id: 5,
    title: "Aiding the homeless when things seem hopeless",
    image: "assets/images/donation/Cause5.png",
    raised: 49500,
    goal: 55000,
  },
  {
    id: 6,
    title: "Emergency Nutrition for War-Displaced Families",
    image: "assets/images/donation/Cause6.png",
    raised: 49500,
    goal: 55000,
  },
];

const container = document.querySelector(
  ".donation-hero-section-donation-causes",
);

function formatCurrency(amount) {
  return `$${amount.toLocaleString()}`;
}

function createCard(cause) {
  const percentage = Math.round((cause.raised / cause.goal) * 100);

  return `
    <div class="donation-card">
      
      <div class="donation-card-image">
        <img src="${cause.image}" alt="${cause.title}">
      </div>

      <div class="donation-card-status">
        <div class="donation-card-status-top">
          <h3>Donation</h3>
          <p>${percentage}%</p>
        </div>

        <div class="donation-card-status-progress">
          <div class="progress-bar">
            <div class="progress-fill" style="width:${percentage}%"></div>
          </div>
        </div>

        <div class="donation-card-status-bottom">
          <h2>Raised ${formatCurrency(cause.raised)}</h2>
          <h2>Goal ${formatCurrency(cause.goal)}</h2>
        </div>
      </div>

      <div class="donation-card-description">
        ${cause.title}
      </div>

      <button class="donate-btn" onClick=(openDonationdetail())>
        Donate Now ↗
      </button>

    </div>
  `;
}

container.innerHTML = causes.map(createCard).join("");


function openDonationdetail(){
  window.location.href = "donation-detail.html";
}