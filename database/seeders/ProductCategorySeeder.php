<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // Parent categories
            [
                'name' => 'قطعات کامپیوتر',
                'slug' => 'computer-parts',
                'description' => 'قطعات سخت‌افزاری کامپیوتر و لپ‌تاپ',
                'sort_order' => 1,
                'is_active' => true,
                'children' => [
                    ['name' => 'پردازنده (CPU)', 'slug' => 'cpu', 'description' => 'پردازنده‌های Intel و AMD'],
                    ['name' => 'مادربرد', 'slug' => 'motherboard', 'description' => 'مادربردهای کامپیوتر'],
                    ['name' => 'رم (RAM)', 'slug' => 'ram', 'description' => 'ماژول‌های حافظه RAM'],
                    ['name' => 'هارد دیسک', 'slug' => 'hard-drive', 'description' => 'هارد دیسک‌های داخلی و خارجی'],
                    ['name' => 'SSD', 'slug' => 'ssd', 'description' => 'درایوهای حالت جامد'],
                    ['name' => 'کارت گرافیک', 'slug' => 'graphics-card', 'description' => 'کارت گرافیک NVIDIA و AMD'],
                    ['name' => 'پاور (منبع تغذیه)', 'slug' => 'power-supply', 'description' => 'منابع تغذیه کامپیوتر'],
                    ['name' => 'کیس کامپیوتر', 'slug' => 'computer-case', 'description' => 'کیس‌های کامپیوتر'],
                ],
            ],
            [
                'name' => 'لوازم جانبی',
                'slug' => 'accessories',
                'description' => 'لوازم جانبی کامپیوتر و لپ‌تاپ',
                'sort_order' => 2,
                'is_active' => true,
                'children' => [
                    ['name' => 'مانیتور', 'slug' => 'monitor', 'description' => 'صفحه نمایش کامپیوتر'],
                    ['name' => 'کیبورد', 'slug' => 'keyboard', 'description' => 'کیبوردهای مکانیکی و ممبرین'],
                    ['name' => 'ماوس', 'slug' => 'mouse', 'description' => 'ماوس‌های оптиکی و گیمینگ'],
                    ['name' => 'اسپیکر', 'slug' => 'speaker', 'description' => 'سیستم‌های صوتی'],
                    ['name' => 'وبکم', 'slug' => 'webcam', 'description' => 'دوربین‌های وب'],
                    ['name' => 'هدفون', 'slug' => 'headphone', 'description' => 'هدفون و هدست'],
                ],
            ],
            [
                'name' => 'لپ‌تاپ و نوت‌بوک',
                'slug' => 'laptops',
                'description' => 'لپ‌تاپ‌های گیمینگ و کاری',
                'sort_order' => 3,
                'is_active' => true,
                'children' => [
                    ['name' => 'لپ‌تاپ گیمینگ', 'slug' => 'gaming-laptops', 'description' => 'لپ‌تاپ‌های مخصوص بازی'],
                    ['name' => 'لپ‌تاپ کاری', 'slug' => 'business-laptops', 'description' => 'لپ‌تاپ‌های مناسب کار اداری'],
                    ['name' => 'اولترابوک', 'slug' => 'ultrabooks', 'description' => 'لپ‌تاپ‌های فوق‌العاده سبک'],
                    ['name' => 'اکسسوری لپ‌تاپ', 'slug' => 'laptop-accessories', 'description' => 'کیف، کولر و لوازم جانبی لپ‌تاپ'],
                ],
            ],
            [
                'name' => 'شبکه و اینترنت',
                'slug' => 'networking',
                'description' => 'تجهیزات شبکه و اینترنت',
                'sort_order' => 4,
                'is_active' => true,
                'children' => [
                    ['name' => 'روتر', 'slug' => 'router', 'description' => 'روترهای بی‌سیم و سیمی'],
                    ['name' => 'سوئیچ شبکه', 'slug' => 'switch', 'description' => 'سوئیچ‌های شبکه'],
                    ['name' => 'کارت شبکه', 'slug' => 'network-card', 'description' => 'کارت شبکه بی‌سیم و سیمی'],
                    ['name' => 'کابل شبکه', 'slug' => 'network-cable', 'description' => 'کابل‌های شبکه و اتصال'],
                ],
            ],
        ];

        foreach ($categories as $categoryData) {
            $children = $categoryData['children'] ?? [];
            unset($categoryData['children']);

            $parent = \App\Models\ProductCategory::create($categoryData);

            foreach ($children as $childData) {
                $childData['parent_id'] = $parent->id;
                \App\Models\ProductCategory::create($childData);
            }
        }
    }
}
