# Playwright QA Bootcamp

Project Web Automation Testing menggunakan Playwright untuk tugas praktik SWQA - SAKTI.

## 🎯 Tujuan

Project ini dibuat untuk mempraktikkan:

- CSS Selector dan Playwright Built-in Locators
- Data-testid dan locator berbasis atribut
- Auto-waiting Playwright
- Asynchronous Assertion menggunakan `expect()`
- Page Object Model (POM)
- System Testing dan End-to-End Testing
- Playwright HTML Report

## 🛠️ Tools

- Playwright
- Node.js
- JavaScript
- Chromium / Desktop Chrome
- Playwright HTML Reporter

## 📁 Struktur Project

```text
Playwright_QA_Bootcamp/
│
├── pages/
│   ├── LoginPage.js
│   └── HomePage.js
│
├── tests/
│   ├── system/
│   │   └── login.spec.js
│   │
│   └── e2e/
│       └── checkout.spec.js
│
├── playwright-report/
│
├── playwright.config.js
├── package.json
└── README.md

🧪 Test Cases
System Testing
Test ID	Test Case
LGN-001	Login menggunakan email dan password valid
LGN-002	Login menggunakan email dan password tidak valid
LGN-003	Login tanpa mengisi email dan password
LPSW-001	Membuka halaman Lupa Password
DB-001	Membuka halaman Wishlist
End-to-End Testing
Test ID	Test Case
FVRT-001	Menambahkan produk ke Favorite
FVRT-002	Menghapus produk dari Favorite
SCRT-001	Melihat produk di Keranjang
▶️ Menjalankan Test

Jalankan seluruh test menggunakan:

npx playwright test

Playwright menjalankan test dalam mode headless sesuai konfigurasi project.

📊 HTML Report

Setelah test selesai, buka Playwright HTML Report dengan:

npx playwright show-report

Report berisi hasil eksekusi seluruh test case, termasuk status Passed/Failed dan detail setiap pengujian.

⚙️ Configuration

Konfigurasi Playwright terdapat pada:

playwright.config.js

Konfigurasi mencakup:

Test directory
HTML reporter
Base URL
Headless browser
Screenshot ketika test gagal
Trace pada retry
Chromium / Desktop Chrome
🧩 Page Object Model

Project menggunakan Page Object Model untuk memisahkan locator dan action dari test logic.

Page Object yang digunakan:

LoginPage.js
HomePage.js

Pendekatan ini membuat kode automation lebih terstruktur, reusable, dan mudah dipelihara.

✅ Test Result

Total test case:

System Testing: 5 test cases
End-to-End Testing: 3 test cases
Total: 8 test cases

Status terakhir:
8 Passed