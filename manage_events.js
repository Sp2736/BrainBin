// Add new event
document.getElementById("addEventForm").addEventListener("submit", (e) => {
  e.preventDefault()

  const formData = new FormData()
  formData.append("action", "add_event")
  formData.append("event_name", document.getElementById("eventName").value)
  formData.append("event_date", document.getElementById("eventDate").value)
  formData.append("event_description", document.getElementById("eventDescription").value)
  formData.append("event_status", document.getElementById("eventStatus").value)

  fetch("event_actions.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.status === "success") {
        showMessage(data.message, "success")
        document.getElementById("addEventForm").reset()
        setTimeout(() => location.reload(), 1500)
      } else {
        showMessage(data.message, "error")
      }
    })
    .catch((error) => {
      console.error("Error:", error)
      showMessage("Error adding event!", "error")
    })
})

// Edit event
document.getElementById("editEventForm").addEventListener("submit", (e) => {
  e.preventDefault()

  const formData = new FormData()
  formData.append("action", "edit_event")
  formData.append("event_id", document.getElementById("editEventId").value)
  formData.append("event_name", document.getElementById("editEventName").value)
  formData.append("event_date", document.getElementById("editEventDate").value)
  formData.append("event_description", document.getElementById("editEventDescription").value)
  formData.append("event_status", document.getElementById("editEventStatus").value)

  fetch("event_actions.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.status === "success") {
        showMessage(data.message, "success")
        closeEditModal()
        setTimeout(() => location.reload(), 1500)
      } else {
        showMessage(data.message, "error")
      }
    })
    .catch((error) => {
      console.error("Error:", error)
      showMessage("Error updating event!", "error")
    })
})

// Open edit modal
function openEditModal(id, name, date, description, status) {
  document.getElementById("editEventId").value = id
  document.getElementById("editEventName").value = name
  document.getElementById("editEventDate").value = date
  document.getElementById("editEventDescription").value = description
  document.getElementById("editEventStatus").value = status
  document.getElementById("editModal").style.display = "block"
}

// Close edit modal
function closeEditModal() {
  document.getElementById("editModal").style.display = "none"
}

// Close modal when clicking outside
window.onclick = (event) => {
  const modal = document.getElementById("editModal")
  if (event.target === modal) {
    closeEditModal()
  }
}

// Toggle event status
function toggleEventStatus(eventId, currentStatus) {
  const newStatus = currentStatus === "open" ? "closed" : "open"
  const action = newStatus === "open" ? "open" : "close"

  if (!confirm(`Are you sure you want to ${action} this event?`)) return

  const formData = new FormData()
  formData.append("action", "toggle_status")
  formData.append("event_id", eventId)
  formData.append("new_status", newStatus)

  fetch("event_actions.php", {
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
      showMessage("Error toggling event status!", "error")
    })
}

// Delete event
function deleteEvent(eventId, eventName) {
  if (!confirm(`Are you sure you want to DELETE the event "${eventName}"? This action cannot be undone!`)) return

  const formData = new FormData()
  formData.append("action", "delete_event")
  formData.append("event_id", eventId)

  fetch("event_actions.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.status === "success") {
        showMessage(data.message, "success")
        const eventCard = document.querySelector(`[data-event-id="${eventId}"]`)
        if (eventCard) eventCard.remove()
      } else {
        showMessage(data.message, "error")
      }
    })
    .catch((error) => {
      console.error("Error:", error)
      showMessage("Error deleting event!", "error")
    })
}

// Show message
function showMessage(message, type) {
  const messageBox = document.getElementById("messageBox")
  messageBox.textContent = message
  messageBox.className = type
  messageBox.style.display = "block"
  messageBox.style.padding = "12px"
  messageBox.style.borderRadius = "8px"
  messageBox.style.fontWeight = "600"
  messageBox.style.textAlign = "center"
  messageBox.style.marginBottom = "20px"

  if (type === "success") {
    messageBox.style.backgroundColor = "#d4edda"
    messageBox.style.color = "#155724"
    messageBox.style.border = "1px solid #c3e6cb"
  } else {
    messageBox.style.backgroundColor = "#f8d7da"
    messageBox.style.color = "#721c24"
    messageBox.style.border = "1px solid #f5c6cb"
  }

  setTimeout(() => {
    messageBox.style.display = "none"
  }, 5000)
}
