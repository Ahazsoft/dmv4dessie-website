// const causes = [
//   {
//     id: 1,
//     title: "Supporting Widows and the Elderly in Hardship",
//     image: "assets/images/donation/Cause4.png",
//     raised: 49500,
//     goal: 55000,
//   },

//   {
//     id: 2,
//     title: "Standing with Cancer Patients Through Care and Support",
//     image: "assets/images/donation/Cause2.png",
//     raised: 49500,
//     goal: 55000,
//   },
//   {
//     id: 3,
//     title: "Bringing Hope and Protection to Orphaned Children",
//     image: "assets/images/donation/Cause3.png",
//     raised: 49500,
//     goal: 55000,
//   },
//   {
//     id: 4,
//     title: "Empowering Orphans and Children with Special Needs",
//     image: "assets/images/donation/Cause1.png",
//     raised: 49500,
//     goal: 55000,
//   },
//   {
//     id: 5,
//     title: "Supporting People Living with Blindness",
//     image: "assets/images/donation/Cause5.png",
//     raised: 49500,
//     goal: 55000,
//   },
//   {
//     id: 6,
//     title: "Providing Essential School Supplies to Underfunded Schools",
//     image: "assets/images/donation/Cause6.png",
//     raised: 49500,
//     goal: 55000,
//   },
//     {
//     id: 7,
//     title: "Emergency Support for Displaced Families in Refugee Camps",
//     image: "assets/images/donation/Cause6.png",
//     raised: 49500,
//     goal: 55000,
//   },
// ];

document.addEventListener("DOMContentLoaded", async () => {
  try {
    const response = await fetch("./assets/js/donation-causes.json");

    if (!response.ok) {
      throw new Error("Failed to load donation data");
    }

    const causes = await response.json();

    const container = document.querySelector(
      ".donation-hero-section-donation-causes",
    );
    container.innerHTML = causes.map(createCard).join("");
    simulateProgressBar();
  } catch (error) {
    console.error("Error:", error);
  }
});

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
            <div class="progress-fill" data-percentage="${percentage}"></div>
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

<button class="donate-btn" onClick="window.location.href = 'donation-detail.html?id=' + ${cause.id};">
    Donate Now <span class="custom-btn-arrow"></span>
</button>
    </div>
  `;
}

function simulateProgressBar() {
  let count = 0;
  document.querySelectorAll(".progress-fill").forEach((bar) => {
    const delay = 500;
    count = count + 1;
    const percentage = bar.dataset.percentage;

    // slight delay so transition works
    setTimeout(
      () => {
        bar.style.width = percentage + "%";
      },
      delay + delay * count,
    );
  });
}
