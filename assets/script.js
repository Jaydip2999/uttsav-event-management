
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
