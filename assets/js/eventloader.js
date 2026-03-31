// event loader js

const eventContainer = document.getElementById(
  "event-section-event-inner-container",
);

const events = [
  {
    id: 1,
    imageurl: "assets/images/events/Event1.bmp",
    date: { day: 18, month: "June" },
    organizedBy: "Sharmii",
    title: "Education for poor children",
    description:
      "There are many variations of passages of injected Lorem Ipsum available, but the majority have.",
  },
  {
    id: 2,
    imageurl: "assets/images/events/Event2.bmp",
    date: { day: 21, month: "June" },
    organizedBy: "Nattasha",
    title: "Healthy food for growing",
    description:
      "There are many variations of passages of injected Lorem Ipsum available, but the majority have.",
  },
  {
    id: 3,
    imageurl: "assets/images/events/Event3.bmp",
    date: { day: 20, month: "September" },
    organizedBy: "Rithvik",
    title: "Community Aid: Help the helpless",
    description:
      "Let’s come together with kindness to support the poor and helpless with food, essentials, and polite community assistance.",
  },
  {
    id: 4,
    imageurl: "assets/images/events/Event4.bmp",
    date: { day: 9, month: "July" },
    organizedBy: "Sachin",
    title: "Love to help awarness event",
    description:
      "There are many variations of passages of injected Lorem Ipsum available, but the majority have.",
  },
  {
    id: 3,
    imageurl: "assets/images/events/Event5.bmp",
    date: { day: 10, month: "August" },
    organizedBy: "Savin",
    title: "Support Children with Autism",
    description:
      "Join us to raise funds and awareness for children with autism through therapy, resources, and community support.",
  },
  {
    id: 6,
    imageurl: "assets/images/events/Event6.bmp",
    date: { day: 15, month: "August" },
    organizedBy: "Rithvik",
    title: "Campaign: Support Elderly People",
    description:
      "Join us to support elderly people through care packages, companionship initiatives, and essential resources for their well-being.",
  },
];

document.addEventListener("DOMContentLoaded", () => {
  const eventContainer = document.getElementById(
    "event-section-event-inner-container",
  );

  if (!eventContainer || !Array.isArray(events)) return;

  eventContainer.innerHTML = events
    .map((event) => {
      return `
        <div class="event-card" data-aos="zoom-in">
          <div class="event-card-top">
            <img src="${event.imageurl}" alt="${event.title}" />
            <div class="event-card-top-date-container">
              <h2>${event.date.day}</h2>
              <h4>${event.date.month}</h4>
            </div>
          </div>

          <div class="event-card-medium">
            <p>Organized By: <span>${event.organizedBy}</span></p>
          </div>

          <div class="event-card-bottom">
            <h2>${event.title}</h2>
            <p>${event.description}</p>
          </div>
        </div>
      `;
    })
    .join("");
});
