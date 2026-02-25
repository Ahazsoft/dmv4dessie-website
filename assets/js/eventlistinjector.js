const CONSTANT_TIME = {
  start: "9:30 am",
  end: "17:30 pm",
};

const CONSTANT_SPEAKERS = [
  { id: 1, avatar: "assets/images/events/EventPerson1.png" },
  { id: 2, avatar: "assets/images/events/EventPerson2.png" },
  { id: 3, avatar: "assets/images/events/EventPerson3.png" },
];

const events = [
  {
    id: 1,
    title: "Education for poor children",
    date: { day: 18, month: "June", year: 2026 },
    location: "654 Town Street, 88 United States",
    time: CONSTANT_TIME,
    speakers: CONSTANT_SPEAKERS,
  },
  {
    id: 2,
    title: "Health for the growing",
    date: { day: 21, month: "June", year: 2026 },
    location: "654 Town Street, 88 United States",
    time: CONSTANT_TIME,
    speakers: CONSTANT_SPEAKERS,
  },
  {
    id: 3,
    title: "Cloth coming",
    date: { day: 28, month: "June", year: 2026 },
    location: "654 Town Street, 88 United States",
    time: CONSTANT_TIME,
    speakers: CONSTANT_SPEAKERS,
  },
  {
    id: 4,
    title: "Love to help awareness event",
    date: { day: 9, month: "July", year: 2026 },
    location: "654 Town Street, 88 United States",
    time: CONSTANT_TIME,
    speakers: CONSTANT_SPEAKERS,
  },
  {
    id: 5,
    title: "Raise your hand against cancer",
    date: { day: 23, month: "July", year: 2026 },
    location: "654 Town Street, 88 United States",
    time: CONSTANT_TIME,
    speakers: CONSTANT_SPEAKERS,
  },
  {
    id: 6,
    title: "Fund raising for water",
    date: { day: 15, month: "August", year: 2026 },
    location: "654 Town Street, 88 United States",
    time: CONSTANT_TIME,
    speakers: CONSTANT_SPEAKERS,
  },
];

function injectEvents(eventsList) {
  const container = document.querySelector(".events-section-events-container");

  container.innerHTML = eventsList
    .map((event) => {
      return `
        <div class="events-section-event-card" data-aos="fade-up">
          
          <div class="date-month-year-container">
            <div class="date">${event.date.day}</div>
            <div class="month-year">
              ${event.date.month}<br />
              ${event.date.year}
            </div>
          </div>

          <div class="description">
            <div class="title">${event.title}</div>

            <div class="location-time-container">
              
              <div class="location-container">
                <img src="assets/images/icon/Calendar.png" alt="" />
                <p>${event.location}</p>
              </div>

              <div class="time-container">
                <img src="assets/images/icon/Clock.png" alt="" />
                <p>${event.time.start} - ${event.time.end}</p>
              </div>

            </div>

            <div class="image-containers">
              ${event.speakers
                .map((speaker, index) => {
                  const stackedClass =
                    index === 1 ? "stacked-1" : index === 2 ? "stacked-2" : "";
                  return `
                    <div class="image-container ${stackedClass}">
                      <img src="${speaker.avatar}" alt="speaker" />
                    </div>
                  `;
                })
                .join("")}

              <div class="image-container stacked-3">
                <div class="text-container stacked-3">Spe</div>
              </div>
              <p>akers</p>
            </div>
          </div>

          <div class="button-container">
            <button class="event-detail-btn" data-id="${event.id}">
              Event Details <span class= "custom-btn-arrow"></span>
            </button>
          </div>

        </div>
      `;
    })
    .join("");
}

function initLogics() {
  const eventDetailButtons = document.querySelectorAll(".event-detail-btn");

  eventDetailButtons.forEach((button) => {
    button.addEventListener("click", () => {
      const eventId = button.dataset.id;
      window.location.href = `event-detail.html?id=${eventId}`;
    });
  });
}

document.addEventListener("DOMContentLoaded", function () {
  injectEvents(events);
  initLogics();
});
