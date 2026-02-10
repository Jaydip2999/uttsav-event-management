
// ==================menu for mobile====================
const menuBtn = document.getElementById("menuBtn");
const navLinks = document.getElementById("navLinks");

menuBtn.addEventListener("click", (e) => {
    e.stopPropagation(); 
    navLinks.classList.toggle("active");
});

document.body.addEventListener("click", (e) => {
    if (navLinks.classList.contains("active") && !navLinks.contains(e.target) && e.target !== menuBtn) {
        navLinks.classList.remove("active");
    }
});

navLinks.addEventListener("click", (e) => {
    e.stopPropagation();
});
// =====================top rate card =====================
  const slides = document.querySelectorAll(".slide");
  let index = 0;

  setInterval(() => {
    slides[index].classList.remove("active");
    index = (index + 1) % slides.length;
    slides[index].classList.add("active");
  }, 4500); 

  lucide.createIcons();
// =======================login logout btn =================
document.addEventListener("DOMContentLoaded", () => {

    const profileIcon = document.getElementById("profileIcon");
    const profilePopup = document.getElementById("profilePopup");

    if(profileIcon && profilePopup){

        profileIcon.addEventListener("click", (e) => {
            e.stopPropagation();

            if(profilePopup.style.display === "block"){
                profilePopup.style.display = "none";
            } else {
                profilePopup.style.display = "block";
            }
        });

        document.addEventListener("click", (e) => {
            if(!profilePopup.contains(e.target) && e.target !== profileIcon){
                profilePopup.style.display = "none";
            }
        });
    }
});

// ===================event cards search and category ============
const categories = document.querySelectorAll(".category");
const cards = document.querySelectorAll(".event-card");

categories.forEach(cat => {
  cat.addEventListener("click", () => {

    categories.forEach(c => c.classList.remove("active"));
    cat.classList.add("active");

    const filter = cat.dataset.cat;
    const today = new Date();
    today.setHours(0,0,0,0);

    cards.forEach(card => {
      const cardCat = card.dataset.cat;
      const eventDate = new Date(card.dataset.date);
      eventDate.setHours(0,0,0,0);

      let show = true;

      // NORMAL CATEGORY
      if (!["all","today","week","month","past"].includes(filter)) {
        show = cardCat === filter;
      }

      // TODAY
      if (filter === "today") {
        show = eventDate.getTime() === today.getTime();
      }

      // THIS WEEK
      if (filter === "week") {
        const diff = (eventDate - today) / (1000*60*60*24);
        show = diff >= 0 && diff <= 7;
      }

      // THIS MONTH
      if (filter === "month") {
        show =
          eventDate.getMonth() === today.getMonth() &&
          eventDate.getFullYear() === today.getFullYear();
      }

      // 🔥 PAST EVENTS
      if (filter === "past") {
        show = eventDate < today;
      }

      card.style.display = show ? "block" : "none";
    });

  });
});

