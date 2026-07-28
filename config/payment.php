<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Driver
    |--------------------------------------------------------------------------
    */
    'default' => 'zarinpal',

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    | Toman or Rial
    */
    'currency' => 'Toman',

    /*
    |--------------------------------------------------------------------------
    | Payment Drivers
    |--------------------------------------------------------------------------
    */
    'drivers' => [
        'zarinpal' => [
            'apiPurchaseUrl' => 'https://api.zarinpal.com/pg/v4/payment/request.json',
            'apiPaymentUrl' => 'https://www.zarinpal.com/pg/StartPay/',
            'apiVerificationUrl' => 'https://api.zarinpal.com/pg/v4/payment/verify.json',
            'sandboxApiPurchaseUrl' => 'https://sandbox.zarinpal.com/pg/v4/payment/request.json',
            'sandboxApiPaymentUrl' => 'https://sandbox.zarinpal.com/pg/StartPay/',
            'sandboxApiVerificationUrl' => 'https://sandbox.zarinpal.com/pg/v4/payment/verify.json',
            'mode' => 'sandbox',
            'merchantId' => 'xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx',
            'description' => 'پرداخت سفارش از طریق درگاه آنلاین زرین‌پال',
        ],
        'zibal' => [
            'apiPurchaseUrl' => 'https://gateway.zibal.ir/v1/request',
            'apiPaymentUrl' => 'https://gateway.zibal.ir/start/',
            'apiVerificationUrl' => 'https://gateway.zibal.ir/v1/verify',
            'merchantId' => 'zibal',
            'description' => 'پرداخت سفارش از طریق درگاه زیبال',
        ],
        'saman' => [
            'apiPurchaseUrl' => 'https://sep.shaparak.ir/onlinepg/onlinepg',
            'apiPaymentUrl' => 'https://sep.shaparak.ir/OnlinePG/SendToken',
            'apiVerificationUrl' => 'https://sep.shaparak.ir/verifyTxnRandomSessionId/onlinepg',
            'merchantId' => '',
            'terminalId' => '',
            'description' => 'پرداخت سفارش از طریق سامان کیش (SEP)',
        ],
        'mellat' => [
            'apiPurchaseUrl' => 'https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl',
            'apiPaymentUrl' => 'https://bpm.shaparak.ir/pgwchannel/startpay.mellat',
            'apiVerificationUrl' => 'https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl',
            'terminalId' => '',
            'username' => '',
            'password' => '',
            'description' => 'پرداخت سفارش از طریق بهپرداخت ملت',
        ],
        'behpardakht' => [
            'apiPurchaseUrl' => 'https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl',
            'apiPaymentUrl' => 'https://bpm.shaparak.ir/pgwchannel/startpay.mellat',
            'apiVerificationUrl' => 'https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl',
            'terminalId' => '',
            'username' => '',
            'password' => '',
            'description' => 'پرداخت سفارش از طریق بهپرداخت ملت',
        ],
        'parsian' => [
            'apiPurchaseUrl' => 'https://pec.shaparak.ir/NewIPGServices/Sale/SaleService.asmx?wsdl',
            'apiPaymentUrl' => 'https://pec.shaparak.ir/NewIPG/',
            'apiVerificationUrl' => 'https://pec.shaparak.ir/NewIPGServices/Confirm/ConfirmService.asmx?wsdl',
            'merchantId' => '',
            'description' => 'پرداخت سفارش از طریق بانک پارسیان',
        ],
        'nextpay' => [
            'apiPurchaseUrl' => 'https://nextpay.org/nx/gateway/token',
            'apiPaymentUrl' => 'https://nextpay.org/nx/gateway/payment/',
            'apiVerificationUrl' => 'https://nextpay.org/nx/gateway/verify',
            'merchantId' => '',
            'description' => 'پرداخت سفارش از طریق درگاه نکست‌پی',
        ],
        'sadad' => [
            'apiPaymentByMultiIdentityUrl' => 'https://sadad.shaparak.ir/VPG/api/v0/PaymentByMultiIdentityRequest',
            'apiPaymentByIdentityUrl' => 'https://sadad.shaparak.ir/api/v0/PaymentByIdentity/PaymentRequest',
            'apiPaymentUrl' => 'https://sadad.shaparak.ir/api/v0/Request/PaymentRequest',
            'apiPurchaseUrl' => 'https://sadad.shaparak.ir/Purchase',
            'apiVerificationUrl' => 'https://sadad.shaparak.ir/VPG/api/v0/Advice/Verify',
            'key' => '',
            'merchantId' => '',
            'terminalId' => '',
            'description' => 'پرداخت سفارش از طریق سداد (بانک ملی)',
        ],
        'asanpardakht' => [
            'apiPaymentUrl' => 'https://asan.shaparak.ir',
            'apiRestPaymentUrl' => 'https://ipgrest.asanpardakht.ir/v1/',
            'username' => '',
            'password' => '',
            'merchantConfigID' => '',
            'description' => 'پرداخت سفارش از طریق آسان پرداخت',
        ],
        'payping' => [
            'apiPurchaseUrl' => 'https://api.payping.ir/v3/pay/',
            'apiPaymentUrl' => 'https://api.payping.ir/v3/pay/start/',
            'apiVerificationUrl' => 'https://api.payping.ir/v3/pay/verify/',
            'merchantId' => '',
            'description' => 'پرداخت سفارش از طریق پی‌پینگ',
        ],
        'paystar' => [
            'apiPurchaseUrl' => 'https://core.paystar.ir/api/pardakht/create/',
            'apiPaymentUrl' => 'https://core.paystar.ir/api/pardakht/payment/',
            'apiVerificationUrl' => 'https://core.paystar.ir/api/pardakht/verify/',
            'gatewayId' => '',
            'signKey' => '',
            'description' => 'پرداخت سفارش از طریق پی‌استار',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Class Maps
    |--------------------------------------------------------------------------
    */
    'map' => [
        'local' => \Shetabit\Multipay\Drivers\Local\Local::class,
        'panapal' => \Shetabit\Multipay\Drivers\Panapal\Panapal::class,
        'gooyapay' => \Shetabit\Multipay\Drivers\Gooyapay\Gooyapay::class,
        'fanavacard' => \Shetabit\Multipay\Drivers\Fanavacard\Fanavacard::class,
        'asanpardakht' => \Shetabit\Multipay\Drivers\Asanpardakht\Asanpardakht::class,
        'atipay' => \Shetabit\Multipay\Drivers\Atipay\Atipay::class,
        'behpardakht' => \Shetabit\Multipay\Drivers\Behpardakht\Behpardakht::class,
        'mellat' => \Shetabit\Multipay\Drivers\Behpardakht\Behpardakht::class,
        'digipay' => \Shetabit\Multipay\Drivers\Digipay\Digipay::class,
        'etebarino' => \Shetabit\Multipay\Drivers\Etebarino\Etebarino::class,
        'irandargah' => \Shetabit\Multipay\Drivers\IranDargah\IranDargah::class,
        'irankish' => \Shetabit\Multipay\Drivers\Irankish\Irankish::class,
        'jibit' => \Shetabit\Multipay\Drivers\Jibit\Jibit::class,
        'nextpay' => \Shetabit\Multipay\Drivers\Nextpay\Nextpay::class,
        'omidpay' => \Shetabit\Multipay\Drivers\Omidpay\Omidpay::class,
        'parsian' => \Shetabit\Multipay\Drivers\Parsian\Parsian::class,
        'parspal' => \Shetabit\Multipay\Drivers\Parspal\Parspal::class,
        'pasargad' => \Shetabit\Multipay\Drivers\Pasargad\Pasargad::class,
        'paypal' => \Shetabit\Multipay\Drivers\Paypal\Paypal::class,
        'payping' => \Shetabit\Multipay\Drivers\Payping\Payping::class,
        'paystar' => \Shetabit\Multipay\Drivers\Paystar\Paystar::class,
        'poolam' => \Shetabit\Multipay\Drivers\Poolam\Poolam::class,
        'sadad' => \Shetabit\Multipay\Drivers\Sadad\Sadad::class,
        'saman' => \Shetabit\Multipay\Drivers\Saman\Saman::class,
        'sep' => \Shetabit\Multipay\Drivers\SEP\SEP::class,
        'sepehr' => \Shetabit\Multipay\Drivers\Sepehr\Sepehr::class,
        'yekpay' => \Shetabit\Multipay\Drivers\Yekpay\Yekpay::class,
        'zarinpal' => \Shetabit\Multipay\Drivers\Zarinpal\Zarinpal::class,
        'zibal' => \Shetabit\Multipay\Drivers\Zibal\Zibal::class,
        'sepordeh' => \Shetabit\Multipay\Drivers\Sepordeh\Sepordeh::class,
        'rayanpay' => \Shetabit\Multipay\Drivers\Rayanpay\Rayanpay::class,
        'shepa' => \Shetabit\Multipay\Drivers\Shepa\Shepa::class,
        'sizpay' => \Shetabit\Multipay\Drivers\Sizpay\Sizpay::class,
        'vandar' => \Shetabit\Multipay\Drivers\Vandar\Vandar::class,
        'aqayepardakht' => \Shetabit\Multipay\Drivers\Aqayepardakht\Aqayepardakht::class,
        'azki' => \Shetabit\Multipay\Drivers\Azki\Azki::class,
        'payfa' => \Shetabit\Multipay\Drivers\Payfa\Payfa::class,
        'toman' => \Shetabit\Multipay\Drivers\Toman\Toman::class,
        'bitpay' => \Shetabit\Multipay\Drivers\Bitpay\Bitpay::class,
        'minipay' => \Shetabit\Multipay\Drivers\Minipay\Minipay::class,
        'snapppay' => \Shetabit\Multipay\Drivers\SnappPay\SnappPay::class,
        'pna' => \Shetabit\Multipay\Drivers\Pna\Pna::class,
        'stripe' => \Shetabit\Multipay\Drivers\Stripe\Stripe::class,
        'torobpay' => \Shetabit\Multipay\Drivers\TorobPay\TorobPay::class,
        'xendit' => \Shetabit\Multipay\Drivers\Xendit\Xendit::class,
    ]
];
