// event loader js

const eventContainer = document.getElementById("event-section-event-inner-container");

const events = [
  {
    id: 1,
    imageurl:"assets/images/events/Event1.png",
    date: { day: 18, month: "June" },
    organizedBy: "Sharmii",
    title: "Education for Poor Children",
    description:
      "There are many variations of passages of injected Lorem Ipsum available, but the majority have."
  },
  {
    id: 2,
    imageurl:"assets/images/events/Event2.png",
    date: { day: 21, month: "June" },
    organizedBy: "Nattasha",
    title: "Healthy Food for Growing",
    description:
      "There are many variations of passages of injected Lorem Ipsum available, but the majority have."
  },
  {
    id: 3,
    imageurl:"assets/images/events/Event3.png",
    date: { day: 28, month: "June" },
    organizedBy: "Shanika",
    title: "Cloth Conning",
    description:
      "There are many variations of passages of injected Lorem Ipsum available, but the majority have."
  },
  {
    id: 4,
    imageurl:"assets/images/events/Event4.png",
    date: { day: 9, month: "July" },
    organizedBy: "Sachin",
    title: "Love to Help Awarness Event",
    description:
      "There are many variations of passages of injected Lorem Ipsum available, but the majority have."
  },
  {
    id: 5,
    imageurl:"assets/images/events/Event5.png",
    date: { day: 23, month: "July" },
    organizedBy: "Savin",
    title: "Raise Your Hands Against Cancer",
    description:
      "There are many variations of passages of injected Lorem Ipsum available, but the majority have."
  },
  {
    id: 6,
    imageurl:"assets/images/events/Event6.png",
    date: { day: 15, month: "August" },
    organizedBy: "Rithvik",
    title: "Compaign: Fund Raising for Water",
    description:
      "There are many variations of passages of injected Lorem Ipsum available, but the majority have."
  }
];

document.addEventListener("DOMContentLoaded", () => {
  const eventContainer = document.getElementById(
    "event-section-event-inner-container"
  );

  if (!eventContainer || !Array.isArray(events)) return;

  // Clear existing static card if needed
  eventContainer.innerHTML = "";

  events.forEach((event) => {
    // Create elements
    const eventCard = document.createElement("div");
    eventCard.className = "event-card";

    eventCard.innerHTML = `
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
    `;

    eventContainer.appendChild(eventCard);
  });
});



