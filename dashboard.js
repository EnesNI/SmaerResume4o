document.addEventListener("DOMContentLoaded", () => {
  // Sidebar toggle functionality
  const sidebarToggle = document.getElementById("sidebarToggle")
  const sidebar = document.getElementById("sidebar")

  if (sidebarToggle) {
    sidebarToggle.addEventListener("click", () => {
      sidebar.classList.toggle("open")
    })
  }

  // Close sidebar when clicking outside on mobile
  document.addEventListener("click", (e) => {
    if (window.innerWidth <= 768) {
      if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
        sidebar.classList.remove("open")
      }
    }
  })

  // Counter animation for stats
  function animateCounters() {
    const counters = document.querySelectorAll(".counter")

    counters.forEach((counter) => {
      const target = Number.parseInt(counter.textContent)
      const increment = target / 50 // Adjust speed here
      let current = 0

      const updateCounter = () => {
        if (current < target) {
          current += increment
          counter.textContent = Math.ceil(current)
          requestAnimationFrame(updateCounter)
        } else {
          counter.textContent = target
        }
      }

      updateCounter()
    })
  }

  // Intersection Observer for animations
  const observerOptions = {
    threshold: 0.1,
    rootMargin: "0px 0px -50px 0px",
  }

  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.style.opacity = "1"
        entry.target.style.transform = "translateY(0)"

        // Animate counters when stats section comes into view
        if (entry.target.classList.contains("stats-section")) {
          setTimeout(animateCounters, 300)
        }
      }
    })
  }, observerOptions)

  // Observe elements for animation
  const animatedElements = document.querySelectorAll(
    ".stat-card, .quick-actions-card, .recent-activity-card, .performance-card",
  )
  animatedElements.forEach((el) => observer.observe(el))

  // Also observe the stats section for counter animation
  const statsSection = document.querySelector(".stats-section")
  if (statsSection) {
    observer.observe(statsSection)
  }

  // Search functionality
  const searchInput = document.getElementById("searchInput")
  if (searchInput) {
    searchInput.addEventListener("input", (e) => {
      const searchTerm = e.target.value.toLowerCase()
      // You can implement search logic here
      console.log("Searching for:", searchTerm)

      // Example: Filter activity items
      const activityItems = document.querySelectorAll(".activity-item")
      activityItems.forEach((item) => {
        const fileName = item.querySelector(".activity-file").textContent.toLowerCase()
        if (fileName.includes(searchTerm)) {
          item.style.display = "flex"
        } else {
          item.style.display = searchTerm === "" ? "flex" : "none"
        }
      })
    })
  }

  // Notification button
  const notificationBtn = document.getElementById("notificationBtn")
  if (notificationBtn) {
    notificationBtn.addEventListener("click", () => {
      showNotification("You have 3 new notifications!", "info")
    })
  }

  // Add loading states to action buttons
  const actionItems = document.querySelectorAll(".action-item, .btn")
  actionItems.forEach((item) => {
    item.addEventListener("click", function (e) {
      // Add loading state
      this.classList.add("loading")

      // Remove loading state after navigation (for demo purposes)
      setTimeout(() => {
        this.classList.remove("loading")
      }, 1000)
    })
  })

  // Smooth scrolling for anchor links
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      e.preventDefault()
      const target = document.querySelector(this.getAttribute("href"))
      if (target) {
        target.scrollIntoView({
          behavior: "smooth",
          block: "start",
        })
      }
    })
  })

  // Real-time clock (optional)
  function updateTime() {
    const now = new Date()
    const timeString = now.toLocaleTimeString()
    const timeElement = document.querySelector(".current-time")
    if (timeElement) {
      timeElement.textContent = timeString
    }
  }

  // Update time every second
  setInterval(updateTime, 1000)
  updateTime()

  // Add hover effects to cards
  const cards = document.querySelectorAll(".stat-card, .action-item, .performance-item")
  cards.forEach((card) => {
    card.addEventListener("mouseenter", function () {
      this.style.transform = "translateY(-4px)"
    })

    card.addEventListener("mouseleave", function () {
      this.style.transform = "translateY(0)"
    })
  })

  // Handle window resize
  window.addEventListener("resize", () => {
    if (window.innerWidth > 768) {
      sidebar.classList.remove("open")
    }
  })
})

// Utility functions
function showNotification(message, type = "info") {
  // Create notification element
  const notification = document.createElement("div")
  notification.className = `notification notification-${type}`
  notification.textContent = message

  // Style the notification
  notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        background: ${type === "success" ? "#10b981" : type === "error" ? "#ef4444" : "#3b82f6"};
        color: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 10000;
        transform: translateX(100%);
        transition: transform 0.3s ease;
        font-weight: 500;
    `

  document.body.appendChild(notification)

  // Animate in
  setTimeout(() => {
    notification.style.transform = "translateX(0)"
  }, 100)

  // Remove after 3 seconds
  setTimeout(() => {
    notification.style.transform = "translateX(100%)"
    setTimeout(() => {
      if (document.body.contains(notification)) {
        document.body.removeChild(notification)
      }
    }, 300)
  }, 3000)
}

// AJAX function for dynamic content loading
function loadContent(url, targetElement) {
  const target = document.querySelector(targetElement)
  if (!target) return

  // Show loading state
  target.classList.add("loading")

  fetch(url)
    .then((response) => response.text())
    .then((data) => {
      target.innerHTML = data
      target.classList.remove("loading")
    })
    .catch((error) => {
      console.error("Error loading content:", error)
      target.classList.remove("loading")
      showNotification("Error loading content", "error")
    })
}

// Export functions for use in other scripts
window.dashboardUtils = {
  showNotification,
  loadContent,
}

// Initialize tooltips (if you want to add them later)
function initTooltips() {
  const tooltipElements = document.querySelectorAll("[data-tooltip]")
  tooltipElements.forEach((element) => {
    element.addEventListener("mouseenter", function () {
      const tooltip = document.createElement("div")
      tooltip.className = "tooltip"
      tooltip.textContent = this.getAttribute("data-tooltip")
      tooltip.style.cssText = `
                position: absolute;
                background: #1e293b;
                color: white;
                padding: 0.5rem;
                border-radius: 4px;
                font-size: 0.8rem;
                z-index: 10000;
                pointer-events: none;
            `
      document.body.appendChild(tooltip)

      const rect = this.getBoundingClientRect()
      tooltip.style.top = rect.top - tooltip.offsetHeight - 5 + "px"
      tooltip.style.left = rect.left + rect.width / 2 - tooltip.offsetWidth / 2 + "px"

      this._tooltip = tooltip
    })

    element.addEventListener("mouseleave", function () {
      if (this._tooltip) {
        document.body.removeChild(this._tooltip)
        this._tooltip = null
      }
    })
  })
}

// Call initTooltips if you have elements with data-tooltip attributes
// initTooltips();
