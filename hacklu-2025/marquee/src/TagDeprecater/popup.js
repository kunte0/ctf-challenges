document.addEventListener("DOMContentLoaded", () => {
    const replacementInput = document.getElementById("replacement")
    const saveButton = document.getElementById("save")
    const resetButton = document.getElementById("reset")


    const DEFAULT = `<img src="${chrome.runtime.getURL('no_entry.png')}" alt="STOP">`

    // Load current replacement string
    chrome.storage.sync.get("replacementString", ({ replacementString }) => {
        replacementInput.value = replacementString
    })

    // Save new replacement string
    saveButton.addEventListener("click", () => {
        const newReplacement = replacementInput.value || DEFAULT
        chrome.storage.sync.set({ replacementString: newReplacement }, () => {
            alert("Replacement string saved!")
        })
    })
    // Reset to default
    resetButton.addEventListener("click", () => {
        chrome.storage.sync.set({ replacementString: DEFAULT }, () => {
            replacementInput.value = DEFAULT
        })
    })
    // Listen for messages from content script
    window.addEventListener("message", (event) => {
        if (event.data.type === "GET") {
            chrome.storage.sync.get("replacementString", ({ replacementString }) => {
                event.source.postMessage({ type: "REPLACEMENT_STRING", replacementString }, event.origin)
            })
        } else if (event.data.type === "SET") {
            chrome.storage.sync.set({ replacementString: event.data.replacementString })
            replacementInput.value = event.data.replacementString
        }
    })
})