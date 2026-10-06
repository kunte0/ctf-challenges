const express = require('express');
const puppeteer = require('puppeteer');


const { APPDOMAIN, FLAG } = process.env;

const app = express();
app.use(express.urlencoded({ extended: true }));


app.get('/', (req, res) => {
    res.redirect('/bot/');
});

app.get('/bot', (req, res) => {
    const { url } = req.query;
    if (url) {
        if (!url.startsWith('http://') && !url.startsWith('https://')) {
            return res.send(`<h2>Invalid URL!</h2>`);
        }

        console.log('Submitted URL:', url);
        try {
            visit(url);
        } catch (e) {
            console.log(e);
        }

        return res.send(`<h2>Visiting ... </h2>`);
    }

    return res.send(`
        <html>
        <body>
            <h1>Submit a URL </h1>
            <form action="/bot/" method="GET">
            <input type="text" name="url" placeholder="Enter a URL" required />
            <button type="submit">Submit</button>
            </form>
        </body>
        </html>
    `);
});


const sleep = d => new Promise(r => setTimeout(r, d));

const visit = async (url) => {
    let browser;
    try {
        browser = await puppeteer.launch({
            browser: 'firefox',
            headless: true,

        });

        const ctx = await browser.createBrowserContext();

        page = await ctx.newPage();
        await page.goto(`https://${APPDOMAIN}`, { timeout: 3000, waitUntil: 'domcontentloaded' });
        await sleep(2000);
        await page.waitForSelector('#itemName');
        await page.type('#itemName', FLAG);
        await sleep(500);
        await page.click('#addItemBtn')
        await sleep(2000);
        await page.close();

        page = await ctx.newPage();
        await page.goto(url, { timeout: 3000, waitUntil: 'domcontentloaded' });
        await page.click('body')

        await sleep(30000);

        await browser.close();
        browser = null;
    } catch (err) {
        console.log(err);
    } finally {
        if (browser) await browser.close();
    }
};



app.listen(80, () => {
    console.log(`Server running on http://localhost:80`);
});