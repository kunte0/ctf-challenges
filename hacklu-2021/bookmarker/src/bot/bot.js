const puppeteer = require('puppeteer-core');

const TIMEOUT_SECS = parseInt(process.env.TIMEOUT_SECS || '30', 10);
const ADMIN_PASS = process.env.ADMIN_PASS || console.log('Admin pass missing Wtf')
const LOGIN_URL = process.env.LOGIN_URL || console.log('URL missing Wtf')


if (process.argv.length !== 3 && process.argv.length !== 4) {
  console.log(`Usage: node ${process.argv[1]} <url> [cookies]`);
  process.exit(1);
}

const url = process.argv[2];
const cookies = JSON.parse(process.argv[3] || '[]');

if (!url || url === '' || typeof(url) !== 'string') {
    console.log('No URL provided!');
    process.exit(1);
}

(async () => {
  // launch a browser with our config
  const browser = await puppeteer.launch({
    headless: true,
    executablePath: '/usr/bin/google-chrome',
    args: [
      // disable stuff we do not need
      '--disable-gpu', '--disable-software-rasterizer', '--disable-dev-shm-usage',

      // disable sandbox since it does not work inside docker
      // (but we will use seccomp at least)
      '--no-sandbox',
    ],
  });

  // open a new page
  let page = await browser.newPage();

  // set the cookies
  for (const cookieSite of cookies) {
    console.log('[Cookie]', 'Visiting', cookieSite.url);
    await page.goto(cookieSite.url);
    console.log('[Cookie]', 'Setting cookies:', ...cookieSite.cookies);
    await page.setCookie(...cookieSite.cookies);
  }


  // login 
  await page.goto(LOGIN_URL, { waitUntil: 'networkidle0' }); // wait until page load
  await page.type('#username', 'admin');
  await page.type('#password', ADMIN_PASS);
  // click and wait for navigation
  await Promise.all([
    page.click('#submit'),
    page.waitForNavigation({ waitUntil: 'networkidle0' }),
  ]);

  // avoid leaking anything
  console.log('Opening new page');
  await page.close();
  page = await browser.newPage();

  page.on('console', (msg) => {
    console.log('[Console]', msg);
  });

  // close the browser after TIMEOUT_SECS seconds
  setTimeout(() => browser.close(), TIMEOUT_SECS * 1000);

  // open the link
  console.log('Visiting URL');
  await page.goto(url);
})().catch(error => {
  console.log('Error:', error);
  process.exit(1);
});
