const { test, expect } = require('@playwright/test');

const { LoginPage } = require('../../pages/LoginPage');
const { HomePage } = require('../../pages/HomePage');

test.describe('Halvora - End to End Testing', () => {

  test.beforeEach(async ({ page }) => {

    const loginPage = new LoginPage(page);

    const validEmail = 'maulmalikib@gmail.com';
    const validPassword = 'inipassword';

    await loginPage.goto();

    await expect(loginPage.emailInput).toBeVisible();
    await expect(loginPage.emailInput).toBeEnabled();

    await expect(loginPage.passwordInput).toBeVisible();
    await expect(loginPage.passwordInput).toBeEnabled();

    await loginPage.login(validEmail, validPassword);

    await expect(page).toHaveURL(
      'https://storefront-islamic.wins.web.id/'
    );
  });


  test('FVRT-001 - Menambahkan produk ke Favorite', async ({ page }) => {

    const homePage = new HomePage(page);

    await homePage.openCatalog();

    await expect(homePage.wishlistButton.first()).toBeVisible();
    await expect(homePage.wishlistButton.first()).toBeEnabled();

    await homePage.addFirstProductToWishlist();

  });


  test('FVRT-002 - Menghapus produk dari Favorite', async ({ page }) => {

    const homePage = new HomePage(page);

    await homePage.openWishlist();

    await expect(page).toHaveURL(/\/wishlist/);

    await expect(homePage.wishlistButton.first()).toBeVisible();
    await expect(homePage.wishlistButton.first()).toBeEnabled();

    await homePage.removeFirstProductFromWishlist();

  });


  test('SCRT-001 - Melihat produk di Keranjang', async ({ page }) => {

    const homePage = new HomePage(page);

    await expect(homePage.cartLink).toBeVisible();
    await expect(homePage.cartLink).toBeEnabled();

    await homePage.openCart();

    await expect(page).toHaveURL(/\/cart/);

  });

});