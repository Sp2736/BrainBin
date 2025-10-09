document.getElementById("addDriveResourceForm").addEventListener("submit", async function (e) {
  e.preventDefault()

  const formData = new FormData(this)

  try {
    const response = await fetch("admin_drive_actions.php?action=add", {
      method: "POST",
      body: formData,
    })

    const data = await response.json()

    showMessage(data.message, data.status === "success" ? "success" : "error")

    if (data.status === "success") {
      this.reset()
      setTimeout(() => location.reload(), 1500)
    }
  } catch (error) {
    console.error("[v0] Error adding resource:", error)
    showMessage("Error adding resource. Please try again.", "error")
  }
})

async function deleteResource(id) {
  if (!confirm("Are you sure you want to delete this Drive resource?")) {
    return
  }

  try {
    const response = await fetch(`admin_drive_actions.php?action=delete&id=${id}`, {
      method: "POST",
    })

    const data = await response.json()

    showMessage(data.message, data.status === "success" ? "success" : "error")

    if (data.status === "success") {
      setTimeout(() => location.reload(), 1500)
    }
  } catch (error) {
    console.error("[v0] Error deleting resource:", error)
    showMessage("Error deleting resource. Please try again.", "error")
  }
}

function showMessage(message, type) {
  const messageBox = document.getElementById("messageBox")
  messageBox.innerHTML = `
        <div style="padding: 15px; border-radius: 8px; background: ${type === "success" ? "#e8f5e9" : "#ffebee"}; 
                    color: ${type === "success" ? "#388e3c" : "#c62828"}; font-weight: 600;">
            ${message}
        </div>
    `

  setTimeout(() => {
    messageBox.innerHTML = ""
  }, 5000)
}
