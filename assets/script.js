
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

// =================== EVENT SEARCH + CATEGORY FILTER ===================
const categories = document.querySelectorAll(".category");
const cards = document.querySelectorAll(".event-card");
const searchInput = document.getElementById("searchInput");

let activeCategory = "all";

function filterEvents() {
  const searchText = searchInput.value.toLowerCase();
  const today = new Date();
  today.setHours(0,0,0,0);

  cards.forEach(card => {
    const cardCat = card.dataset.cat;
    const eventDate = new Date(card.dataset.date);
    eventDate.setHours(0,0,0,0);

    const title = card.querySelector("h3").innerText.toLowerCase();
    const desc  = card.querySelector("p").innerText.toLowerCase();
    const location = card.querySelector(".event-meta").innerText.toLowerCase();

    let show = true;

    /* ===== CATEGORY FILTER ===== */
    if (!["all","today","week","month","past"].includes(activeCategory)) {
      show = cardCat === activeCategory;
    }

    if (activeCategory === "today") {
      show = eventDate.getTime() === today.getTime();
    }

    if (activeCategory === "week") {
      const diff = (eventDate - today) / (1000*60*60*24);
      show = diff >= 0 && diff <= 7;
    }

    if (activeCategory === "month") {
      show =
        eventDate.getMonth() === today.getMonth() &&
        eventDate.getFullYear() === today.getFullYear();
    }

    if (activeCategory === "past") {
      show = eventDate < today;
    }

    /* ===== SEARCH FILTER ===== */
    if (
      !title.includes(searchText) &&
      !desc.includes(searchText) &&
      !location.includes(searchText)
    ) {
      show = false;
    }

    card.style.display = show ? "block" : "none";
  });
}

/* CATEGORY CLICK */
categories.forEach(cat => {
  cat.addEventListener("click", () => {
    categories.forEach(c => c.classList.remove("active"));
    cat.classList.add("active");

    activeCategory = cat.dataset.cat;
    searchInput.value = "";
    searchInput.blur();
    filterEvents();
  });
});

/* SEARCH INPUT */
searchInput.addEventListener("keyup", filterEvents);
// ========================= admin side bar toggle

function toggleMenu(){
  document.getElementById("sidebar").classList.toggle("active");
  document.getElementById("overlay").classList.toggle("active");
  document.body.classList.toggle("menu-open");
}