<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'پردازنده Intel Core i7-13700KF',
                'slug' => 'intel-core-i7-13700kf',
                'description' => 'پردازنده Intel Core i7-13700KF با 16 هسته و فرکانس 5.4 گیگاهرتز',
                'short_description' => 'پردازنده قدرتمند Intel برای گیمینگ و کارهای سنگین',
                'sku' => 'CPU-INTEL-I7-13700KF',
                'price' => 8500000,
                'sale_price' => 7800000,
                'stock_quantity' => 15,
                'manage_stock' => true,
                'stock_status' => 'instock',
                'category_slug' => 'cpu',
                'is_featured' => true,
                'is_active' => true,
                'weight' => 0.1,
                'attributes' => [
                    'cores' => '16',
                    'threads' => '24',
                    'base_clock' => '3.4 GHz',
                    'boost_clock' => '5.4 GHz',
                    'socket' => 'LGA 1700',
                    'tdp' => '125W',
                ],
            ],
            [
                'name' => 'کارت گرافیک NVIDIA RTX 4070',
                'slug' => 'nvidia-rtx-4070',
                'description' => 'کارت گرافیک NVIDIA GeForce RTX 4070 با 12GB حافظه GDDR6X',
                'short_description' => 'کارت گرافیک قدرتمند برای گیمینگ 1440p و 4K',
                'sku' => 'GPU-NVIDIA-RTX4070',
                'price' => 25000000,
                'stock_quantity' => 8,
                'manage_stock' => true,
                'stock_status' => 'instock',
                'category_slug' => 'graphics-card',
                'is_featured' => true,
                'is_active' => true,
                'weight' => 1.2,
                'attributes' => [
                    'memory' => '12GB GDDR6X',
                    'memory_bus' => '192-bit',
                    'core_clock' => '1920 MHz',
                    'boost_clock' => '2475 MHz',
                    'cuda_cores' => '5888',
                ],
            ],
            [
                'name' => 'رم Corsair Vengeance 16GB DDR4',
                'slug' => 'corsair-vengeance-16gb-ddr4',
                'description' => 'رم Corsair Vengeance LPX 16GB (2x8GB) DDR4-3200MHz',
                'short_description' => 'رم DDR4 با فرکانس بالا برای سیستم‌های گیمینگ',
                'sku' => 'RAM-CORSAIR-16GB-DDR4',
                'price' => 1200000,
                'stock_quantity' => 25,
                'manage_stock' => true,
                'stock_status' => 'instock',
                'category_slug' => 'ram',
                'is_featured' => false,
                'is_active' => true,
                'weight' => 0.15,
                'attributes' => [
                    'capacity' => '16GB (2x8GB)',
                    'speed' => 'DDR4-3200',
                    'cas_latency' => '16',
                    'voltage' => '1.35V',
                ],
            ],
            [
                'name' => 'SSD Samsung 980 PRO 1TB',
                'slug' => 'samsung-980-pro-1tb',
                'description' => 'SSD سامسونگ 980 PRO با ظرفیت 1TB و سرعت خواندن 7000MB/s',
                'short_description' => 'SSD NVMe با سرعت فوق‌العاده بالا',
                'sku' => 'SSD-SAMSUNG-980PRO-1TB',
                'price' => 2800000,
                'sale_price' => 2500000,
                'stock_quantity' => 12,
                'manage_stock' => true,
                'stock_status' => 'instock',
                'category_slug' => 'ssd',
                'is_featured' => true,
                'is_active' => true,
                'weight' => 0.05,
                'attributes' => [
                    'capacity' => '1TB',
                    'interface' => 'PCIe 4.0',
                    'read_speed' => '7000 MB/s',
                    'write_speed' => '5100 MB/s',
                    'form_factor' => 'M.2 2280',
                ],
            ],
            [
                'name' => 'کیبورد مکانیکی Logitech MX Keys',
                'slug' => 'logitech-mx-keys-mechanical',
                'description' => 'کیبورد مکانیکی Logitech MX Keys با قابلیت اتصال چند دستگاهه',
                'short_description' => 'کیبورد مکانیکی بی‌سیم با باتری طولانی',
                'sku' => 'KB-LOGITECH-MXKEYS',
                'price' => 1800000,
                'stock_quantity' => 20,
                'manage_stock' => true,
                'stock_status' => 'instock',
                'category_slug' => 'keyboard',
                'is_featured' => false,
                'is_active' => true,
                'weight' => 0.8,
                'attributes' => [
                    'connection' => 'Bluetooth, USB-C',
                    'battery_life' => '10 months',
                    'layout' => 'Full-size',
                    'backlight' => 'No',
                    'switch_type' => 'Scissor',
                ],
            ],
            [
                'name' => 'مانیتور LG 27UK650 4K UHD',
                'slug' => 'lg-27uk650-4k-monitor',
                'description' => 'مانیتور LG 27UK650 با رزولوشن 4K UHD و پنل IPS',
                'short_description' => 'مانیتور 4K با کیفیت تصویر فوق‌العاده',
                'sku' => 'MON-LG-27UK650-4K',
                'price' => 6500000,
                'sale_price' => 5800000,
                'stock_quantity' => 6,
                'manage_stock' => true,
                'stock_status' => 'instock',
                'category_slug' => 'monitor',
                'is_featured' => true,
                'is_active' => true,
                'weight' => 5.5,
                'attributes' => [
                    'size' => '27 inch',
                    'resolution' => '3840x2160 (4K)',
                    'panel_type' => 'IPS',
                    'refresh_rate' => '60Hz',
                    'response_time' => '5ms',
                    'connectors' => 'HDMI, DisplayPort',
                ],
            ],
            [
                'name' => 'پردازنده AMD Ryzen 7 7800X3D',
                'slug' => 'amd-ryzen-7-7800x3d',
                'description' => 'پردازنده AMD Ryzen 7 7800X3D با 8 هسته و حافظه 3D V-Cache',
                'short_description' => 'پردازنده قدرتمند AMD برای گیمینگ',
                'sku' => 'CPU-AMD-RYZEN7-7800X3D',
                'price' => 7200000,
                'stock_quantity' => 10,
                'manage_stock' => true,
                'stock_status' => 'instock',
                'category_slug' => 'cpu',
                'is_featured' => true,
                'is_active' => true,
                'weight' => 0.1,
                'attributes' => [
                    'cores' => '8',
                    'threads' => '16',
                    'base_clock' => '4.2 GHz',
                    'boost_clock' => '5.0 GHz',
                    'socket' => 'AM5',
                    'tdp' => '120W',
                    'cache' => '104MB 3D V-Cache',
                ],
            ],
            [
                'name' => 'کیس کامپیوتر Corsair 4000D Airflow',
                'slug' => 'corsair-4000d-airflow-case',
                'description' => 'کیس Corsair 4000D Airflow با سیستم خنک‌کننده عالی',
                'short_description' => 'کیس Mid-Tower با قابلیت نصب رادهای 360mm',
                'sku' => 'CASE-CORSAIR-4000D-AIRFLOW',
                'price' => 850000,
                'stock_quantity' => 18,
                'manage_stock' => true,
                'stock_status' => 'instock',
                'category_slug' => 'computer-case',
                'is_featured' => false,
                'is_active' => true,
                'weight' => 4.2,
                'attributes' => [
                    'form_factor' => 'Mid-Tower',
                    'motherboard_support' => 'ATX, Micro-ATX, Mini-ITX',
                    'max_gpu_length' => '360mm',
                    'max_cpu_cooler_height' => '170mm',
                    'radiator_support' => '360mm',
                    'included_fans' => '2x 120mm',
                ],
            ],
        ];

        foreach ($products as $productData) {
            $slug = $productData['category_slug'];
            unset($productData['category_slug']);
            
            $category = \App\Models\ProductCategory::where('slug', $slug)->first();
            if ($category) {
                $productData['category_id'] = $category->id;
            } else {
                // Fallback to first category if slug not found
                $productData['category_id'] = \App\Models\ProductCategory::first()?->id;
            }
            
            \App\Models\Product::create($productData);
        }
    }
}
