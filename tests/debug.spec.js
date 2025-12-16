import { test, expect } from '../global-setup.js';

test('debug app for errors', async ({ page }) => {
  // Instead of navigating, check current page location dynamically
  const url = page.url();

  // Log current URL for debugging
  console.log(`Current page URL: ${url}`);

  // Basic checks
  await expect(page).toHaveTitle(/billionerp/i); // Case insensitive title check

  // Console errors are checked automatically by the fixture
});
