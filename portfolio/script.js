document.addEventListener("DOMContentLoaded", () => {
  // Mobile menu toggle
  const mobileMenuBtn = document.getElementById("mobile-menu-btn")
  const mobileNav = document.getElementById("mobile-nav")

  mobileMenuBtn.addEventListener("click", () => {
    mobileNav.classList.toggle("open")
  })

  // Close mobile menu when clicking on a link
  const mobileNavLinks = document.querySelectorAll(".mobile-nav-link")
  mobileNavLinks.forEach((link) => {
    link.addEventListener("click", () => {
      mobileNav.classList.remove("open")
    })
  })

  // Close mobile menu when clicking outside
  document.addEventListener("click", (event) => {
    if (!mobileMenuBtn.contains(event.target) && !mobileNav.contains(event.target)) {
      mobileNav.classList.remove("open")
    }
  })

  // Form submission
  const contactForm = document.querySelector(".contact-form")
  if (contactForm) {
    contactForm.addEventListener("submit", (event) => {
      event.preventDefault()

      const nameInput = document.getElementById("name")
      const emailInput = document.getElementById("email")
      const messageInput = document.getElementById("message")

      // Simple validation
      if (!nameInput.value || !emailInput.value || !messageInput.value) {
        alert("Please fill in all fields")
        return
      }

      // Here you would typically send the form data to a server
      alert("Thank you for your message! I will get back to you soon.")
      contactForm.reset()
    })
  }

  // Smooth scrolling for anchor links
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      e.preventDefault()

      const targetId = this.getAttribute("href")
      if (targetId === "#") return

      const targetElement = document.querySelector(targetId)
      if (targetElement) {
        window.scrollTo({
          top: targetElement.offsetTop - 80, // Offset for header
          behavior: "smooth",
        })
      }
    })
  })
})
