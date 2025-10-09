// Toggle password visibility
function toggleVisibility(inputId, eyeIcon) {
  const field = document.getElementById(inputId)
  console.log(`Toggling password visibility for: ${inputId}`)

  if (field.type === "password") {
    field.type = "text"
    eyeIcon.textContent = "(O_O)"
  } else {
    field.type = "password"
    eyeIcon.textContent = "( - _ - )"
  }
}

// Form Submission with PHP POST request
function handleFormSubmit(e) {
  e.preventDefault()
  console.log("Form submit triggered")

  const errors = []
  const username = document.getElementById("username").value.trim()
  const email = document.getElementById("email")?.value.trim()
  const password = document.getElementById("password").value.trim()
  const confirmpwd = document.getElementById("confirmpwd").value.trim()
  const phone = document.getElementById("phone").value.trim()
  const dob = document.getElementById("dob").value.trim()
  const terms = document.getElementById("terms").checked

  if (username === "") errors.push("Username is required.")
  if (!/^\S+@\S+\.\S+$/.test(email)) errors.push("Invalid email format.")
  if (password.length < 6) errors.push("Password must be at least 6 characters.")
  if (confirmpwd === "") errors.push("Confirm password is required.")
  else if (password !== confirmpwd) errors.push("Passwords do not match.")
  if (phone === "") errors.push("Phone number is required.")
  if (!/^\d{10}$/.test(phone)) errors.push("Phone number must be 10 digits.")
  if (dob === "") errors.push("Date of birth is required.")
  if (!terms) errors.push("You must agree to the terms.")

  const errorBox = document.getElementById("errorMessages")

  if (errors.length > 0) {
    console.log("Form validation errors:", errors)
    errorBox.innerHTML = `<ul type="none">` + errors.map((e) => `<li>${e}</li>`).join("") + "</ul>"
    errorBox.style.color = "red"
  } else {
    const formData = new FormData()
    formData.append("username", username)
    formData.append("email", email)
    formData.append("password", password)
    formData.append("phone", phone)
    formData.append("dob", dob)

    console.log("Submitting form data to signup.php")

    fetch("signup.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        console.log("PHP Response:", data)
        if (data.status === "success") {
          errorBox.innerHTML = "<strong style='color:green;'>" + "Signup was Successful!" + "</strong>"
          setTimeout(() => (window.location.href = "login.html"), 2000)
        } else {
          errorBox.innerHTML = "<strong style='color:red;'>" + data.message + "</strong>"
        }
      })
      .catch((error) => {
        console.error("Error:", error)
        errorBox.innerHTML = "<strong style='color:red;'>Error submitting form!</strong>"
      })
  }
}

// Simple popup function so code doesn't break
function showPopup(message, success) {
  alert(message) // For now just a basic alert
}

// DOM Ready
document.addEventListener("DOMContentLoaded", () => {
  console.log("DOM fully loaded for signup page")

  const form = document.getElementById("registrationForm")
  if (form) {
    form.addEventListener("submit", handleFormSubmit)
  }

  const faqToggles = document.querySelectorAll(".faq-toggle")
  faqToggles.forEach((toggle) => {
    toggle.addEventListener("click", function () {
      console.log("FAQ clicked:", this.textContent.trim())
      this.classList.toggle("active")
      const content = this.nextElementSibling
      content.style.maxHeight = content.style.maxHeight ? null : content.scrollHeight + "px"
    })
  })

  const popupBtn = document.getElementById("open-popup")
  const popupModel = document.getElementById("popup-model")
  const closePopupBtn = document.getElementById("close-popup")

  if (popupBtn && popupModel && closePopupBtn) {
    popupBtn.addEventListener("click", (e) => {
      e.preventDefault()
      console.log("T&C popup opened")
      popupModel.classList.remove("hidden")
      document.body.classList.add("blurred")
    })

    closePopupBtn.addEventListener("click", () => {
      console.log("T&C popup closed")
      popupModel.classList.add("hidden")
      document.body.classList.remove("blurred")
    })

    window.addEventListener("click", (e) => {
      if (e.target === popupModel) {
        console.log("T&C popup closed by outside click")
        popupModel.classList.add("hidden")
        document.body.classList.remove("blurred")
      }
    })
  }
})
