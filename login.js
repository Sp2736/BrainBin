// eye icon toggle function
function toggleVisibility(inputId, eyeIcon) {
  const input = document.getElementById(inputId)
  if (input.type === "password") {
    input.type = "text"
    eyeIcon.textContent = "(O_O)"
  } else {
    input.type = "password"
    eyeIcon.textContent = "( - _ - )"
  }
}

// Show popup notification
function showPopup(message, isSuccess = true) {
  const popup = document.getElementById("popupBanner")
  const popupMessage = document.getElementById("popupMessage")
  popupMessage.textContent = message
  popup.classList.add(isSuccess ? "success" : "error", "active")
  setTimeout(() => {
    popup.classList.remove("active")
  }, 4000)
}

function closePopup() {
  const popup = document.getElementById("popupBanner")
  popup.classList.remove("active")
}

function handleLoginSubmit(e) {
  e.preventDefault()
  const errors = []

  const username = document.getElementById("username").value.trim()
  const password = document.getElementById("password").value.trim()
  const remember = document.getElementById("remember")?.checked || false

  if (username === "") errors.push("Username is required.")
  if (password === "") errors.push("Password is required.")

  const errorBox = document.getElementById("errorMessages")

  if (errors.length > 0) {
    errorBox.innerHTML = `<ul type="none">` + errors.map((err) => `<li>${err}</li>`).join("") + "</ul>"
    errorBox.style.color = "red"
  } else {
    // Send to PHP backend
    const formData = new FormData()
    formData.append("username", username)
    formData.append("password", password)
    if (remember) formData.append("remember", "1")

    fetch("login.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        console.log("PHP Response:", data)
        if (data.status === "success") {
          errorBox.innerHTML = "<strong style='color:green;'>" + data.message + "</strong>"
          showPopup("Login Successful!", true)
          setTimeout(() => {
            if (data.role === "admin") {
              window.location.href = "admin_dashboard.php"
            } else {
              window.location.href = "dashboard.php"
            }
          }, 1500)
        } else if (data.message === "user_not_found") {
          errorBox.innerHTML = "<strong style='color:orange;'>User not found! Redirecting to Sign Up...</strong>"
          showPopup("User not found! Redirecting to Sign Up.", false)
          setTimeout(() => (window.location.href = "signup.html"), 2500)
        } else {
          errorBox.innerHTML = "<strong style='color:red;'>" + data.message + "</strong>"
          showPopup(data.message, false)
        }
      })
      .catch((error) => {
        console.error("Error:", error)
        errorBox.innerHTML = "<strong style='color:red;'>Error connecting to server!</strong>"
      })
  }
}

// Attach when DOM is ready
document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("loginForm")
  if (form) form.addEventListener("submit", handleLoginSubmit)

  // FAQ toggle
  const faqToggles = document.querySelectorAll(".faq-toggle")
  faqToggles.forEach((toggle) => {
    toggle.addEventListener("click", function () {
      this.classList.toggle("active")
      const content = this.nextElementSibling
      content.style.maxHeight = content.style.maxHeight ? null : content.scrollHeight + "px"
    })
  })
})
