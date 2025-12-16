import { test as base } from '@playwright/test';

export const test = base.extend({
  page: async ({ page }, use) => {
    const errors = [];

    page.on('console', msg => {
      if (msg.type() === 'error') {
        console.error(`Console error: ${msg.text()}`);
        errors.push(msg.text());
      }
    });

    page.on('pageerror', error => {
      console.error(`Page error: ${error.message}`);
      errors.push(error.message);
    });

    await use(page);

    // After test, check for errors
    if (errors.length > 0) {
      throw new Error(`Console errors detected: ${errors.join('; ')}`);
    }
  },
});

export { expect } from '@playwright/test';
