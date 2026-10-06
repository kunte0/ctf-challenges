self.addEventListener('install', (event) => {
    console.log("SW Install")
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    console.log("SW Activate")
});

self.addEventListener('fetch', (event) => {
    if(event.request.url.includes('search/')){
        let res = fetch(event.request.url, {
            method: 'POST',
            mode: 'no-cors',
            credentials: 'include',
        })
    
        event.respondWith(res)
    }
    else{
        return
    }


});