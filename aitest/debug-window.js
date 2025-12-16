const { chromium } = require('@playwright/test');
const fs = require('fs').promises;

// Toggle for taking screenshots on page load (set to true to enable)
const takeScreenshots = false;

async function runDebugWindow() {
  const browser = await chromium.launch({ headless: false, args: ['--start-maximized'] });
  const page = await browser.newPage();

  // Listen for console messages
  page.on('console', msg => {
    const type = msg.type();
    if (type === 'error' || type === 'log') {
      const location = msg.location();
      const locStr = location ? ` at ${location.url}:${location.lineNumber}:${location.columnNumber}` : '';
      const logMsg = `Console ${type}: ${msg.text()}${locStr}`;
      if (type === 'error') {
        console.error(logMsg);
      } else {
        console.log(logMsg);
      }
    }
  });

  // Listen for page errors
  page.on('pageerror', error => {
    console.error(`Page error: ${error.message}`);
    if (error.stack) {
      console.error(`Stack trace: ${error.stack}`);
    }
  });

  // Listen for page load completion
  page.on('load', async () => {
    const url = page.url();
    console.log(`Page fully loaded: ${url}`);

    if (takeScreenshots) {
      // Wait 5 seconds for page to fully render
      await page.waitForTimeout(5000);

      // Ensure screenshots directory exists
      await fs.mkdir('screenshots', { recursive: true });

      // Take screenshot with URL in filename
      const timestamp = new Date().toISOString().replace(/[:.]/g, '-');
      const sanitizedUrl = url.replace(/https?:\/\//, '').replace(/[^a-zA-Z0-9]/g, '_');
      const filename = `screenshots/screenshot_${sanitizedUrl}_${timestamp}.png`;
      await page.screenshot({ path: filename, fullPage: true });
      console.log(`Screenshot saved: ${filename}`);
    }
  });

  // Navigate to the app
  await page.goto('http://localhost:9001');

  // Keep the browser open for manual testing
  console.log('Debug window opened. Perform manual testing. Press Ctrl+C to close.');

  // Wait indefinitely
  await new Promise(() => {}); // This will keep the script running
}

runDebugWindow().catch(console.error);
