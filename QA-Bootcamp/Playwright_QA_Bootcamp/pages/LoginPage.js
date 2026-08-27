class LoginPage {
  constructor(page) {
    this.page = page;

    this.emailInput = page.locator('#email');
    this.passwordInput = page.locator('#password');
    this.loginButton = page.getByRole('button', { name: 'Log In' });
    this.wishlistLink = page.locator('a[href="/wishlist"]');
    this.forgotPasswordLink = page.getByText('Forgot Password?');
  }

  async goto() {
    await this.page.goto('/login');
  }

  async login(email, password) {
    await this.emailInput.fill(email);
    await this.passwordInput.fill(password);
    await this.loginButton.click();
  }

  async clickForgotPassword() {
    await this.forgotPasswordLink.click();
  }
}

module.exports = { LoginPage };