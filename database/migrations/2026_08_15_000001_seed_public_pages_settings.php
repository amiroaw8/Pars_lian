<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $defaults = [
            // --- قوانین و مقررات ---
            [
                'key'   => 'terms_meta_title',
                'value' => 'قوانین و مقررات - پارس لیان',
                'group' => 'public_pages',
                'label' => 'عنوان متا — صفحه قوانین',
                'type'  => 'text',
            ],
            [
                'key'   => 'terms_meta_desc',
                'value' => 'قوانین و شرایط استفاده از خدمات فروشگاه پارس لیان',
                'group' => 'public_pages',
                'label' => 'توضیحات متا — صفحه قوانین',
                'type'  => 'text',
            ],
            [
                'key'   => 'terms_content',
                'value' => '<h2>شرایط استفاده</h2><p>با استفاده از خدمات فروشگاه پارس لیان، شما شرایط و قوانین ذکر شده در این صفحه را می‌پذیرید.</p><h2>خرید و پرداخت</h2><p>تمامی قیمت‌ها به تومان بوده و شامل مالیات می‌باشند. پس از تأیید سفارش، امکان استرداد وجه طبق قوانین فروشگاه انجام می‌شود.</p>',
                'group' => 'public_pages',
                'label' => 'محتوای صفحه قوانین',
                'type'  => 'textarea',
            ],

            // --- سوالات متداول ---
            [
                'key'   => 'faq_meta_title',
                'value' => 'سوالات متداول - پارس لیان',
                'group' => 'public_pages',
                'label' => 'عنوان متا — صفحه سوالات متداول',
                'type'  => 'text',
            ],
            [
                'key'   => 'faq_meta_desc',
                'value' => 'پاسخ به سوالات متداول درباره خرید، ارسال و ضمانت محصولات پارس لیان',
                'group' => 'public_pages',
                'label' => 'توضیحات متا — صفحه سوالات متداول',
                'type'  => 'text',
            ],
            [
                'key'   => 'faq_content',
                'value' => '<h2>آیا محصولات اصل هستند؟</h2><p>بله، تمامی محصولات دارای گارانتی معتبر و اصالت کالا هستند.</p><h2>مدت زمان ارسال چقدر است؟</h2><p>سفارشات در کمتر از ۷۲ ساعت کاری ارسال می‌شوند.</p><h2>آیا امکان مرجوعی وجود دارد؟</h2><p>بله، تا ۷ روز پس از تحویل امکان مرجوعی وجود دارد.</p>',
                'group' => 'public_pages',
                'label' => 'محتوای صفحه سوالات متداول',
                'type'  => 'textarea',
            ],

            // --- حریم خصوصی ---
            [
                'key'   => 'privacy_meta_title',
                'value' => 'حریم خصوصی - پارس لیان',
                'group' => 'public_pages',
                'label' => 'عنوان متا — صفحه حریم خصوصی',
                'type'  => 'text',
            ],
            [
                'key'   => 'privacy_meta_desc',
                'value' => 'سیاست حریم خصوصی و نحوه استفاده از اطلاعات کاربران در فروشگاه پارس لیان',
                'group' => 'public_pages',
                'label' => 'توضیحات متا — صفحه حریم خصوصی',
                'type'  => 'text',
            ],
            [
                'key'   => 'privacy_content',
                'value' => '<h2>جمع‌آوری اطلاعات</h2><p>ما تنها اطلاعات ضروری برای پردازش سفارش شما را جمع‌آوری می‌کنیم.</p><h2>استفاده از اطلاعات</h2><p>اطلاعات شما در اختیار اشخاص ثالث قرار نمی‌گیرد و صرفاً برای ارائه خدمات بهتر استفاده می‌شود.</p>',
                'group' => 'public_pages',
                'label' => 'محتوای صفحه حریم خصوصی',
                'type'  => 'textarea',
            ],
        ];

        foreach ($defaults as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                array_merge($setting, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('key', [
            'terms_meta_title', 'terms_meta_desc', 'terms_content',
            'faq_meta_title',   'faq_meta_desc',   'faq_content',
            'privacy_meta_title','privacy_meta_desc','privacy_content',
        ])->delete();
    }
};
