class HomePage {
  constructor(page) {
    this.page = page;

    this.wishlistButton = page.locator(
      'button[aria-label="Toggle Wishlist"]'
    );

    this.cartLink = page.locator('a[href="/cart"]');
  }

  async openCatalog() {
    await this.page.goto('/catalog');
  }

  async openWishlist() {
    await this.page.goto('/wishlist');
  }

  async addFirstProductToWishlist() {
    await this.wishlistButton.first().click();
  }

  async removeFirstProductFromWishlist() {
    await this.wishlistButton.first().click();
  }

  async openCart() {
    await this.cartLink.click();
  }
}

module.exports = { HomePage };