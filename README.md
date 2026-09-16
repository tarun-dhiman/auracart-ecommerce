# ✨ AuraCart — Modern E-Commerce Platform & Admin Control Hub

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php)](https://php.net)
[![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css)](https://tailwindcss.com)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql)](https://mysql.com)

AuraCart is a modern, high-performance, production-grade e-commerce application designed with a customer storefront, a customer account portal, and a dedicated admin hub with immediate storefront reflection.

---

## 🌟 Key Features

### 🛍️ Customer Storefront
* **Homepage Experience**: Hero promotional slider, featured categories, new arrivals, bestsellers, and customer testimonials.
* **Product Catalog**: Multi-attribute filtering (category, subcategory, brand, price slider, in-stock status) and search with autocomplete suggestions.
* **Product Details**: Multi-angle image gallery, selectable variants with price modifiers, live stock counter, tabbed specifications, and customer review submission.
* **Shopping Cart & Coupons**: Persistent guest session cart automatically synced on login, AJAX slide-out cart drawer, percentage and fixed promo coupons (`WELCOME10`, `AURAFEST500`), 18% GST calculation, and free delivery thresholds.
* **Checkout & Multi-Gateway**: Saved address book, new delivery address creation, and gateway abstraction (**Cash on Delivery**, **Razorpay**, **Stripe**).
* **Order Tracking & Invoice**: 4-stage visual timeline (*Placed ➔ Processing ➔ Shipped ➔ Delivered*) with live courier tracking links and printable GST tax invoices.

### 👤 Customer Account Portal (`/account`)
* Order history with itemized breakdown and tracking.
* Self-service order cancellation for unfulfilled orders with automatic inventory restocking.
* Multi-address management with default billing and shipping markers.
* Wishlist management with one-click "Move to Cart".
* Profile management and secure password change.

### 🛡️ Admin Control Hub (`/admin`)
* **Dashboard Analytics**: Real-time sales statistics, revenue charts, order status distribution, and low-stock radars.
* **Catalog Management**: Full Product CRUD with multi-image uploads, variants, and **immediate live reflection on the storefront**.
* **Taxonomy**: Categories and subcategories with icons and banner visuals.
* **Inventory Control**: Real-time stock adjustments protected by row-level locking (`lockForUpdate`) with a complete audit trail log (`inventory_logs`).
* **Order Fulfillment**: Lifecycle order progression (`processing`, `shipped`, `delivered`), courier carrier assignment, and tracking numbers.
* **Customer Directory**: Registered customer list, order count, total spending, and one-click account deactivation/blocking.
* **Marketing & Promotions**: Discount coupons with minimum order values, usage limits, and storefront promotional banners.
* **Review Moderation**: Approve, reject, or delete customer reviews before they appear publicly.
* **Store Settings**: GSTIN, tax rates, shipping policies, and payment gateway credentials.

### 📱 API-Ready Architecture (`/api/v1/`)
* Ready for mobile apps (iOS, Android, React Native, Flutter):
  * `POST /api/v1/auth/login` & `POST /api/v1/auth/register`
  * `GET /api/v1/products` & `GET /api/v1/products/{id}`
  * `GET /api/v1/categories`
  * `GET /api/v1/cart` & `POST /api/v1/cart/add`

---

## 🔑 Default Credentials

| Portal | URL | Email | Password | Role |
| :--- | :--- | :--- | :--- | :--- |
| **Admin Hub** | `/admin/login` | `admin@auracart.com` | `password123` | Store Administrator |
| **Customer Account** | `/login` | `customer@auracart.com` | `password123` | Customer |

---

## 🚀 Installation & Setup

1. **Clone the Repository**:
   ```bash
   git clone <your-repository-url>
   cd auracart-ecommerce
   ```

2. **Install Dependencies**:
   ```bash
   composer install
   ```

3. **Configure Environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Set up your MySQL database credentials in `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=auracart_db
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Run Migrations & Seeders**:
   ```bash
   php artisan migrate:fresh --seed
   php artisan storage:link
   ```

5. **Start the Development Server**:
   ```bash
   php artisan serve
   ```
   Open your browser at [http://127.0.0.1:8000](http://127.0.0.1:8000).

---

## 🧪 Verification Suite

Run the end-to-end automated verification suite:
```bash
php tests/test_full_suite.php
```

All 35 core business tests (stock lock safety, cart calculations, order fulfillment, coupon engine, and view compilation) will execute cleanly.

---

## 📄 License
This project is open-source software licensed under the [MIT license](LICENSE).
