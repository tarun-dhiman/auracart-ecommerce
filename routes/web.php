<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminBannerController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminCouponController;
use App\Http\Controllers\Admin\AdminCustomerController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminInventoryController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminReviewController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerDashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\OrderTrackingController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Customer Storefront Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

// Products
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/quick-view/{id}', [ProductController::class, 'quickView'])->name('products.quickview');
Route::get('/api/search/suggestions', [ProductController::class, 'searchSuggestions'])->name('search.suggestions');

// Cart
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/coupon/apply', [CartController::class, 'applyCoupon'])->name('cart.coupon.apply');
Route::post('/cart/coupon/remove', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');
Route::get('/cart/drawer', [CartController::class, 'drawer'])->name('cart.drawer');

// Wishlist
Route::post('/wishlist/toggle/{productId}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
Route::post('/wishlist/move-to-cart/{productId}', [WishlistController::class, 'moveToCart'])->name('wishlist.move-to-cart');

// Checkout
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
Route::match(['get', 'post'], '/checkout/verify/{orderNumber}', [CheckoutController::class, 'verifyPayment'])->name('checkout.verify');
Route::get('/checkout/success/{orderNumber}', [CheckoutController::class, 'success'])->name('checkout.success');

// Order Tracking & Invoice
Route::get('/track-order', [OrderTrackingController::class, 'index'])->name('orders.track');
Route::post('/track-order', [OrderTrackingController::class, 'track'])->name('orders.track.post');
Route::get('/orders/{orderNumber}/invoice', [InvoiceController::class, 'show'])->name('orders.invoice');

// Authentication
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Customer Account Portal
Route::middleware('auth')->prefix('account')->name('customer.')->group(function () {
    Route::get('/', [CustomerDashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/orders', [CustomerDashboardController::class, 'orders'])->name('orders');
    Route::get('/orders/{orderNumber}', [CustomerDashboardController::class, 'orderDetails'])->name('orders.show');
    Route::post('/orders/{orderNumber}/cancel', [CustomerDashboardController::class, 'cancelOrder'])->name('orders.cancel');
    Route::get('/profile', [CustomerDashboardController::class, 'profile'])->name('profile');
    Route::post('/profile', [CustomerDashboardController::class, 'updateProfile'])->name('profile.update');
    Route::post('/password', [CustomerDashboardController::class, 'updatePassword'])->name('password.update');
    Route::get('/addresses', [CustomerDashboardController::class, 'addresses'])->name('addresses');
    Route::post('/addresses', [CustomerDashboardController::class, 'storeAddress'])->name('addresses.store');
    Route::delete('/addresses/{id}', [CustomerDashboardController::class, 'deleteAddress'])->name('addresses.delete');
    Route::post('/addresses/{id}/default', [CustomerDashboardController::class, 'setDefaultAddress'])->name('addresses.default');
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist');
    Route::post('/reviews/{productId}', [ReviewController::class, 'store'])->name('reviews.store');
});

/*
|--------------------------------------------------------------------------
| Admin Panel Routes
|--------------------------------------------------------------------------
*/

Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login']);

Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [AdminDashboardController::class, 'index']);

    // Products Management
    Route::get('/products', [AdminProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [AdminProductController::class, 'create'])->name('products.create');
    Route::post('/products', [AdminProductController::class, 'store'])->name('products.store');
    Route::get('/products/{id}/edit', [AdminProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{id}', [AdminProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{id}', [AdminProductController::class, 'destroy'])->name('products.destroy');
    Route::post('/products/{id}/toggle', [AdminProductController::class, 'toggleStatus'])->name('products.toggle');
    Route::delete('/products/images/{id}', [AdminProductController::class, 'deleteImage'])->name('products.images.delete');

    // Categories Management
    Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
    Route::post('/subcategories', [AdminCategoryController::class, 'storeSubcategory'])->name('subcategories.store');
    Route::delete('/categories/{id}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');

    // Inventory Management
    Route::get('/inventory', [AdminInventoryController::class, 'index'])->name('inventory.index');
    Route::post('/inventory/{id}/adjust', [AdminInventoryController::class, 'adjust'])->name('inventory.adjust');

    // Orders Management
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{orderNumber}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::put('/orders/{orderNumber}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');

    // Customers Management
    Route::get('/customers', [AdminCustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/{id}', [AdminCustomerController::class, 'show'])->name('customers.show');
    Route::post('/customers/{id}/toggle-block', [AdminCustomerController::class, 'toggleBlock'])->name('customers.toggle-block');

    // Coupons Management
    Route::get('/coupons', [AdminCouponController::class, 'index'])->name('coupons.index');
    Route::post('/coupons', [AdminCouponController::class, 'store'])->name('coupons.store');
    Route::post('/coupons/{id}/toggle', [AdminCouponController::class, 'toggle'])->name('coupons.toggle');
    Route::delete('/coupons/{id}', [AdminCouponController::class, 'destroy'])->name('coupons.destroy');

    // Banners Management
    Route::get('/banners', [AdminBannerController::class, 'index'])->name('banners.index');
    Route::post('/banners', [AdminBannerController::class, 'store'])->name('banners.store');
    Route::delete('/banners/{id}', [AdminBannerController::class, 'destroy'])->name('banners.destroy');

    // Reviews Management
    Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::put('/reviews/{id}/status', [AdminReviewController::class, 'updateStatus'])->name('reviews.status');
    Route::delete('/reviews/{id}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');

    // Settings
    Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
});