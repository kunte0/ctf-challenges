const puppeteer = require('puppeteer');
const http = require('http');
const TIMEOUT_SECS = parseInt(process.env.TIMEOUT_SECS || '30', 10);
const FLAG = process.env.FLAG || 'flag{***************************}';
const EXTENSIONPATH = '/app/TagDeprecater';


if (process.argv.length < 3) {
    console.log(`Usage: node ${process.argv[1]} <url>`);
    process.exit(1);
}

const URL = process.argv[2];

if (!URL || URL === '' || typeof (URL) !== 'string' || !/^https?:\/\/./.test(URL)) {
    console.log('[bot] No URL provided!');
    process.exit(1);
}

// start the flag webserver
const flagServer = http.createServer((req, res) => {
    res.writeHead(200, { 'Content-Type': 'text/html' });
    res.end(`<marquee>${FLAG}</marquee>`);
});

flagServer.listen(1337);


(async () => {
    // launch a browser with our config
    const browser = await puppeteer.launch({
        headless: true,
        pipe: true,
        args: [
            `--disable-extensions-except=${EXTENSIONPATH}`,
            `--load-extension=${EXTENSIONPATH}`,
            // disable stuff we do not need
            '--disable-gpu', '--disable-software-rasterizer', '--disable-dev-shm-usage',
            // disable sandbox since it does not work inside docker
            '--no-sandbox',
            // no exploits please
            "--js-flags=--noexpose_wasm,--jitless",
        ],
    });

    // close the browser after TIMEOUT_SECS seconds
    setTimeout(() => {
        console.log('[bot] Timeout reached, closing browser');
        browser.close();
        process.exit(0);
    }, TIMEOUT_SECS * 1000);

    // open a new page
    let page = await browser.newPage();

    // log console messages
    page.on('console', msg => console.log('[console]', msg.text()));

    // open the link
    console.log(`[bot] Visiting URL: ${URL}`);
    await page.goto(URL);
})().catch(error => {
    console.log('[bot] Error:', error);
    process.exit(1);
});
