// Change user role (promote/demote)
function changeRole(userId, newRole) {
  const action = newRole === "admin" ? "promote" : "demote"
  const confirmMsg = `Are you sure you want to ${action} this user to ${newRole}?`

  if (!confirm(confirmMsg)) return

  const formData = new FormData()
  formData.append("action", "change_role")
  formData.append("user_id", userId)
  formData.append("new_role", newRole)

  fetch("admin_actions.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.status === "success") {
        showMessage(data.message, "success")
        setTimeout(() => location.reload(), 1500)
      } else {
        showMessage(data.message, "error")
      }
    })
    .catch((error) => {
      console.error("Error:", error)
      showMessage("Error changing user role!", "error")
    })
}

// Delete user
function deleteUser(userId, username) {
  const confirmMsg = `Are you sure you want to DELETE user "${username}"? This action cannot be undone!`

  if (!confirm(confirmMsg)) return

  const formData = new FormData()
  formData.append("action", "delete_user")
  formData.append("user_id", userId)

  fetch("admin_actions.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.status === "success") {
        showMessage(data.message, "success")
        // Remove the row from table
        const row = document.querySelector(`tr[data-user-id="${userId}"]`)
        if (row) row.remove()
      } else {
        showMessage(data.message, "error")
      }
    })
    .catch((error) => {
      console.error("Error:", error)
      showMessage("Error deleting user!", "error")
    })
}

// Show message to user
function showMessage(message, type) {
  const messageBox = document.getElementById("messageBox")
  messageBox.textContent = message
  messageBox.className = type
  messageBox.style.display = "block"

  setTimeout(() => {
    messageBox.style.display = "none"
  }, 5000)
}

function scrollToSection(sectionId) {
  const section = document.getElementById(sectionId)
  if (section) {
    section.scrollIntoView({ behavior: "smooth", block: "start" })
  }
}

function filterUsers() {
  const searchInput = document.getElementById("searchUsers").value.toLowerCase()
  const roleFilter = document.getElementById("filterRole").value
  const statusFilter = document.getElementById("filterStatus").value
  const table = document.getElementById("userTable")
  const rows = table.getElementsByTagName("tbody")[0].getElementsByTagName("tr")

  for (let i = 0; i < rows.length; i++) {
    const row = rows[i]
    const username = row.cells[1].textContent.toLowerCase()
    const email = row.cells[2].textContent.toLowerCase()
    const role = row.getAttribute("data-role")
    const status = row.getAttribute("data-status")

    const matchesSearch = username.includes(searchInput) || email.includes(searchInput)
    const matchesRole = !roleFilter || role === roleFilter
    const matchesStatus = !statusFilter || status === statusFilter

    if (matchesSearch && matchesRole && matchesStatus) {
      row.style.display = ""
    } else {
      row.style.display = "none"
    }
  }
}

function toggleStatus(userId, newStatus) {
  const action = newStatus === "active" ? "activate" : "deactivate"
  const confirmMsg = `Are you sure you want to ${action} this user?`

  if (!confirm(confirmMsg)) return

  const formData = new FormData()
  formData.append("action", "toggle_status")
  formData.append("user_id", userId)
  formData.append("new_status", newStatus)

  fetch("admin_actions.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.status === "success") {
        showMessage(data.message, "success")
        setTimeout(() => location.reload(), 1500)
      } else {
        showMessage(data.message, "error")
      }
    })
    .catch((error) => {
      console.error("Error:", error)
      showMessage("Error changing user status!", "error")
    })
}

function exportData() {
  showMessage("Exporting data... This feature will be available soon!", "success")
}

document.addEventListener("DOMContentLoaded", () => {
  const uploadForm = document.getElementById("uploadMaterialForm")
  if (uploadForm) {
    uploadForm.addEventListener("submit", async (e) => {
      e.preventDefault()

      const formData = new FormData(uploadForm)
      const submitBtn = uploadForm.querySelector('button[type="submit"]')
      const originalText = submitBtn.textContent

      // Disable button and show loading state
      submitBtn.disabled = true
      submitBtn.textContent = "Uploading..."

      try {
        const response = await fetch("upload_material.php", {
          method: "POST",
          body: formData,
        })

        const data = await response.json()

        if (data.status === "success") {
          showMessage(data.message, "success")
          uploadForm.reset()
        } else {
          showMessage(data.message, "error")
        }
      } catch (error) {
        console.error("Error:", error)
        showMessage("Error uploading material!", "error")
      } finally {
        // Re-enable button
        submitBtn.disabled = false
        submitBtn.textContent = originalText
      }
    })
  }
})
