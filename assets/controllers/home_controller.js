import { Controller } from "@hotwired/stimulus"

// Connects to data-controller="home"
export default class extends Controller {
    static targets = ["counter", "message", "textInput", "textOutput"]

    connect() {
        console.log("Home Stimulus Controller connected! 🎉")
        this.clickCount = 0
        this.messageVisible = false
    }

    disconnect() {
        console.log("Home Stimulus Controller disconnected! 👋")
    }

    // Increment click counter
    increment() {
        this.clickCount++
        this.counterTarget.textContent = this.clickCount
        
        // Add some visual feedback
        this.counterTarget.classList.add("animate-pulse")
        setTimeout(() => {
            this.counterTarget.classList.remove("animate-pulse")
        }, 500)

        // Show special message at certain milestones
        if (this.clickCount % 10 === 0) {
            this.showNotification(`Wow! You've clicked ${this.clickCount} times! 🎉`)
        } else if (this.clickCount % 5 === 0) {
            this.showNotification(`${this.clickCount} clicks and counting! 🚀`)
        }
    }

    // Toggle message visibility
    toggleMessage() {
        this.messageVisible = !this.messageVisible
        
        if (this.messageVisible) {
            this.messageTarget.classList.remove("hidden")
            this.messageTarget.classList.add("animate-fade-in")
        } else {
            this.messageTarget.classList.add("hidden")
            this.messageTarget.classList.remove("animate-fade-in")
        }
    }

    // Update text output as user types
    updateText() {
        const inputValue = this.textInputTarget.value
        
        if (inputValue.trim() === "") {
            this.textOutputTarget.textContent = "Your input will appear here..."
            this.textOutputTarget.classList.remove("font-semibold", "text-blue-700")
        } else {
            this.textOutputTarget.textContent = `You typed: "${inputValue}"`
            this.textOutputTarget.classList.add("font-semibold", "text-blue-700")
        }
    }

    // Reset everything
    reset() {
        this.clickCount = 0
        this.counterTarget.textContent = "0"
        this.messageTarget.classList.add("hidden")
        this.messageVisible = false
        this.textInputTarget.value = ""
        this.textOutputTarget.textContent = "Your input will appear here..."
        this.textOutputTarget.classList.remove("font-semibold", "text-blue-700")
        
        this.showNotification("Everything has been reset! ✨")
    }

    // Helper method to show notifications
    showNotification(message) {
        // Create notification element
        const notification = document.createElement("div")
        notification.className = "fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300"
        notification.textContent = message
        
        document.body.appendChild(notification)
        
        // Animate in
        setTimeout(() => {
            notification.classList.remove("translate-x-full")
        }, 100)
        
        // Animate out and remove
        setTimeout(() => {
            notification.classList.add("translate-x-full")
            setTimeout(() => {
                if (document.body.contains(notification)) {
                    document.body.removeChild(notification)
                }
            }, 300)
        }, 3000)
    }
}
