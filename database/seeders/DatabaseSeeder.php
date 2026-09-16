<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Review;
use App\Models\Setting;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Admins & Customers
        $admin = User::firstOrCreate(
            ['email' => 'admin@auracart.com'],
            [
                'name' => 'Aura Admin',
                'phone' => '+91 99887 76655',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'is_blocked' => false,
            ]
        );

        $customer = User::firstOrCreate(
            ['email' => 'customer@auracart.com'],
            [
                'name' => 'Tarun Verma',
                'phone' => '+91 98765 43210',
                'password' => Hash::make('password123'),
                'role' => 'customer',
                'is_blocked' => false,
            ]
        );

        // Address for customer
        Address::firstOrCreate(
            ['user_id' => $customer->id, 'address_line1' => 'Flat 402, Lotus Residency'],
            [
                'full_name' => 'Tarun Verma',
                'phone' => '+91 98765 43210',
                'email' => 'customer@auracart.com',
                'address_line2' => 'Koramangala 4th Block',
                'city' => 'Bengaluru',
                'state' => 'Karnataka',
                'pincode' => '560034',
                'country' => 'India',
                'type' => 'both',
                'is_default' => true,
            ]
        );

        // 2. Settings
        $defaultSettings = [
            'store_name' => 'AuraCart Luxury E-Commerce',
            'store_email' => 'support@auracart.com',
            'store_phone' => '+91 98765 43210',
            'store_address' => '101 Horizon Towers, Bengaluru, Karnataka, 560001, India',
            'store_gst' => '29ABCDE1234F1Z5',
            'currency_symbol' => '₹',
            'tax_rate' => '18.0',
            'shipping_fee' => '99.0',
            'free_shipping_threshold' => '999.0',
            'enable_cod' => '1',
            'enable_razorpay' => '1',
            'razorpay_key_id' => 'rzp_test_sample_key',
            'razorpay_key_secret' => 'sample_secret_key',
            'enable_stripe' => '1',
            'stripe_publishable_key' => 'pk_test_sample_key',
            'stripe_secret_key' => 'sk_test_sample_secret',
        ];

        foreach ($defaultSettings as $key => $val) {
            Setting::set($key, $val);
        }

        // 3. Hero Banners & Promo Banners
        $banners = [
            [
                'type' => 'hero_slide',
                'title' => 'Redefine Modern Luxury & Lifestyle',
                'subtitle' => 'Curated premium audio, sustainable daily essentials, and cutting-edge fashion with express delivery.',
                'button_text' => 'Explore Collection',
                'link_url' => '/products',
                'image_path' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=1600&auto=format&fit=crop&q=80',
                'sort_order' => 1,
            ],
            [
                'type' => 'hero_slide',
                'title' => 'The Audiophile Sound Evolution',
                'subtitle' => 'Studio-grade acoustic precision with active noise cancellation and 40-hour ultra battery life.',
                'button_text' => 'Shop Acoustics',
                'link_url' => '/products?category=electronics',
                'image_path' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=1600&auto=format&fit=crop&q=80',
                'sort_order' => 2,
            ],
            [
                'type' => 'promo_strip',
                'title' => 'Exclusive Summer Release: Flat 20% Off With WELCOME10',
                'subtitle' => 'Free priority insured shipping on orders above ₹999 across India.',
                'button_text' => 'Claim Offer',
                'link_url' => '/products',
                'image_path' => 'https://images.unsplash.com/photo-1472851294608-062f824d29cc?w=1200&auto=format&fit=crop&q=80',
                'sort_order' => 1,
            ],
            [
                'type' => 'deal_banner',
                'title' => 'Eco-Conscious Oral Care',
                'subtitle' => 'Handcrafted pure neem wood bristles with natural antimicrobial benefits.',
                'button_text' => 'View Eco Range',
                'link_url' => '/products?category=wellness-personal-care',
                'image_path' => 'https://images.unsplash.com/photo-1544816155-12df9643f363?w=800&auto=format&fit=crop&q=80',
                'sort_order' => 1,
            ],
        ];

        foreach ($banners as $b) {
            Banner::updateOrCreate(['title' => $b['title']], $b);
        }

        // 4. Categories & Subcategories
        $catData = [
            [
                'name' => 'Electronics & Gadgets',
                'slug' => 'electronics',
                'description' => 'Flagship smart devices, wireless audio, smart home hubs and accessories.',
                'icon' => 'laptop',
                'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=800&auto=format&fit=crop&q=80',
                'is_featured' => true,
                'subcategories' => ['Smartphones & Tablets', 'Wireless Headphones & Earbuds', 'Smart Wearables & Watches', 'Audio Systems'],
            ],
            [
                'name' => 'Fashion & Apparel',
                'slug' => 'fashion',
                'description' => 'Contemporary minimalist designer clothing, footwear, and leather goods.',
                'icon' => 'shirt',
                'image' => 'https://images.unsplash.com/photo-1489987707025-afc232f7ea0f?w=800&auto=format&fit=crop&q=80',
                'is_featured' => true,
                'subcategories' => ['Men Casuals', 'Women Apparel', 'Footwear & Sneakers', 'Accessories & Bags'],
            ],
            [
                'name' => 'Wellness & Personal Care',
                'slug' => 'wellness-personal-care',
                'description' => 'Holistic personal hygiene, botanical extracts, organic skincare, and oral care.',
                'icon' => 'heart',
                'image' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?w=800&auto=format&fit=crop&q=80',
                'is_featured' => true,
                'subcategories' => ['Oral Care', 'Organic Skincare', 'Hair & Scalp Treatments', 'Aromatherapy Diffusers'],
            ],
            [
                'name' => 'Home & Living',
                'slug' => 'home-living',
                'description' => 'Architectural lighting, artisanal drinkware, ergonomic desks, and home decor.',
                'icon' => 'home',
                'image' => 'https://images.unsplash.com/photo-1513694203232-719a280e022f?w=800&auto=format&fit=crop&q=80',
                'is_featured' => true,
                'subcategories' => ['Minimalist Furniture', 'Smart Ambient Lighting', 'Kitchenware & Brews', 'Bedding & Linen'],
            ],
        ];

        $categoryMap = [];
        $subcategoryMap = [];

        foreach ($catData as $item) {
            $cat = Category::updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'name' => $item['name'],
                    'description' => $item['description'],
                    'icon' => $item['icon'],
                    'image' => $item['image'],
                    'is_featured' => $item['is_featured'],
                    'is_active' => true,
                ]
            );
            $categoryMap[$cat->slug] = $cat;

            foreach ($item['subcategories'] as $subName) {
                $subSlug = Str::slug($subName);
                $sub = Subcategory::updateOrCreate(
                    ['category_id' => $cat->id, 'slug' => $subSlug],
                    ['name' => $subName, 'is_active' => true]
                );
                $subcategoryMap[$subName] = $sub;
            }
        }

        // 5. Brands
        $brands = [
            ['name' => 'Apple', 'slug' => 'apple'],
            ['name' => 'Sony', 'slug' => 'sony'],
            ['name' => 'Bose', 'slug' => 'bose'],
            ['name' => 'Nike', 'slug' => 'nike'],
            ['name' => 'Zara', 'slug' => 'zara'],
            ['name' => 'Aura Organics', 'slug' => 'aura-organics'],
            ['name' => 'Dyson', 'slug' => 'dyson'],
            ['name' => 'Nordic Minimal', 'slug' => 'nordic-minimal'],
        ];

        $brandMap = [];
        foreach ($brands as $b) {
            $brandMap[$b['slug']] = Brand::updateOrCreate(['slug' => $b['slug']], ['name' => $b['name'], 'is_active' => true]);
        }

        // 6. Products
        $products = [
            [
                'name' => 'Neem Wooden Toothbrush',
                'slug' => 'neem-wooden-toothbrush',
                'sku' => 'SKU-NEEM-199',
                'category_slug' => 'wellness-personal-care',
                'sub_name' => 'Oral Care',
                'brand_slug' => 'aura-organics',
                'price' => 249.00,
                'sale_price' => 199.00,
                'stock_quantity' => 100,
                'low_stock_threshold' => 15,
                'short_description' => '100% biodegradable pure organic neem wood toothbrush with natural antimicrobial properties.',
                'description' => 'Crafted from ethically sourced organic Indian neem wood, this toothbrush naturally combats bacterial growth, promotes gum health, and eliminates single-use plastic waste from our oceans. The ergonomic contour provides superior grip while the charcoal-infused soft bristles gently polish enamel.',
                'specifications' => [
                    'Handle Material' => '100% Organic Seasoned Neem Wood',
                    'Bristles' => 'BPA-Free Charcoal Infused Dupont Nylon',
                    'Lifespan' => '3 Months Recommended',
                    'Country of Origin' => 'India',
                ],
                'tags' => ['sustainable', 'oral care', 'neem', 'eco-friendly', 'biodegradable'],
                'is_featured' => true,
                'is_bestseller' => true,
                'is_new' => true,
                'images' => [
                    'https://images.unsplash.com/photo-1544816155-12df9643f363?w=800&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1607613009820-a29f7bb81c04?w=800&auto=format&fit=crop&q=80',
                ],
                'variants' => [
                    ['variant_name' => 'Soft Charcoal Bristles', 'price' => 199.00, 'stock_quantity' => 60],
                    ['variant_name' => 'Medium Plant-Based Bristles', 'price' => 219.00, 'stock_quantity' => 40],
                ],
            ],
            [
                'name' => 'Sony WH-1000XM5 Wireless Noise Cancelling Headphones',
                'slug' => 'sony-wh-1000xm5-wireless-headphones',
                'sku' => 'SKU-SONY-XM5',
                'category_slug' => 'electronics',
                'sub_name' => 'Wireless Headphones & Earbuds',
                'brand_slug' => 'sony',
                'price' => 34990.00,
                'sale_price' => 29990.00,
                'stock_quantity' => 35,
                'low_stock_threshold' => 5,
                'short_description' => 'Industry-leading noise cancellation with dual processors and 8 microphones for unparalleled clarity.',
                'description' => 'The Sony WH-1000XM5 redefines distraction-free listening. Equipped with two processors controlling eight microphones, Auto NC Optimizer, and a precision-engineered 30mm driver unit with carbon fiber composite dome, you experience crystal-clear hands-free calling and high-resolution spatial audio.',
                'specifications' => [
                    'Driver Unit' => '30mm High-Resolution Dynamic Driver',
                    'Battery Life' => 'Up to 30 hours with ANC on, 40 hours ANC off',
                    'Fast Charging' => '3 min charge gives 3 hours playback',
                    'Bluetooth Version' => '5.2 with LDAC codec',
                    'Weight' => '250 grams',
                ],
                'tags' => ['audio', 'noise cancelling', 'sony', 'premium', 'wireless'],
                'is_featured' => true,
                'is_bestseller' => true,
                'is_new' => false,
                'images' => [
                    'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=800&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1484704849700-f032a568e944?w=800&auto=format&fit=crop&q=80',
                ],
                'variants' => [
                    ['variant_name' => 'Color: Midnight Black', 'price' => 29990.00, 'stock_quantity' => 20],
                    ['variant_name' => 'Color: Platinum Silver', 'price' => 29990.00, 'stock_quantity' => 15],
                ],
            ],
            [
                'name' => 'Apple Watch Series 9 GPS + Cellular (Midnight Aluminum)',
                'slug' => 'apple-watch-series-9-gps-cellular',
                'sku' => 'SKU-APPL-W9',
                'category_slug' => 'electronics',
                'sub_name' => 'Smart Wearables & Watches',
                'brand_slug' => 'apple',
                'price' => 54900.00,
                'sale_price' => 48900.00,
                'stock_quantity' => 22,
                'low_stock_threshold' => 4,
                'short_description' => 'Powerful S9 chip, brilliant 2000-nit Always-On Retina display, and Double Tap gesture control.',
                'description' => 'Experience magical interactions with Double Tap, allowing you to answer calls and snooze alarms without touching the screen. Features advanced health sensors including ECG, Blood Oxygen, and Sleep Stage tracking.',
                'specifications' => [
                    'Case Size' => '45mm Aluminum',
                    'Display' => 'Always-On Retina OLED (2000 nits)',
                    'Chipset' => 'S9 SiP with 64-bit dual-core processor',
                    'Water Resistance' => '50 meters swim-proof',
                ],
                'tags' => ['smartwatch', 'apple', 'fitness', 'wearable', 'gps'],
                'is_featured' => true,
                'is_bestseller' => false,
                'is_new' => true,
                'images' => [
                    'https://images.unsplash.com/photo-1546868871-7041f2a55e12?w=800&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1508685096489-7aacd43bd3b1?w=800&auto=format&fit=crop&q=80',
                ],
                'variants' => [
                    ['variant_name' => 'Size: 41mm Sport Band', 'price' => 44900.00, 'stock_quantity' => 10],
                    ['variant_name' => 'Size: 45mm Sport Band', 'price' => 48900.00, 'stock_quantity' => 12],
                ],
            ],
            [
                'name' => 'Nike Air Zoom Pegasus 40 Running Shoes',
                'slug' => 'nike-air-zoom-pegasus-40',
                'sku' => 'SKU-NIKE-PEG40',
                'category_slug' => 'fashion',
                'sub_name' => 'Footwear & Sneakers',
                'brand_slug' => 'nike',
                'price' => 11995.00,
                'sale_price' => 9995.00,
                'stock_quantity' => 45,
                'low_stock_threshold' => 8,
                'short_description' => 'Springy ride for every run, familiar responsiveness designed for road and track runners.',
                'description' => 'The Pegasus 40 returns with enhanced comfort in sensitive foot zones like the arch and toes. Powered by Nike React foam and dual Zoom Air units for a responsive, energizing bounce in every stride.',
                'specifications' => [
                    'Midsole' => 'Nike React Foam with Forefoot & Heel Zoom Air',
                    'Upper' => 'Single-layer engineered breathable mesh',
                    'Weight' => '288g (Men size 9)',
                    'Surface' => 'Road, Treadmill, Track',
                ],
                'tags' => ['shoes', 'sneakers', 'nike', 'running', 'sportswear'],
                'is_featured' => false,
                'is_bestseller' => true,
                'is_new' => true,
                'images' => [
                    'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=800&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1608231387042-66d1773070a5?w=800&auto=format&fit=crop&q=80',
                ],
                'variants' => [
                    ['variant_name' => 'Size: UK 8 / White & Crimson', 'price' => 9995.00, 'stock_quantity' => 15],
                    ['variant_name' => 'Size: UK 9 / White & Crimson', 'price' => 9995.00, 'stock_quantity' => 18],
                    ['variant_name' => 'Size: UK 10 / White & Crimson', 'price' => 9995.00, 'stock_quantity' => 12],
                ],
            ],
            [
                'name' => 'Minimalist Linen Relaxed Overshirt',
                'slug' => 'minimalist-linen-relaxed-overshirt',
                'sku' => 'SKU-ZARA-LINEN1',
                'category_slug' => 'fashion',
                'sub_name' => 'Men Casuals',
                'brand_slug' => 'zara',
                'price' => 3990.00,
                'sale_price' => 3290.00,
                'stock_quantity' => 50,
                'low_stock_threshold' => 10,
                'short_description' => 'Pure breathable European flax linen overshirt tailored for effortless summer layers.',
                'description' => 'Crafted from 100% European natural flax, this lightweight overshirt features tortoiseshell horn buttons, clean patch pockets, and a camp collar silhouette for breathable ease in any climate.',
                'specifications' => [
                    'Fabric' => '100% Sustainable European Flax Linen',
                    'Fit' => 'Relaxed boxy silhouette',
                    'Care' => 'Machine wash cold, air dry in shade',
                ],
                'tags' => ['apparel', 'linen', 'minimalist', 'summer', 'menswear'],
                'is_featured' => false,
                'is_bestseller' => false,
                'is_new' => true,
                'images' => [
                    'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=800&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1589310243389-96a5483213a8?w=800&auto=format&fit=crop&q=80',
                ],
                'variants' => [
                    ['variant_name' => 'Size: M (Chest 40")', 'price' => 3290.00, 'stock_quantity' => 20],
                    ['variant_name' => 'Size: L (Chest 42")', 'price' => 3290.00, 'stock_quantity' => 20],
                    ['variant_name' => 'Size: XL (Chest 44")', 'price' => 3290.00, 'stock_quantity' => 10],
                ],
            ],
            [
                'name' => 'Aura Smart Ultrasonic Ceramic Aroma Diffuser',
                'slug' => 'aura-smart-ultrasonic-aroma-diffuser',
                'sku' => 'SKU-AURA-DIFF1',
                'category_slug' => 'wellness-personal-care',
                'sub_name' => 'Aromatherapy Diffusers',
                'brand_slug' => 'aura-organics',
                'price' => 4499.00,
                'sale_price' => 3499.00,
                'stock_quantity' => 60,
                'low_stock_threshold' => 10,
                'short_description' => 'Handcrafted matte porcelain aroma diffuser with ambient LED glow and whisper-quiet misting.',
                'description' => 'Infuse your sanctuary with pure essential botanical oils. Features 2.4MHz ultrasonic vibration that preserves the therapeutic integrity of essential oils without heating elements, covering rooms up to 500 sq ft.',
                'specifications' => [
                    'Capacity' => '300ml water reservoir (up to 12 hrs mist)',
                    'Cover Material' => 'Matte Terracotta Hand-Poured Ceramic',
                    'Safety' => 'Auto shut-off when reservoir is depleted',
                ],
                'tags' => ['wellness', 'aromatherapy', 'home decor', 'essential oils'],
                'is_featured' => true,
                'is_bestseller' => true,
                'is_new' => false,
                'images' => [
                    'https://images.unsplash.com/photo-1608571423902-eed4a5ad8108?w=800&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1547887537-6158d64c35b3?w=800&auto=format&fit=crop&q=80',
                ],
            ],
            [
                'name' => 'Nordic Solid Oak Minimalist Coffee Table',
                'slug' => 'nordic-solid-oak-coffee-table',
                'sku' => 'SKU-NORD-TAB1',
                'category_slug' => 'home-living',
                'sub_name' => 'Minimalist Furniture',
                'brand_slug' => 'nordic-minimal',
                'price' => 18999.00,
                'sale_price' => 15499.00,
                'stock_quantity' => 18,
                'low_stock_threshold' => 3,
                'short_description' => 'Handcrafted solid white oak with organic bevelled contours and matte protective natural oil finish.',
                'description' => 'The cornerstone of modern living rooms. Solid kiln-dried European white oak with seamless dovetail joinery, sturdy tapered legs, and water-repellent organic oil wax coating that patinas gracefully with age.',
                'specifications' => [
                    'Dimensions' => '110cm (L) x 60cm (W) x 42cm (H)',
                    'Material' => 'Solid European White Oak',
                    'Finish' => 'Zero-VOC Natural Hardwax Oil',
                ],
                'tags' => ['furniture', 'oak', 'minimalist', 'interior', 'table'],
                'is_featured' => true,
                'is_bestseller' => false,
                'is_new' => true,
                'images' => [
                    'https://images.unsplash.com/photo-1533090161767-e6ffed986c88?w=800&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=800&auto=format&fit=crop&q=80',
                ],
            ],
            [
                'name' => 'Dyson Pure Cool HEPA Air Purifier & Tower Fan',
                'slug' => 'dyson-pure-cool-air-purifier',
                'sku' => 'SKU-DYSON-TP07',
                'category_slug' => 'home-living',
                'sub_name' => 'Smart Ambient Lighting',
                'brand_slug' => 'dyson',
                'price' => 45900.00,
                'sale_price' => 39900.00,
                'stock_quantity' => 14,
                'low_stock_threshold' => 2,
                'short_description' => 'Captures 99.95% of microscopic particles and allergens with Air Multiplier technology.',
                'description' => 'Fully sealed to HEPA H13 standard, trapping 99.95% of ultrafine pollutants including dust, pet dander, and volatile organic compounds. Intelligently reports real-time PM2.5 and PM10 air quality metrics.',
                'specifications' => [
                    'Filtration' => 'HEPA H13 + Activated Carbon Filtration',
                    'Airflow' => 'Up to 290 liters/second Air Multiplier tech',
                    'Sound Level' => 'Quiet operation at 42dB in Night Mode',
                ],
                'tags' => ['appliances', 'dyson', 'air purifier', 'smart home'],
                'is_featured' => false,
                'is_bestseller' => true,
                'is_new' => false,
                'images' => [
                    'https://images.unsplash.com/photo-1585771724684-38269d6639fd?w=800&auto=format&fit=crop&q=80',
                ],
            ],
        ];

        foreach ($products as $pData) {
            $cat = $categoryMap[$pData['category_slug']] ?? null;
            $sub = $subcategoryMap[$pData['sub_name']] ?? null;
            $brand = $brandMap[$pData['brand_slug']] ?? null;

            $product = Product::updateOrCreate(
                ['sku' => $pData['sku']],
                [
                    'name' => $pData['name'],
                    'slug' => $pData['slug'],
                    'category_id' => $cat ? $cat->id : 1,
                    'subcategory_id' => $sub ? $sub->id : null,
                    'brand_id' => $brand ? $brand->id : null,
                    'price' => $pData['price'],
                    'sale_price' => $pData['sale_price'],
                    'stock_quantity' => $pData['stock_quantity'],
                    'low_stock_threshold' => $pData['low_stock_threshold'],
                    'short_description' => $pData['short_description'],
                    'description' => $pData['description'],
                    'specifications' => $pData['specifications'] ?? null,
                    'tags' => $pData['tags'] ?? null,
                    'is_featured' => $pData['is_featured'],
                    'is_bestseller' => $pData['is_bestseller'],
                    'is_new' => $pData['is_new'],
                    'is_active' => true,
                    'views_count' => rand(150, 2400),
                ]
            );

            // Images
            $isPrimary = true;
            foreach ($pData['images'] as $idx => $imgUrl) {
                ProductImage::firstOrCreate(
                    ['product_id' => $product->id, 'image_path' => $imgUrl],
                    ['is_primary' => $isPrimary, 'sort_order' => $idx]
                );
                $isPrimary = false;
            }

            // Variants
            if (!empty($pData['variants'])) {
                foreach ($pData['variants'] as $v) {
                    ProductVariant::firstOrCreate(
                        ['product_id' => $product->id, 'variant_name' => $v['variant_name']],
                        [
                            'price' => $v['price'],
                            'stock_quantity' => $v['stock_quantity'],
                            'sku' => $product->sku . '-' . Str::upper(Str::random(4)),
                        ]
                    );
                }
            }

            // Reviews
            Review::firstOrCreate(
                ['product_id' => $product->id, 'user_id' => $customer->id],
                [
                    'rating' => 5,
                    'title' => 'Exceptional craft and pristine quality!',
                    'comment' => 'The attention to detail and packaging was immaculate. Works beyond my expectations and looks gorgeous on my desk.',
                    'is_verified_purchase' => true,
                    'status' => 'approved',
                ]
            );
        }

        // 7. Coupons
        Coupon::updateOrCreate(
            ['code' => 'WELCOME10'],
            [
                'type' => 'percentage',
                'value' => 10.00,
                'min_order_value' => 500.00,
                'max_discount' => 500.00,
                'start_date' => now()->subDays(10),
                'expiry_date' => now()->addYear(),
                'usage_limit' => 500,
                'usage_per_user' => 1,
                'is_active' => true,
            ]
        );

        Coupon::updateOrCreate(
            ['code' => 'AURAFEST500'],
            [
                'type' => 'fixed',
                'value' => 500.00,
                'min_order_value' => 2999.00,
                'start_date' => now()->subDays(5),
                'expiry_date' => now()->addMonths(6),
                'usage_limit' => 200,
                'usage_per_user' => 1,
                'is_active' => true,
            ]
        );

        // 8. Sample Order for customer
        $sampleProd = Product::where('slug', 'neem-wooden-toothbrush')->first();
        if ($sampleProd) {
            $order = Order::firstOrCreate(
                ['order_number' => 'ORD-2026-000001'],
                [
                    'user_id' => $customer->id,
                    'subtotal' => 398.00,
                    'discount_amount' => 39.80,
                    'coupon_code' => 'WELCOME10',
                    'tax_amount' => 64.48,
                    'shipping_amount' => 99.00,
                    'grand_total' => 521.68,
                    'payment_method' => 'cod',
                    'payment_status' => 'pending',
                    'order_status' => 'shipped',
                    'tracking_number' => 'DELHIVERY-AUR-9821374',
                    'carrier_name' => 'Delhivery Express',
                    'shipping_address' => [
                        'full_name' => 'Tarun Verma',
                        'email' => 'customer@auracart.com',
                        'phone' => '+91 98765 43210',
                        'address_line1' => 'Flat 402, Lotus Residency',
                        'address_line2' => 'Koramangala 4th Block',
                        'city' => 'Bengaluru',
                        'state' => 'Karnataka',
                        'pincode' => '560034',
                        'country' => 'India',
                    ],
                ]
            );

            OrderItem::firstOrCreate(
                ['order_id' => $order->id, 'product_id' => $sampleProd->id],
                [
                    'product_name' => $sampleProd->name,
                    'sku' => $sampleProd->sku,
                    'unit_price' => 199.00,
                    'quantity' => 2,
                    'subtotal' => 398.00,
                ]
            );
        }
    }
}