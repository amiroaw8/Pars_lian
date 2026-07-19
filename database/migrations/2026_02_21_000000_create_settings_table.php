<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('group')->index(); // 'sms', 'print_receipt', 'print_invoice', etc.
            $table->string('label')->nullable();
            $table->string('type')->default('text'); // text, textarea, boolean, etc.
            $table->timestamps();
        });

        // Seed default values
        $defaultSettings = [
            // Print - Header
            [
                'key' => 'print_header_title',
                'value' => 'PARS LIAN',
                'group' => 'print_header',
                'label' => 'عنوان انگلیسی سربرگ',
                'type' => 'text'
            ],
            [
                'key' => 'print_header_subtitle',
                'value' => 'مرکز تخصصی تعمیرات',
                'group' => 'print_header',
                'label' => 'زیرعنوان سربرگ',
                'type' => 'text'
            ],
            
            // Print - Footer
            [
                'key' => 'print_footer_text',
                'value' => 'چاپ شده توسط سیستم مدیریت تعمیرات پارس لیان',
                'group' => 'print_footer',
                'label' => 'متن پاورقی',
                'type' => 'text'
            ],
            [
                'key' => 'print_footer_website',
                'value' => 'www.PLian.ir',
                'group' => 'print_footer',
                'label' => 'آدرس وب‌سایت در پاورقی',
                'type' => 'text'
            ],

            // Print - Receipt Terms
            [
                'key' => 'print_receipt_terms',
                'value' => "دستگاه فوق با مشخصات و ایراد ذکر شده تحویل گرفته شد.\nمرکز مسئولیتی در قبال اطلاعات شخصی روی دستگاه ندارد. لطفا قبل از تحویل بکاپ تهیه نمایید.\nهزینه نهایی پس از بررسی دقیق اعلام می‌گردد.\nمدت زمان تست دستگاه پس از تحویل ۲۴ ساعت می‌باشد.\nدر صورت عدم مراجعه تا یک ماه پس از اعلام تعمیر، مرکز مسئولیتی در قبال دستگاه ندارد.",
                'group' => 'print_receipt',
                'label' => 'شرایط پذیرش (رسید)',
                'type' => 'textarea'
            ],

            // Print - Invoice Notes
            [
                'key' => 'print_invoice_notes',
                'value' => 'با تشکر از انتخاب شما',
                'group' => 'print_invoice',
                'label' => 'توضیحات فاکتور',
                'type' => 'textarea'
            ],

            // Print - Thermal Footer
            [
                'key' => 'print_thermal_footer_1',
                'value' => 'با تشکر از انتخاب شما',
                'group' => 'print_thermal',
                'label' => 'متن پایین فیش حرارتی (خط ۱)',
                'type' => 'text'
            ],
            [
                'key' => 'print_thermal_footer_2',
                'value' => 'www.PLian.ir',
                'group' => 'print_thermal',
                'label' => 'متن پایین فیش حرارتی (خط ۲)',
                'type' => 'text'
            ],
        ];

        DB::table('settings')->insert($defaultSettings);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
