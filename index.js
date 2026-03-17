document.addEventListener("DOMContentLoaded", () => {
  // Notification Banner
  const banner = document.getElementById("notification-banner");
  const closeBtn = document.getElementById("close-banner");

  setTimeout(() => banner.classList.remove("hidden"), 1000);

  closeBtn.addEventListener("click", () => {
    banner.classList.add("hidden");
  });

  // FAQ Collapsible
  const questions = document.querySelectorAll(".faq-question");
  questions.forEach((question) => {
    question.addEventListener("click", () => {
      const answer = question.nextElementSibling;

      // Close other open answers
      document.querySelectorAll(".faq-answer").forEach((a) => {
        if (a !== answer) {
          a.style.maxHeight = null;
          a.previousElementSibling.classList.remove("active");
        }
      });

      // Toggle this answer
      question.classList.toggle("active");
      if (answer.style.maxHeight) {
        answer.style.maxHeight = null;
      } else {
        answer.style.maxHeight = answer.scrollHeight + "px";
      }
    });
  });
});
// Popup Model
const popupBtn = document.getElementById("open-popup");
const popupmodel = document.getElementById("popup-model");
const closePopup = document.getElementById("close-popup");

popupBtn.addEventListener("click", () => {
  popupmodel.classList.remove("hidden");
});

closePopup.addEventListener("click", () => {
  popupmodel.classList.add("hidden");
});

window.addEventListener("click", (e) => {
  if (e.target === popupmodel) {
    popupmodel.classList.add("hidden");
  }
});
