const { defineConfig, devices } = require('@playwright/test');

module.exports = defineConfig({
  testDir: './tests',

  reporter: 'html',

  use: {
    baseURL: 'https://storefront-islamic.wins.web.id',
    headless: true,
    screenshot: 'only-on-failure',
    trace: 'on-first-retry',
  },

  projects: [
    {
      name: 'chromium',
      use: { ...devices['Desktop Chrome'] },
    },
  ],
});