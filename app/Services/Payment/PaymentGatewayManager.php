<?php

namespace App\Services\Payment;

use App\Models\Setting;
use Illuminate\Support\Facades\Config;

class PaymentGatewayManager
{
    /**
     * Supported payment drivers metadata definitions.
     */
    public static function supportedDrivers(): array
    {
        return [
            'zarinpal' => [
                'name' => 'زرین‌پال',
                'driver' => 'zarinpal',
                'icon' => 'ti ti-brand-cashapp',
                'fields' => [
                    'mode' => ['type' => 'select', 'label' => 'حالت کارکرد (Mode)', 'options' => ['sandbox' => 'سندباکس (تست)', 'normal' => 'اصلی (محیط عملیاتی)']],
                    'merchantId' => ['type' => 'text', 'label' => 'کد پذیرنده (Merchant ID)', 'placeholder' => 'xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx'],
                ],
                'defaults' => [
                    'enabled' => true,
                    'mode' => 'sandbox',
                    'merchantId' => 'xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx',
                    'description' => 'پرداخت امن آنلاین از طریق تمام کارت‌های عضو شتاب (زرین‌پال)',
                ],
            ],
            'zibal' => [
                'name' => 'زیبال',
                'driver' => 'zibal',
                'icon' => 'ti ti-credit-card',
                'fields' => [
                    'merchantId' => ['type' => 'text', 'label' => 'کد پذیرنده (Merchant ID)', 'placeholder' => 'zibal (برای تست) یا کد اختصاصی'],
                ],
                'defaults' => [
                    'enabled' => true,
                    'merchantId' => 'zibal',
                    'description' => 'پرداخت آنلاین از طریق درگاه واسط زیبال',
                ],
            ],
            'saman' => [
                'name' => 'سامان کیش (SEP)',
                'driver' => 'saman',
                'icon' => 'ti ti-building-bank',
                'fields' => [
                    'merchantId' => ['type' => 'text', 'label' => 'کد پذیرنده (Merchant ID)', 'placeholder' => 'کد پذیرنده بانک سامان'],
                    'terminalId' => ['type' => 'text', 'label' => 'شماره ترمینال (Terminal ID)', 'placeholder' => 'ترمینال بانک سامان'],
                ],
                'defaults' => [
                    'enabled' => false,
                    'merchantId' => '',
                    'terminalId' => '',
                    'description' => 'پرداخت از طریق درگاه پرداخت الکترونیک بانک سامان',
                ],
            ],
            'mellat' => [
                'name' => 'به پرداخت ملت',
                'driver' => 'mellat',
                'icon' => 'ti ti-building-bank',
                'fields' => [
                    'terminalId' => ['type' => 'text', 'label' => 'شماره ترمینال (Terminal ID)', 'placeholder' => 'ترمینال به پرداخت'],
                    'username' => ['type' => 'text', 'label' => 'نام کاربری (Username)', 'placeholder' => 'نام کاربری به پرداخت'],
                    'password' => ['type' => 'password', 'label' => 'کلمه عبور (Password)', 'placeholder' => 'رمز عبور به پرداخت'],
                ],
                'defaults' => [
                    'enabled' => false,
                    'terminalId' => '',
                    'username' => '',
                    'password' => '',
                    'description' => 'پرداخت مستقیم از طریق درگاه به پرداخت بانک ملت',
                ],
            ],
            'parsian' => [
                'name' => 'پارسیان (PEC)',
                'driver' => 'parsian',
                'icon' => 'ti ti-credit-card',
                'fields' => [
                    'merchantId' => ['type' => 'text', 'label' => 'کد پذیرنده (PIN/Merchant ID)', 'placeholder' => 'پین تاپ یا مرچنت پارسیان'],
                ],
                'defaults' => [
                    'enabled' => false,
                    'merchantId' => '',
                    'description' => 'پرداخت از طریق درگاه پارسیان',
                ],
            ],
            'nextpay' => [
                'name' => 'نکست‌پی',
                'driver' => 'nextpay',
                'icon' => 'ti ti-link',
                'fields' => [
                    'apiKey' => ['type' => 'text', 'label' => 'کلید API (API Key)', 'placeholder' => 'کلید اختصاصی نکست‌پی'],
                ],
                'defaults' => [
                    'enabled' => false,
                    'apiKey' => '',
                    'description' => 'پرداخت آنلاین سریع از طریق نکست‌پی',
                ],
            ],
            'sadad' => [
                'name' => 'سداد (بانک ملی)',
                'driver' => 'sadad',
                'icon' => 'ti ti-building-bank',
                'fields' => [
                    'merchantId' => ['type' => 'text', 'label' => 'کد پذیرنده (Merchant ID)', 'placeholder' => 'مرچنت سداد'],
                    'terminalId' => ['type' => 'text', 'label' => 'شماره ترمینال (Terminal ID)', 'placeholder' => 'ترمینال سداد'],
                    'key' => ['type' => 'password', 'label' => 'کلید امضا (Key)', 'placeholder' => 'کلید تراکنش سداد'],
                ],
                'defaults' => [
                    'enabled' => false,
                    'merchantId' => '',
                    'terminalId' => '',
                    'key' => '',
                    'description' => 'پرداخت مستقیم از طریق سداد بانک ملی ایران',
                ],
            ],
            'idpay' => [
                'name' => 'آیدی‌پی (IDPay)',
                'driver' => 'idpay',
                'icon' => 'ti ti-wallet',
                'fields' => [
                    'sandbox' => ['type' => 'select', 'label' => 'حالت تست (Sandbox)', 'options' => ['1' => 'فعال (تست)', '0' => 'غیرفعال (اصلی)']],
                    'apiKey' => ['type' => 'text', 'label' => 'کلید API', 'placeholder' => 'کلید API درگاه آیدی‌پی'],
                ],
                'defaults' => [
                    'enabled' => false,
                    'sandbox' => '1',
                    'apiKey' => '',
                    'description' => 'پرداخت امن از طریق درگاه آیدی‌پی',
                ],
            ],
            'payir' => [
                'name' => 'پی‌آیر (Pay.ir)',
                'driver' => 'payir',
                'icon' => 'ti ti-currency-dollar',
                'fields' => [
                    'apiKey' => ['type' => 'text', 'label' => 'کلید API (برای تست: test)', 'placeholder' => 'test یا کلید اصلی'],
                ],
                'defaults' => [
                    'enabled' => false,
                    'apiKey' => 'test',
                    'description' => 'پرداخت از طریق شبکه درگاه Pay.ir',
                ],
            ],
            'asanpardakht' => [
                'name' => 'آسان پرداخت (آپ)',
                'driver' => 'asanpardakht',
                'icon' => 'ti ti-device-mobile',
                'fields' => [
                    'merchantId' => ['type' => 'text', 'label' => 'Merchant ID', 'placeholder' => 'کد پذیرنده آپ'],
                    'username' => ['type' => 'text', 'label' => 'Username', 'placeholder' => 'نام کاربری آپ'],
                    'password' => ['type' => 'password', 'label' => 'Password', 'placeholder' => 'رمز عبور آپ'],
                    'configurationId' => ['type' => 'text', 'label' => 'Configuration ID', 'placeholder' => 'کد کانفیگ آپ'],
                ],
                'defaults' => [
                    'enabled' => false,
                    'merchantId' => '',
                    'username' => '',
                    'password' => '',
                    'configurationId' => '',
                    'description' => 'پرداخت از طریق آسان پرداخت',
                ],
            ],
        ];
    }

    /**
     * Get configured payment gateway settings from database.
     */
    public static function getSettings(): array
    {
        $raw = Setting::get('payment_gateways');
        $stored = $raw ? json_decode($raw, true) : [];

        $supported = static::supportedDrivers();
        $gateways = [];

        foreach ($supported as $driver => $meta) {
            $userConfig = $stored['gateways'][$driver] ?? [];
            $merged = array_merge($meta['defaults'], $userConfig);
            $merged['name'] = $meta['name'];
            $merged['driver'] = $driver;
            $merged['icon'] = $meta['icon'];
            $gateways[$driver] = $merged;
        }

        $defaultDriver = $stored['default'] ?? 'zarinpal';
        if (!isset($gateways[$defaultDriver])) {
            $defaultDriver = 'zarinpal';
        }

        return [
            'default' => $defaultDriver,
            'gateways' => $gateways,
        ];
    }

    /**
     * Save payment gateway settings to database.
     */
    public static function saveSettings(array $data): void
    {
        $currentSettings = static::getSettings();
        $gateways = $currentSettings['gateways'];

        if (isset($data['default'])) {
            $currentSettings['default'] = $data['default'];
        }

        if (isset($data['gateways']) && is_array($data['gateways'])) {
            foreach ($data['gateways'] as $driver => $config) {
                if (!isset($gateways[$driver])) {
                    continue;
                }

                $gateways[$driver]['enabled'] = isset($config['enabled']) && ($config['enabled'] == '1' || $config['enabled'] === true);
                $gateways[$driver]['description'] = $config['description'] ?? $gateways[$driver]['description'];

                foreach ($config as $key => $val) {
                    if (!in_array($key, ['name', 'driver', 'icon', 'enabled'])) {
                        $gateways[$driver][$key] = $val;
                    }
                }
            }
        }

        $currentSettings['gateways'] = $gateways;

        Setting::set('payment_gateways', json_encode($currentSettings, JSON_UNESCAPED_UNICODE), [
            'group' => 'payment',
            'label' => 'تنظیمات درگاه‌های پرداخت آنلاین',
            'type' => 'json',
        ]);
    }

    /**
     * Get list of active (enabled) payment gateways for checkout.
     */
    public static function getActiveGateways(): array
    {
        $settings = static::getSettings();
        $active = [];

        foreach ($settings['gateways'] as $driver => $gateway) {
            if (!empty($gateway['enabled'])) {
                $active[$driver] = [
                    'driver' => $driver,
                    'name' => $gateway['name'],
                    'icon' => $gateway['icon'],
                    'description' => $gateway['description'] ?? '',
                    'is_default' => ($settings['default'] === $driver),
                ];
            }
        }

        // If no gateway is active, fallback to zarinpal sandbox mode so checkout is never broken
        if (empty($active)) {
            $active['zarinpal'] = [
                'driver' => 'zarinpal',
                'name' => 'زرین‌پال (حالت تست)',
                'icon' => 'ti ti-brand-cashapp',
                'description' => 'پرداخت تست آنلاین از طریق زرین‌پال',
                'is_default' => true,
            ];
        }

        return $active;
    }

    /**
     * Setup Shetabit config dynamically at runtime for the given driver.
     */
    public static function configureDriver(string $driver): array
    {
        $settings = static::getSettings();
        $gatewayConfig = $settings['gateways'][$driver] ?? null;

        $baseConfig = Config::get("payment.drivers.{$driver}", []);

        if ($gatewayConfig) {
            $merged = array_merge($baseConfig, $gatewayConfig);

            // Handle sandbox boolean casting for IDPay
            if ($driver === 'idpay') {
                $merged['sandbox'] = !empty($gatewayConfig['sandbox']) && $gatewayConfig['sandbox'] != '0';
            }

            // Sync with Laravel config array for shetabit facade
            Config::set("payment.drivers.{$driver}", $merged);

            // Ensure driver mapping exists in config('payment.map')
            $maps = Config::get('payment.map', []);
            if (empty($maps[$driver])) {
                $driverClass = match ($driver) {
                    'zarinpal' => \Shetabit\Multipay\Drivers\Zarinpal\Zarinpal::class,
                    'zibal' => \Shetabit\Multipay\Drivers\Zibal\Zibal::class,
                    'saman' => \Shetabit\Multipay\Drivers\Saman\Saman::class,
                    'mellat', 'behpardakht' => \Shetabit\Multipay\Drivers\Behpardakht\Behpardakht::class,
                    'parsian' => \Shetabit\Multipay\Drivers\Parsian\Parsian::class,
                    'nextpay' => \Shetabit\Multipay\Drivers\Nextpay\Nextpay::class,
                    'sadad' => \Shetabit\Multipay\Drivers\Sadad\Sadad::class,
                    'asanpardakht' => \Shetabit\Multipay\Drivers\Asanpardakht\Asanpardakht::class,
                    'payping' => \Shetabit\Multipay\Drivers\Payping\Payping::class,
                    'paystar' => \Shetabit\Multipay\Drivers\Paystar\Paystar::class,
                    default => null
                };
                if ($driverClass) {
                    Config::set("payment.map.{$driver}", $driverClass);
                }
            }

            return $merged;
        }

        return $baseConfig;
    }
}
