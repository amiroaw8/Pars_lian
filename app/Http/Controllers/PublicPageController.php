<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\View\View;

/**
 * کنترلر صفحات عمومی سایت (قوانین، سوالات متداول، حریم خصوصی)
 * این صفحات کاملاً مستقل از اتوماسیون و فروشگاه هستند.
 */
class PublicPageController extends Controller
{
    /**
     * صفحهٔ قوانین و مقررات — مسیر: /terms
     */
    public function terms(): View
    {
        $title   = Setting::get('terms_meta_title',   'قوانین و مقررات - پارس لیان');
        $metaDesc = Setting::get('terms_meta_desc',   'قوانین و شرایط استفاده از خدمات فروشگاه پارس لیان');
        $content = Setting::get('terms_content',      '<p>محتوای قوانین و مقررات در پنل مدیریت قابل تنظیم است.</p>');

        return view('shop.pages.terms', compact('title', 'metaDesc', 'content'));
    }

    /**
     * صفحهٔ سوالات متداول — مسیر: /faq
     */
    public function faq(): View
    {
        $title   = Setting::get('faq_meta_title',  'سوالات متداول - پارس لیان');
        $metaDesc = Setting::get('faq_meta_desc',  'پاسخ به سوالات متداول درباره خرید، ارسال و ضمانت محصولات پارس لیان');
        $content = Setting::get('faq_content',     '<p>محتوای سوالات متداول در پنل مدیریت قابل تنظیم است.</p>');

        return view('shop.pages.faq', compact('title', 'metaDesc', 'content'));
    }

    /**
     * صفحهٔ حریم خصوصی — مسیر: /privacy
     */
    public function privacy(): View
    {
        $title   = Setting::get('privacy_meta_title', 'حریم خصوصی - پارس لیان');
        $metaDesc = Setting::get('privacy_meta_desc', 'سیاست حریم خصوصی و نحوه استفاده از اطلاعات کاربران در فروشگاه پارس لیان');
        $content = Setting::get('privacy_content',    '<p>محتوای حریم خصوصی در پنل مدیریت قابل تنظیم است.</p>');

        return view('shop.pages.privacy', compact('title', 'metaDesc', 'content'));
    }
}
