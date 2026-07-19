<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LargeStoreSeeder extends Seeder
{
    public function run(): void
    {
        // 1. ایجاد یا دریافت برندها
        $brandsData = [
            ['name' => 'اپل', 'en' => 'Apple'],
            ['name' => 'سامسونگ', 'en' => 'Samsung'],
            ['name' => 'شیائومی', 'en' => 'Xiaomi'],
            ['name' => 'سونی', 'en' => 'Sony'],
            ['name' => 'مایکروسافت', 'en' => 'Microsoft'],
            ['name' => 'ال‌جی', 'en' => 'LG'],
            ['name' => 'جی‌بی‌ال', 'en' => 'JBL'],
            ['name' => 'دلونگی', 'en' => 'DeLonghi'],
            ['name' => 'فیلیپس', 'en' => 'Philips'],
            ['name' => 'دیور', 'en' => 'Dior'],
            ['name' => 'نایکی', 'en' => 'Nike'],
            ['name' => 'آدیداس', 'en' => 'Adidas'],
            ['name' => 'نشر چشمه', 'en' => 'Cheshmeh'],
            ['name' => 'لگو', 'en' => 'LEGO'],
            ['name' => 'ایسوس', 'en' => 'ASUS'],
            ['name' => 'ام‌اس‌آی', 'en' => 'MSI'],
            ['name' => 'کورسیر', 'en' => 'Corsair'],
            ['name' => 'ریزر', 'en' => 'Razer'],
            ['name' => 'لاجیتک', 'en' => 'Logitech'],
            ['name' => 'انویدیا', 'en' => 'NVIDIA'],
            ['name' => 'ای‌ام‌دی', 'en' => 'AMD'],
            ['name' => 'اینتل', 'en' => 'Intel'],
            ['name' => 'کانن', 'en' => 'Canon'],
            ['name' => 'تی‌پی-لینک', 'en' => 'TP-Link'],
            ['name' => 'بوش', 'en' => 'Bosch'],
        ];

        $brandModels = [];
        foreach ($brandsData as $b) {
            $brandModels[$b['en']] = Brand::firstOrCreate(
                ['slug' => Str::slug($b['en'])],
                ['name' => $b['name'], 'name_en' => $b['en']]
            );
        }

        // 2. ایجاد یا دریافت دسته‌بندی‌ها
        $categories = [
            'گوشی موبایل' => 'Mobile Phones',
            'ساعت هوشمند' => 'Smartwatches',
            'تبلت' => 'Tablets',
            'کنسول بازی' => 'Game Consoles',
            'صوتی و تصویری' => 'Audio & Video',
            'لوازم خانگی برقی' => 'Home Appliances',
            'دوربین' => 'Cameras',
            'لوازم آرایشی' => 'Cosmetics',
            'مد و پوشاک' => 'Fashion',
            'کتاب و لوازم تحریر' => 'Books & Stationery',
            'اسباب بازی' => 'Toys',
            'لپ‌تاپ' => 'Laptops',
            'قطعات کامپیوتر' => 'PC Components',
            'مانیتور' => 'Monitors',
            'تجهیزات شبکه' => 'Network Equipment',
            'لوازم جانبی' => 'Accessories',
        ];

        $categoryModels = [];
        foreach ($categories as $fa => $en) {
            $categoryModels[$fa] = ProductCategory::firstOrCreate(
                ['name' => $fa],
                ['slug' => Str::slug($en)]
            );
        }

        // 3. محصولات
        $products = [
            // موبایل
            [
                'name' => 'گوشی موبایل اپل مدل iPhone 15 Pro Max',
                'name_en' => 'Apple iPhone 15 Pro Max 256GB',
                'brand_id' => $brandModels['Apple']->id,
                'category_id' => $categoryModels['گوشی موبایل']->id,
                'price' => 98000000,
                'sale_price' => 94500000,
                'stock_quantity' => 15,
                'is_featured' => true,
                'is_new' => true,
                'description' => 'پرچمدار جدید اپل با بدنه تیتانیومی و تراشه A17 Pro.',
                'technical_specs' => [
                    'صفحه نمایش' => '6.7 inch Super Retina XDR OLED',
                    'تراشه' => 'Apple A17 Pro (3 nm)',
                    'حافظه داخلی' => '256 GB',
                    'رم' => '8 GB',
                    'دوربین اصلی' => '48MP + 12MP + 12MP',
                ],
                'external_url' => 'https://www.apple.com/iphone-15-pro/',
            ],
            [
                'name' => 'گوشی موبایل سامسونگ مدل Galaxy S24 Ultra',
                'name_en' => 'Samsung Galaxy S24 Ultra 5G 256GB',
                'brand_id' => $brandModels['Samsung']->id,
                'category_id' => $categoryModels['گوشی موبایل']->id,
                'price' => 72000000,
                'sale_price' => 69800000,
                'stock_quantity' => 25,
                'is_featured' => true,
                'description' => 'قدرتمندترین گوشی اندرویدی با هوش مصنوعی Galaxy AI و قلم S-Pen.',
                'technical_specs' => [
                    'صفحه نمایش' => '6.8 inch Dynamic LTPO AMOLED 2X',
                    'تراشه' => 'Snapdragon 8 Gen 3 (4 nm)',
                    'حافظه داخلی' => '256 GB',
                    'رم' => '12 GB',
                ],
                'external_url' => 'https://www.samsung.com/global/galaxy/galaxy-s24-ultra/',
            ],
            [
                'name' => 'گوشی موبایل شیائومی 14 پرو',
                'name_en' => 'Xiaomi 14 Pro 5G 512GB',
                'brand_id' => $brandModels['Xiaomi']->id,
                'category_id' => $categoryModels['گوشی موبایل']->id,
                'price' => 52000000,
                'stock_quantity' => 10,
                'is_new' => true,
                'description' => 'گوشی قدرتمند شیائومی با دوربین لایکا.',
            ],

            // لپ‌تاپ
            [
                'name' => 'لپ‌تاپ گیمینگ ایسوس مدل ROG Strix G16',
                'name_en' => 'ASUS ROG Strix G16 (2023) Gaming Laptop',
                'brand_id' => $brandModels['ASUS']->id,
                'category_id' => $categoryModels['لپ‌تاپ']->id,
                'price' => 85000000,
                'sale_price' => 82500000,
                'stock_quantity' => 8,
                'is_featured' => true,
                'is_new' => true,
                'description' => 'لپ‌تاپ گیمینگ قدرتمند با گرافیک RTX 4060.',
                'technical_specs' => [
                    'پردازنده' => 'Core i7-13650HX',
                    'رم' => '16GB DDR5',
                    'حافظه' => '512GB SSD',
                    'گرافیک' => 'RTX 4060 8GB',
                ],
            ],
            [
                'name' => 'لپ‌تاپ اپل مدل MacBook Air M3',
                'name_en' => 'Apple MacBook Air 13-inch M3 Chip',
                'brand_id' => $brandModels['Apple']->id,
                'category_id' => $categoryModels['لپ‌تاپ']->id,
                'price' => 65000000,
                'stock_quantity' => 12,
                'is_new' => true,
                'description' => 'باریک‌ترین و سبک‌ترین لپ‌تاپ اپل با تراشه قدرتمند M3.',
            ],

            // قطعات کامپیوتر
            [
                'name' => 'کارت گرافیک ایسوس مدل ROG Strix RTX 4090',
                'name_en' => 'ASUS ROG Strix GeForce RTX 4090 OC Edition',
                'brand_id' => $brandModels['ASUS']->id,
                'category_id' => $categoryModels['قطعات کامپیوتر']->id,
                'price' => 125000000,
                'stock_quantity' => 3,
                'is_featured' => true,
                'description' => 'قوی‌ترین کارت گرافیک جهان برای گیمینگ و رندرینگ.',
            ],
            [
                'name' => 'پردازنده اینتل مدل Core i9-14900K',
                'name_en' => 'Intel Core i9-14900K Desktop Processor',
                'brand_id' => $brandModels['Intel']->id,
                'category_id' => $categoryModels['قطعات کامپیوتر']->id,
                'price' => 32000000,
                'stock_quantity' => 10,
                'description' => 'پردازنده نسل ۱۴ اینتل با فرکانس بوست فوق‌العاده.',
            ],
            [
                'name' => 'حافظه اس اس دی سامسونگ 990 Pro 2TB',
                'name_en' => 'Samsung 990 Pro 2TB PCIe 4.0 NVMe SSD',
                'brand_id' => $brandModels['Samsung']->id,
                'category_id' => $categoryModels['قطعات کامپیوتر']->id,
                'price' => 11500000,
                'stock_quantity' => 20,
                'is_featured' => true,
            ],

            // کنسول
            [
                'name' => 'کنسول بازی سونی مدل PlayStation 5 Slim',
                'name_en' => 'Sony PlayStation 5 Slim Digital Edition',
                'brand_id' => $brandModels['Sony']->id,
                'category_id' => $categoryModels['کنسول بازی']->id,
                'price' => 28500000,
                'sale_price' => 27200000,
                'stock_quantity' => 12,
                'is_featured' => true,
                'description' => 'نسخه جدید و باریک‌تر کنسول محبوب پلی‌استیشن ۵.',
                'technical_specs' => [
                    'پردازنده' => 'AMD Ryzen Zen 2',
                    'حافظه' => '1TB Custom SSD',
                ],
            ],

            // مانیتور
            [
                'name' => 'مانیتور گیمینگ ام اس آی مدل Optix G271',
                'name_en' => 'MSI Optix G271 27 inch Gaming Monitor',
                'brand_id' => $brandModels['MSI']->id,
                'category_id' => $categoryModels['مانیتور']->id,
                'price' => 12500000,
                'stock_quantity' => 15,
                'description' => 'مانیتور گیمینگ با نرخ تازه‌سازی ۱۴۴ هرتز.',
            ],

            // لوازم جانبی
            [
                'name' => 'ماوس گیمینگ لاجیتک مدل G502 HERO',
                'name_en' => 'Logitech G502 HERO High Performance Wired Gaming Mouse',
                'brand_id' => $brandModels['Logitech']->id,
                'category_id' => $categoryModels['لوازم جانبی']->id,
                'price' => 3500000,
                'stock_quantity' => 30,
                'is_featured' => true,
            ],
            [
                'name' => 'کیبورد مکانیکال ریزر مدل BlackWidow V4',
                'name_en' => 'Razer BlackWidow V4 Mechanical Gaming Keyboard',
                'brand_id' => $brandModels['Razer']->id,
                'category_id' => $categoryModels['لوازم جانبی']->id,
                'price' => 8500000,
                'stock_quantity' => 10,
                'is_new' => true,
            ],

            // صوتی تصویری
            [
                'name' => 'هدفون بی سیم سونی مدل WH-1000XM5',
                'name_en' => 'Sony WH-1000XM5 Wireless Noise Cancelling Headphones',
                'brand_id' => $brandModels['Sony']->id,
                'category_id' => $categoryModels['صوتی و تصویری']->id,
                'price' => 18500000,
                'sale_price' => 17800000,
                'stock_quantity' => 10,
                'is_featured' => true,
                'description' => 'بهترین هدفون نویزکنسلینگ جهان.',
            ],
            [
                'name' => 'اسپیکر بلوتوثی جی بی ال مدل Boombox 3',
                'name_en' => 'JBL Boombox 3 Waterproof Portable Bluetooth Speaker',
                'brand_id' => $brandModels['JBL']->id,
                'category_id' => $categoryModels['صوتی و تصویری']->id,
                'price' => 22500000,
                'stock_quantity' => 5,
            ],

            // لوازم خانگی
            [
                'name' => 'قهوه ساز دلونگی مدل EC685',
                'name_en' => 'DeLonghi Dedica Style EC685 Espresso Machine',
                'brand_id' => $brandModels['DeLonghi']->id,
                'category_id' => $categoryModels['لوازم خانگی برقی']->id,
                'price' => 8900000,
                'sale_price' => 8400000,
                'stock_quantity' => 20,
                'description' => 'اسپرسوساز نیمه صنعتی با طراحی بسیار باریک.',
            ],

            // گجت
            [
                'name' => 'ساعت هوشمند اپل مدل Watch Ultra 2',
                'name_en' => 'Apple Watch Ultra 2 GPS + Cellular',
                'brand_id' => $brandModels['Apple']->id,
                'category_id' => $categoryModels['ساعت هوشمند']->id,
                'price' => 44000000,
                'sale_price' => 41500000,
                'stock_quantity' => 12,
                'is_new' => true,
            ],

            // مد
            [
                'name' => 'کفش دویدن نایکی مدل Air Zoom Pegasus 40',
                'name_en' => 'Nike Air Zoom Pegasus 40 Road Running Shoes',
                'brand_id' => $brandModels['Nike']->id,
                'category_id' => $categoryModels['مد و پوشاک']->id,
                'price' => 7500000,
                'stock_quantity' => 20,
                'is_new' => true,
                'description' => 'کفش ورزشی نایکی با تکنولوژی ایر زوم.',
            ],

            // اسباب بازی
            [
                'name' => 'لگو سری تکنیک مدل ماشین مسابقه‌ای',
                'name_en' => 'LEGO Technic McLaren Senna GTR',
                'brand_id' => $brandModels['LEGO']->id,
                'category_id' => $categoryModels['اسباب بازی']->id,
                'price' => 4500000,
                'stock_quantity' => 5,
                'description' => 'مدل دقیق ماشین مک‌لارن.',
            ],

            // دوربین
            [
                'name' => 'دوربین دیجیتال سونی مدل Alpha a7 IV',
                'name_en' => 'Sony Alpha a7 IV Mirrorless Digital Camera',
                'brand_id' => $brandModels['Sony']->id,
                'category_id' => $categoryModels['دوربین']->id,
                'price' => 115000000,
                'stock_quantity' => 5,
                'is_featured' => true,
                'description' => 'دوربین حرفه‌ای بدون آینه با سنسور ۳۳ مگاپیکسلی.',
            ],
            [
                'name' => 'دوربین دیجیتال کانن مدل EOS R6 Mark II',
                'name_en' => 'Canon EOS R6 Mark II Mirrorless Camera',
                'brand_id' => $brandModels['Canon']->id,
                'category_id' => $categoryModels['دوربین']->id,
                'price' => 108000000,
                'stock_quantity' => 3,
                'is_new' => true,
            ],

            // لوازم خانگی بیشتر
            [
                'name' => 'ماشین لباسشویی بوش مدل WAW32560GC',
                'name_en' => 'Bosch WAW32560GC 9kg Washing Machine',
                'brand_id' => $brandModels['Bosch']->id,
                'category_id' => $categoryModels['لوازم خانگی برقی']->id,
                'price' => 42000000,
                'stock_quantity' => 8,
                'description' => 'ماشین لباسشویی سری ۸ بوش با ظرفیت ۹ کیلوگرم.',
            ],
            [
                'name' => 'تلویزیون اولد ال جی مدل C3 65 inch',
                'name_en' => 'LG C3 65 inch Series 4K Smart OLED evo TV',
                'brand_id' => $brandModels['LG']->id,
                'category_id' => $categoryModels['صوتی و تصویری']->id,
                'price' => 88000000,
                'sale_price' => 85500000,
                'stock_quantity' => 4,
                'is_featured' => true,
            ],

            // تجهیزات شبکه
            [
                'name' => 'مودم روتر تی پی لینک مدل Archer AX73',
                'name_en' => 'TP-Link Archer AX73 AX5400 Dual-Band Gigabit Wi-Fi 6 Router',
                'brand_id' => $brandModels['TP-Link']->id,
                'category_id' => $categoryModels['تجهیزات شبکه']->id,
                'price' => 7800000,
                'stock_quantity' => 15,
                'is_new' => true,
            ],

            // آرایشی
            [
                'name' => 'عطر دیور مدل Sauvage Elixir',
                'name_en' => 'Dior Sauvage Elixir 60ml',
                'brand_id' => $brandModels['Dior']->id,
                'category_id' => $categoryModels['لوازم آرایشی']->id,
                'price' => 9500000,
                'stock_quantity' => 20,
                'is_featured' => true,
            ],
        ];

        foreach ($products as $pData) {
            $product = Product::updateOrCreate(
                ['name_en' => $pData['name_en']],
                array_merge($pData, [
                    'sku' => 'PL-' . strtoupper(Str::random(8)),
                    'slug' => Str::slug($pData['name_en']),
                    'is_active' => true,
                    'manage_stock' => true,
                    'stock_status' => 'instock',
                    'images' => [
                        'https://picsum.photos/seed/' . Str::slug($pData['name_en']) . '/800/800',
                        'https://picsum.photos/seed/' . Str::slug($pData['name_en']) . '-2/800/800',
                    ],
                ])
            );

            $product->createVersion('درج اولیه از فروشگاه بزرگ');
        }
    }
}