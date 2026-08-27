const { test, expect } = require('@playwright/test');
const { LoginPage } = require('../../pages/LoginPage');

test.describe('Authentication - Login', () => {

  test('LGN-001 - Login menggunakan email dan password valid', async ({ page }) => {

    const loginPage = new LoginPage(page);

    const validEmail = 'maulmalikib@gmail.com';
    const validPassword = 'inipassword';

    await loginPage.goto();

    await expect(loginPage.emailInput).toBeVisible();
    await expect(loginPage.emailInput).toBeEnabled();

    await expect(loginPage.passwordInput).toBeVisible();
    await expect(loginPage.passwordInput).toBeEnabled();

    await loginPage.login(validEmail, validPassword);

    await expect(page).toHaveURL('https://storefront-islamic.wins.web.id/');
  });

  test('LGN-002 - Login menggunakan email dan password tidak valid', async ({ page }) => {
  await page.goto('/login');

  await page.locator('#email')
    .fill('maulmalikib@gmail.com');

  await expect(page.locator('#email'))
    .toHaveValue('maulmalikib@gmail.com');

  await page.locator('#password')
    .fill('invalidpassword');

  await expect(page.locator('#password'))
    .toHaveValue('invalidpassword');

  const loginButton = page.getByRole('button', { name: 'Log In' });

  await expect(loginButton).toBeVisible();
  await expect(loginButton).toBeEnabled();

  await loginButton.click();

  await expect(page).toHaveURL(/\/login/);

  await expect(
    page.getByText('Email atau password salah', { exact: true })
  ).toBeVisible();
});

test('LGN-003 - Login tanpa mengisi email dan password', async ({ page }) => {
  await page.goto('/login');

  const emailInput = page.locator('#email');
  const passwordInput = page.locator('#password');
  const loginButton = page.getByRole('button', { name: 'Log In' });

  // Verify email field
  await expect(emailInput).toBeVisible();
  await expect(emailInput).toBeEnabled();
  await expect(emailInput).toHaveAttribute('required', '');

  // Verify password field
  await expect(passwordInput).toBeVisible();
  await expect(passwordInput).toBeEnabled();
  await expect(passwordInput).toHaveAttribute('required', '');

  // Click Login without filling any field
  await loginButton.click();

  // User should remain on login page
  await expect(page).toHaveURL(/\/login/);

  // Verify fields remain empty
  await expect(emailInput).toHaveValue('');
  await expect(passwordInput).toHaveValue('');
});

test('LPSW-001 - Membuka halaman Lupa Password', async ({ page }) => {

  const loginPage = new LoginPage(page);

  await loginPage.goto();

  await expect(loginPage.forgotPasswordLink).toBeVisible();

  await loginPage.clickForgotPassword();

  await expect(page).toHaveURL(/\/forgot-password/);

});

test('DB-001 - Membuka halaman Wishlist', async ({ page }) => {

  const loginPage = new LoginPage(page);

  const validEmail = 'maulmalikib@gmail.com';
  const validPassword = 'inipassword';

  await loginPage.goto();

  await loginPage.login(validEmail, validPassword);

  // Pastikan login berhasil
  await expect(page).not.toHaveURL(/\/login/);

  // Wishlist harus tampil
  await expect(loginPage.wishlistLink).toBeVisible();
  await expect(loginPage.wishlistLink).toBeEnabled();

  // Buka Wishlist
  await loginPage.wishlistLink.click();

  // Verifikasi URL
  await expect(page).toHaveURL(/\/wishlist/);

});
});