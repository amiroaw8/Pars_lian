<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class RealDataSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Product::truncate();
        ProductCategory::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Create Categories
        $categories = [
            ['name' => 'پردازنده', 'name_en' => 'CPU', 'slug' => 'cpu'],
            ['name' => 'کارت گرافیک', 'name_en' => 'GPU', 'slug' => 'gpu'],
            ['name' => 'لپ‌تاپ', 'name_en' => 'Laptop', 'slug' => 'laptop'],
            ['name' => 'مادربرد', 'name_en' => 'Motherboard', 'slug' => 'motherboard'],
            ['name' => 'حافظه رم', 'name_en' => 'RAM', 'slug' => 'ram'],
            ['name' => 'حافظه ذخیره‌سازی', 'name_en' => 'Storage', 'slug' => 'storage'],
        ];

        $categoryMap = [];
        foreach ($categories as $cat) {
            $model = ProductCategory::create([
                'name' => $cat['name'],
                'slug' => $cat['slug'],
                'description' => "دسته بندی حرفه‌ای {$cat['name']}",
                'is_active' => true,
            ]);
            $categoryMap[$cat['slug']] = $model->id;
        }

        // 2. Add Real Products
        $products = [
            [
                'name' => 'پردازنده اینتل Core i9-14900K',
                'name_en' => 'Intel Core i9-14900K Processor',
                'cat' => 'cpu',
                'price' => 34500000,
                'sale_price' => 32900000,
                'sku' => 'INT-I9-14900K',
                'stock_quantity' => 12,
                'short_description' => 'قدرتمندترین پردازنده نسل ۱۴ اینتل برای گیمینگ و رندرینگ حرفه‌ای.',
                'technical_specs' => [
                    'keys' => ['تعداد هسته', 'تعداد رشته', 'فرکانس بوست', 'سوکت'],
                    'values' => ['۲۴ هسته', '۳۲ رشته', '۶.۰ گیگاهرتز', 'LGA 1700']
                ],
                'is_featured' => true,
                'img' => 'https://images.unsplash.com/photo-1591799264318-7e6ef8ddb7ea?q=80&w=800&auto=format&fit=crop'
            ],
            [
                'name' => 'کارت گرافیک ایسوس ROG Strix RTX 4090 OC',
                'name_en' => 'ASUS ROG Strix GeForce RTX 4090 OC Edition',
                'cat' => 'gpu',
                'price' => 115000000,
                'sale_price' => 112500000,
                'sku' => 'ASUS-RTX4090-STRIX',
                'stock_quantity' => 5,
                'short_description' => 'پادشاه کارت گرافیک‌های دنیا با ۲۴ گیگابایت حافظه GDDR6X.',
                'technical_specs' => [
                    'keys' => ['حافظه', 'نوع حافظه', 'رابط حافظه', 'توان مصرفی'],
                    'values' => ['۲۴ گیگابایت', 'GDDR6X', '۳۸۴ بیت', '۴۵۰ وات']
                ],
                'is_featured' => true,
                'img' => 'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?q=80&w=800&auto=format&fit=crop'
            ],
            [
                'name' => 'لپ‌تاپ گیمینگ ایسوس ROG Zephyrus G14 2024',
                'name_en' => 'ASUS ROG Zephyrus G14 (2024) GA403',
                'cat' => 'laptop',
                'price' => 98000000,
                'sku' => 'ASUS-G14-2024',
                'stock_quantity' => 3,
                'short_description' => 'لپ‌تاپ گیمینگ ۱۴ اینچی با صفحه نمایش OLED و کارت گرافیک سری ۴۰.',
                'technical_specs' => [
                    'keys' => ['پردازنده', 'رم', 'کارت گرافیک', 'وزن'],
                    'values' => ['Ryzen 9 8945HS', '۳۲ گیگابایت', 'RTX 4070', '۱.۵ کیلوگرم']
                ],
                'is_featured' => true,
                'img' => 'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?q=80&w=800&auto=format&fit=crop'
            ],
            [
                'name' => 'حافظه اس اس دی سامسونگ 990 Pro 2TB',
                'name_en' => 'Samsung 990 Pro NVMe M.2 SSD 2TB',
                'cat' => 'storage',
                'price' => 12500000,
                'sale_price' => 11800000,
                'sku' => 'SAM-990PRO-2TB',
                'stock_quantity' => 25,
                'short_description' => 'سریع‌ترین حافظه SSD نسل ۴ سامسونگ با عملکرد بی‌نظیر.',
                'technical_specs' => [
                    'keys' => ['ظرفیت', 'سرعت خواندن', 'سرعت نوشتن'],
                    'values' => ['۲ ترابایت', '۷۴۵۰ MB/s', '۶۹۰۰ MB/s']
                ],
                'img' => 'https://images.unsplash.com/photo-1597872200370-493ced2614be?q=80&w=800&auto=format&fit=crop'
            ],
            [
                'name' => 'مادربرد ایسوس ROG MAXIMUS Z790 HERO',
                'name_en' => 'ASUS ROG MAXIMUS Z790 HERO Motherboard',
                'cat' => 'motherboard',
                'price' => 42000000,
                'sku' => 'ASUS-Z790-HERO',
                'stock_quantity' => 10,
                'short_description' => 'مادربرد فوق حرفه‌ای زِد ۷۹۰ برای اورکلاک و پایداری بالا.',
                'technical_specs' => [
                    'keys' => ['سوکت', 'چیپست', 'نوع رم', 'تعداد اسلات M.2'],
                    'values' => ['LGA 1700', 'Intel Z790', 'DDR5', '۵ عدد']
                ],
                'img' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?q=80&w=800&auto=format&fit=crop'
            ]
        ];

        foreach ($products as $p) {
            Product::create([
                'name' => $p['name'],
                'name_en' => $p['name_en'],
                'slug' => Str::slug($p['name_en']),
                'category_id' => $categoryMap[$p['cat']],
                'price' => $p['price'],
                'sale_price' => $p['sale_price'] ?? null,
                'sku' => $p['sku'],
                'stock_quantity' => $p['stock_quantity'],
                'short_description' => $p['short_description'],
                'technical_specs' => $p['technical_specs'],
                'is_featured' => $p['is_featured'] ?? false,
                'is_active' => true,
                'images' => [$p['img']],
            ]);
        }
    }
}
