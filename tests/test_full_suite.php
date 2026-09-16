<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\InventoryLog;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ViewErrorBag;

// Share empty ViewErrorBag for CLI rendering
View::share('errors', new ViewErrorBag());

echo "========================================================\n";
echo " AURACART COMPREHENSIVE END-TO-END VERIFICATION SUITE \n";
echo "========================================================\n\n";

$passed = 0;
$failed = 0;

function assertTest($description, $condition) {
    global $passed, $failed;
    if ($condition) {
        echo "  [PASS] " . $description . "\n";
        $passed++;
    } else {
        echo "  [FAIL] " . $description . "\n";
        $failed++;
    }
}

// 1. Verify Database & Core Entities
echo "--- 1. Verifying Database & Core Entities ---\n";
$productCount = Product::count();
assertTest("Products exist in database (Count: {$productCount})", $productCount >= 8);

$neemBrush = Product::where('slug', 'neem-wooden-toothbrush')->first();
assertTest("Specific product 'neem-wooden-toothbrush' exists", $neemBrush !== null);
assertTest("Product effective sale price is ₹199", $neemBrush && $neemBrush->effective_price == 199.00);

$admin = User::where('email', 'admin@auracart.com')->first();
assertTest("Admin user exists with role 'admin'", $admin && $admin->role === 'admin');

$customer = User::where('email', 'customer@auracart.com')->first();
assertTest("Customer user exists with role 'customer'", $customer && $customer->role === 'customer');

$welcomeCoupon = Coupon::where('code', 'WELCOME10')->first();
assertTest("Coupon WELCOME10 exists and active (Min Order: ₹{$welcomeCoupon->min_order_value})", $welcomeCoupon && $welcomeCoupon->is_active);

// 2. Test Cart Service & Business Rules
echo "\n--- 2. Testing Cart & Discount Engine ---\n";
$cartService = app(CartService::class);
Auth::login($customer);
\App\Models\CouponUsage::where('user_id', $customer->id)->delete();
$welcomeCoupon->update(['times_used' => 0]);
$cartService->clearCart();

// Add Neem toothbrush (2 qty = ₹398, below ₹500 min order)
$cartService->addItem($neemBrush->id, 2);
$failCoupon = $cartService->applyCoupon('WELCOME10');
assertTest("Coupon rejected when cart is below ₹500 min threshold", $failCoupon['success'] === false);

// Add 1 more Neem toothbrush (3 qty = ₹597, above ₹500 min order)
$cartService->addItem($neemBrush->id, 1);
$cartSummary = $cartService->getSummary();
assertTest("Subtotal calculated accurately for 3 units (Subtotal: ₹{$cartSummary['subtotal']})", $cartSummary['subtotal'] == 597.00);

// Apply Coupon WELCOME10 (10% off ₹597 = ₹59.70)
$couponResult = $cartService->applyCoupon('WELCOME10');
assertTest("Applied coupon WELCOME10 successfully when subtotal >= ₹500", $couponResult['success'] === true);

$cartWithDiscount = $cartService->getSummary();
assertTest("Discount calculated 10% (Discount: ₹{$cartWithDiscount['discount']})", $cartWithDiscount['discount'] == 59.70);
assertTest("Tax calculated (GST 18%: ₹{$cartWithDiscount['tax']})", $cartWithDiscount['tax'] > 0);
assertTest("Shipping fee applied below ₹999 free threshold (Shipping: ₹{$cartWithDiscount['shipping']})", $cartWithDiscount['shipping'] == 99.00);

// 3. Test Order Placement & Inventory Service
echo "\n--- 3. Testing Order Checkout & Inventory Atomicity ---\n";
$orderService = app(OrderService::class);

$initialStock = $neemBrush->stock_quantity;
$shippingAddress = [
    'full_name' => 'Aarav Sharma',
    'phone' => '+91 98765 43210',
    'email' => 'aarav.sharma@example.com',
    'address_line1' => 'Flat 402, Lotus Residency',
    'address_line2' => 'Indiranagar 100ft Road',
    'city' => 'Bengaluru',
    'state' => 'Karnataka',
    'postal_code' => '560038',
    'country' => 'India',
];

$orderResult = $orderService->createOrder([
    'payment_method' => 'cod',
    'shipping_address' => $shippingAddress,
    'billing_address' => $shippingAddress,
    'customer_notes' => 'Please leave package at the security desk.',
]);

assertTest("Order checkout completed successfully", $orderResult['success'] === true);
$order = $orderResult['order'];

assertTest("Order generated with format ORD-YYYY-XXXXXX ({$order->order_number})", preg_match('/^ORD-\d{4}-\d{6}$/', $order->order_number) === 1);
assertTest("Order payment status is 'pending' for COD", $order->payment_status === 'pending');

// Check inventory deduction
$neemBrush->refresh();
assertTest("Stock decremented by 3 from {$initialStock} to {$neemBrush->stock_quantity}", $neemBrush->stock_quantity == ($initialStock - 3));

// Check inventory audit log
$log = InventoryLog::where('product_id', $neemBrush->id)->latest()->first();
assertTest("Inventory audit trail recorded type 'order_sale'", $log && $log->type === 'order_sale');

// Check cart emptied
$emptySummary = $cartService->getSummary();
assertTest("Cart cleared after successful checkout (items_count: {$emptySummary['items_count']})", $emptySummary['items_count'] === 0);

// 4. Test Immediate Storefront Reflection on Admin Product Creation
echo "\n--- 4. Testing Immediate Storefront Reflection ---\n";
$category = Category::first();
$newSku = 'TEST-' . time();
$newProduct = Product::create([
    'category_id' => $category->id,
    'name' => 'Handcrafted Brass Incense Holder',
    'slug' => 'handcrafted-brass-incense-holder-' . time(),
    'sku' => $newSku,
    'price' => 799.00,
    'stock_quantity' => 25,
    'is_active' => true,
    'is_featured' => true,
    'short_description' => 'Solid brass incense holder crafted by traditional artisans.',
    'description' => 'Detailed product description for testing immediate reflection.',
]);

$storefrontCheck = Product::active()->where('sku', $newSku)->first();
assertTest("Admin created product immediately queries in active storefront catalog", $storefrontCheck !== null && $storefrontCheck->name === 'Handcrafted Brass Incense Holder');

// Toggle active status to false
$newProduct->update(['is_active' => false]);
$inactiveCheck = Product::active()->where('sku', $newSku)->first();
assertTest("Admin disabled product immediately disappears from active storefront catalog", $inactiveCheck === null);

// Clean up test product
$newProduct->delete();

// 5. Test Order Status Transition & Tracking
echo "\n--- 5. Testing Order Lifecycle & Tracking ---\n";
$order->update(['order_status' => 'processing']);
assertTest("Order updated to 'processing'", $order->order_status === 'processing');

$order->update([
    'order_status' => 'shipped',
    'tracking_number' => 'BLUEDART-882193',
    'shipping_carrier' => 'Blue Dart Express'
]);
assertTest("Order updated to 'shipped' with carrier tracking details", $order->tracking_number === 'BLUEDART-882193');

$order->update([
    'order_status' => 'delivered',
    'payment_status' => 'paid'
]);
assertTest("Order updated to 'delivered' and payment 'paid'", $order->order_status === 'delivered' && $order->payment_status === 'paid');

// 6. Test Controller Rendering (Simulating real controller execution)
echo "\n--- 6. Testing Controller View Rendering ---\n";

// Home Controller
try {
    $homeView = app(\App\Http\Controllers\HomeController::class)->index()->render();
    assertTest("HomeController::index renders successfully", strlen($homeView) > 500);
} catch (\Throwable $e) {
    assertTest("HomeController error: " . $e->getMessage(), false);
}

// Product Catalog Controller
try {
    $productView = app(\App\Http\Controllers\ProductController::class)->index(request())->render();
    assertTest("ProductController::index renders successfully", strlen($productView) > 500);
} catch (\Throwable $e) {
    assertTest("ProductController error: " . $e->getMessage(), false);
}

// Product Details Controller
try {
    $productShowView = app(\App\Http\Controllers\ProductController::class)->show('neem-wooden-toothbrush')->render();
    assertTest("ProductController::show('neem-wooden-toothbrush') renders successfully", strlen($productShowView) > 500);
} catch (\Throwable $e) {
    assertTest("ProductController::show error: " . $e->getMessage(), false);
}

// Cart Controller
try {
    $cartView = app(\App\Http\Controllers\CartController::class)->index()->render();
    assertTest("CartController::index renders successfully", strlen($cartView) > 500);
} catch (\Throwable $e) {
    assertTest("CartController error: " . $e->getMessage(), false);
}

// Invoice Controller
try {
    $invoiceView = app(\App\Http\Controllers\InvoiceController::class)->show($order->order_number)->render();
    assertTest("InvoiceController::show renders printable invoice successfully", strlen($invoiceView) > 500);
} catch (\Throwable $e) {
    assertTest("InvoiceController error: " . $e->getMessage(), false);
}

// Admin Dashboard Controller
try {
    $adminDashView = app(\App\Http\Controllers\Admin\AdminDashboardController::class)->index()->render();
    assertTest("AdminDashboardController::index renders dashboard with charts successfully", strlen($adminDashView) > 500);
} catch (\Throwable $e) {
    assertTest("AdminDashboardController error: " . $e->getMessage(), false);
}

// Admin Customers Controller
try {
    $adminCustView = app(\App\Http\Controllers\Admin\AdminCustomerController::class)->index(request())->render();
    assertTest("AdminCustomerController::index renders customers directory successfully", strlen($adminCustView) > 500);
} catch (\Throwable $e) {
    assertTest("AdminCustomerController error: " . $e->getMessage(), false);
}

// Admin Customer Details Controller
try {
    $adminCustShow = app(\App\Http\Controllers\Admin\AdminCustomerController::class)->show($customer->id)->render();
    assertTest("AdminCustomerController::show renders customer profile & order history successfully", strlen($adminCustShow) > 500);
} catch (\Throwable $e) {
    assertTest("AdminCustomerController::show error: " . $e->getMessage(), false);
}

// Admin Coupons Controller
try {
    $adminCouponsView = app(\App\Http\Controllers\Admin\AdminCouponController::class)->index()->render();
    assertTest("AdminCouponController::index renders coupons management successfully", strlen($adminCouponsView) > 500);
} catch (\Throwable $e) {
    assertTest("AdminCouponController error: " . $e->getMessage(), false);
}

// Admin Banners Controller
try {
    $adminBannersView = app(\App\Http\Controllers\Admin\AdminBannerController::class)->index()->render();
    assertTest("AdminBannerController::index renders banners manager successfully", strlen($adminBannersView) > 500);
} catch (\Throwable $e) {
    assertTest("AdminBannerController error: " . $e->getMessage(), false);
}

// Admin Reviews Controller
try {
    $adminReviewsView = app(\App\Http\Controllers\Admin\AdminReviewController::class)->index(request())->render();
    assertTest("AdminReviewController::index renders reviews moderation successfully", strlen($adminReviewsView) > 500);
} catch (\Throwable $e) {
    assertTest("AdminReviewController error: " . $e->getMessage(), false);
}

// Admin Settings Controller
try {
    $adminSettingsView = app(\App\Http\Controllers\Admin\AdminSettingController::class)->index()->render();
    assertTest("AdminSettingController::index renders store configuration successfully", strlen($adminSettingsView) > 500);
} catch (\Throwable $e) {
    assertTest("AdminSettingController error: " . $e->getMessage(), false);
}

echo "\n========================================================\n";
echo " RESULTS: {$passed} Passed, {$failed} Failed\n";
echo "========================================================\n";

if ($failed > 0) {
    exit(1);
}
