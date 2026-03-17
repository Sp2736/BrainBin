document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".subject-card").forEach(card => {
    const header = card.querySelector(".subject-header");
    header.addEventListener("click", () => {
      card.classList.toggle("active");
      const btn = header.querySelector(".toggle-btn");
      btn.textContent = card.classList.contains("active") ? "−" : "+";
      // card.querySelector("h4"). = card.classList.contains("active") ? "red" : "#0b3d91";
    });
  });
});
