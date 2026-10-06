chrome.runtime.onInstalled.addListener(() => {
    // Set default replacement string
    chrome.storage.sync.set({
        replacementString: `<img src="${chrome.runtime.getURL('no_entry.png')}" alt="STOP">`
    })
})