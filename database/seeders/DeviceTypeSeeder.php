<?php

namespace Database\Seeders;

use App\Models\DeviceType;
use Illuminate\Database\Seeder;

class DeviceTypeSeeder extends Seeder
{
    public function run(): void
    {
        // انواع اصلی دستگاه‌ها
        $mainTypes = [
            'پرینتر' => [
                'HP LaserJet 1102',
                'Canon Pixma G3010',
                'Epson L3150',
                'Samsung ML-2165',
                'Brother DCP-L2540DW',
            ],
            'کامپیوتر' => [
                'Dell OptiPlex 3070',
                'HP ProDesk 400 G5',
                'Lenovo ThinkCentre M720',
                'Asus ExpertCenter D500SA',
                'Acer Veriton X2640G',
            ],
            'لپ‌تاپ' => [
                'Dell Latitude 3420',
                'HP ProBook 450 G8',
                'Lenovo ThinkPad E14',
                'Asus VivoBook 15',
                'Acer Aspire 5',
            ],
            'فکس' => [
                'Canon imageCLASS LBP113w',
                'Brother MFC-L2710DW',
                'HP LaserJet Pro MFP M130fw',
                'Epson WorkForce Pro WF-2860', // تغییر نام برای جلوگیری از duplicate
                'Panasonic KX-MB2000',
            ],
            'اسکنر' => [
                'Canon CanoScan LiDE 300',
                'Epson Perfection V39',
                'HP ScanJet Pro 2000 s2',
                'Brother ADS-2700W',
                'Fujitsu ScanSnap iX1500',
            ],
            'کپی' => [
                'Canon imageRUNNER 2204',
                'Ricoh MP 2014',
                'Xerox WorkCentre 6515',
                'Konica Minolta bizhub 225i',
                'Sharp MX-3070',
            ],
            'مولتی‌فانکشن' => [
                'HP OfficeJet Pro 9015',
                'Canon PIXMA TR8520',
                'Epson WorkForce WF-2860',
                'Brother MFC-J5330DW',
                'Samsung Xpress M2070W',
            ],
        ];

        foreach ($mainTypes as $typeName => $models) {
            // ایجاد نوع اصلی
            $parentType = DeviceType::firstOrCreate(['name' => $typeName]);

            // ایجاد مدل‌ها به عنوان فرزندان
            foreach ($models as $modelName) {
                DeviceType::firstOrCreate([
                    'name' => $modelName,
                    'parent_id' => $parentType->id,
                ]);
            }
        }
    }
}
