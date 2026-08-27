<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\OrderStatusModel;
use App\Models\PaymentStatusModel;
use App\Models\ServiceOrderStatusModel;
use App\Support\AssignableTechnicians;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class SettingController extends Controller
{
    /**
     * Display a listing of the settings and SMS templates.
     */
    public function index()
    {
        // Check permissions (assuming admin/manager role)
        // This should be handled by middleware or policy, but adding a check here is good practice
        // if (!auth()->user()->isAdmin()) abort(403);

        $printSettings = Setting::query()
            ->whereIn('group', ['print_header', 'print_footer', 'print_receipt', 'print_invoice', 'print_thermal'])
            ->orderBy('group')
            ->orderBy('id')
            ->get()
            ->groupBy('group');

        $smsStatuses = ServiceOrderStatusModel::orderBy('id')->get();
        $shopOrderStatuses = OrderStatusModel::orderBy('id')->get();
        $shopPaymentStatuses = PaymentStatusModel::orderBy('id')->get();
        $smsCategories = \App\Support\SmsNotifications::categories();

        $securitySettings = Setting::query()->where('group', 'security')->orderBy('id')->get();

        /** @var \App\Models\User|null $currentUser */
        $currentUser = Auth::user();

        $serviceStaffCandidates = $currentUser?->isSuperAdmin()
            ? AssignableTechnicians::candidates()
            : collect();

        $selectedAssignableTechnicianIds = AssignableTechnicians::configuredIds();
        if ($selectedAssignableTechnicianIds === []) {
            $selectedAssignableTechnicianIds = AssignableTechnicians::allowedIds();
        }

        $siteLicenses = json_decode(Setting::get('site_licenses', '[]'), true);
        if (!is_array($siteLicenses)) {
            $siteLicenses = [];
        }

        $paymentSettings = \App\Services\Payment\PaymentGatewayManager::getSettings();
        $supportedGatewayDrivers = \App\Services\Payment\PaymentGatewayManager::supportedDrivers();

        return view('settings.index', compact(
            'printSettings',
            'smsStatuses',
            'shopOrderStatuses',
            'shopPaymentStatuses',
            'smsCategories',
            'securitySettings',
            'serviceStaffCandidates',
            'selectedAssignableTechnicianIds',
            'siteLicenses',
            'paymentSettings',
            'supportedGatewayDrivers'
        ));
    }

    /**
     * Update print settings.
     */
    public function update(Request $request)
    {
        $data = $request->except(['_token', '_method']);

        foreach ($data as $key => $value) {
            Setting::where('key', $key)->update(['value' => $value]);
        }

        return Redirect::back()->with('success', 'تنظیمات با موفقیت ذخیره شد.');
    }

    /**
     * Update SMS templates.
     */
    public function updateSmsTemplates(Request $request)
    {
        $templates = $request->input('templates', []);
        $enabled = $request->input('sms_enabled', []);

        foreach ($templates as $id => $template) {
            ServiceOrderStatusModel::where('id', $id)->update([
                'sms_template' => $template,
                'sms_enabled' => array_key_exists($id, $enabled),
            ]);
        }

        $shopOrderTemplates = $request->input('shop_order_templates', []);
        $shopOrderEnabled = $request->input('shop_order_sms_enabled', []);
        foreach ($shopOrderTemplates as $id => $template) {
            OrderStatusModel::where('id', $id)->update([
                'sms_template' => $template,
                'sms_enabled' => array_key_exists($id, $shopOrderEnabled),
            ]);
        }

        $shopPaymentTemplates = $request->input('shop_payment_templates', []);
        $shopPaymentEnabled = $request->input('shop_payment_sms_enabled', []);
        foreach ($shopPaymentTemplates as $id => $template) {
            PaymentStatusModel::where('id', $id)->update([
                'sms_template' => $template,
                'sms_enabled' => array_key_exists($id, $shopPaymentEnabled),
            ]);
        }

        $smsBooleanSettings = [
            'sms_order_registered' => 'ارسال پیامک هنگام ثبت سفارش تعمیر',
            'sms_debt_notification' => 'ارسال پیامک اطلاع بدهی',
            'sms_password_reset' => 'ارسال پیامک بازیابی رمز عبور',
            'sms_inventory_alert' => 'ارسال پیامک هشدار موجودی انبار',
        ];

        foreach ($smsBooleanSettings as $key => $label) {
            Setting::set($key, $request->boolean($key) ? '1' : '0', [
                'group' => str_starts_with($key, 'two_factor') ? 'security' : 'sms',
                'label' => $label,
                'type' => 'boolean',
            ]);
        }

        $smsTextSettings = [
            'sms_template_order_registered' => 'متن پیامک ثبت سفارش تعمیر',
            'sms_template_debt_notification' => 'متن پیامک اطلاع بدهی',
            'sms_template_two_factor' => 'متن پیامک کد ورود دو مرحله‌ای',
            'sms_template_password_reset' => 'متن پیامک بازیابی رمز عبور',
            'sms_template_inventory_item' => 'متن پیامک هشدار موجودی (تک کالا)',
            'sms_template_inventory_batch' => 'متن پیامک هشدار موجودی (گزارش دوره‌ای)',
            'sms_business_phone' => 'تلفن تماس (در پیامک‌ها)',
            'sms_business_address' => 'آدرس (در پیامک‌ها)',
        ];

        foreach ($smsTextSettings as $key => $label) {
            Setting::set($key, $request->input($key, ''), [
                'group' => 'sms',
                'label' => $label,
                'type' => 'text',
            ]);
        }

        return Redirect::back()->with('success', 'تنظیمات پیامک با موفقیت ذخیره شد.');
    }

    private function checkSuperAdmin(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (! $user?->isSuperAdmin()) {
            abort(403);
        }
    }

    public function updateSecurity(Request $request)
    {
        $this->checkSuperAdmin();

        Setting::set('two_factor_enabled', $request->boolean('two_factor_enabled') ? '1' : '0', [
            'group' => 'security',
            'label' => 'فعال‌سازی تایید دو مرحله‌ای برای کارکنان',
            'type' => 'boolean',
        ]);

        return Redirect::back()->with('success', 'تنظیمات امنیتی ذخیره شد.');
    }

    public function updateService(Request $request)
    {
        $this->checkSuperAdmin();

        $request->validate([
            'assignable_technician_ids' => ['nullable', 'array'],
            'assignable_technician_ids.*' => ['integer', 'exists:users,id'],
        ]);

        $ids = array_map('intval', $request->input('assignable_technician_ids', []));
        AssignableTechnicians::saveConfiguredIds($ids);

        return Redirect::back()->with('success', 'تنظیمات پذیرش و تکنسین ذخیره شد.');
    }

    public function addLicense(Request $request)
    {
        $this->checkSuperAdmin();

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'url' => 'required|url',
        ]);

        $licenses = json_decode(Setting::get('site_licenses', '[]'), true);
        if (!is_array($licenses)) {
            $licenses = [];
        }

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('public/licenses');
            $licenses[] = [
                'image' => str_replace('public/', 'storage/', $path),
                'url' => $request->input('url')
            ];

            Setting::set('site_licenses', json_encode($licenses), [
                'group' => 'general',
                'label' => 'مجوزات سایت',
                'type' => 'json'
            ]);
        }

        return Redirect::back()->with('success', 'مجوز با موفقیت افزوده شد.');
    }

    public function removeLicense(int $index)
    {
        $this->checkSuperAdmin();

        $licenses = json_decode(Setting::get('site_licenses', '[]'), true);
        if (is_array($licenses) && isset($licenses[$index])) {
            $imagePath = str_replace('storage/', 'public/', $licenses[$index]['image']);
            \Illuminate\Support\Facades\Storage::delete($imagePath);
            array_splice($licenses, $index, 1);
            
            Setting::set('site_licenses', json_encode($licenses), [
                'group' => 'general',
                'label' => 'مجوزات سایت',
                'type' => 'json'
            ]);
        }

        return Redirect::back()->with('success', 'مجوز حذف شد.');
    }

    public function updatePaymentGateways(Request $request)
    {
        $this->checkSuperAdmin();

        \App\Services\Payment\PaymentGatewayManager::saveSettings($request->all());

        return Redirect::back()->with('success', 'تنظیمات درگاه‌های پرداخت با موفقیت بروزرسانی شد.');
    }

    /**
     * ذخیره محتوای صفحات عمومی (قوانین، سوالات متداول، حریم خصوصی)
     */
    public function updatePublicPages(Request $request)
    {
        $keys = [
            'terms_meta_title', 'terms_meta_desc', 'terms_content',
            'faq_meta_title',   'faq_meta_desc',   'faq_content',
            'privacy_meta_title','privacy_meta_desc','privacy_content',
        ];

        foreach ($keys as $key) {
            if ($request->has($key)) {
                Setting::set($key, $request->input($key), [
                    'group' => 'public_pages',
                    'type'  => str_ends_with($key, '_content') ? 'textarea' : 'text',
                ]);
            }
        }

        return Redirect::back()->with('success', 'محتوای صفحات عمومی با موفقیت ذخیره شد.');
    }
}

