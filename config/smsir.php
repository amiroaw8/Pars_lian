<?php

/*
 * You can place your custom package configuration in here.
 */
return [

    /* Important Settings */

    // ======================================================================
    // never remove 'web', just put your middleware like auth or admin (if you have) here. eg: ['web','auth']
    'middlewares' => ['web', 'auth', 'role:admin,super_admin'],
    // you can change default route from sms-admin to anything you want
    'route' => 'admin/sms-panel',
    // SMS.ir Api Key
    'api-key' => env('SMSIR_API_KEY', 'Your api key'),
    // Your sms.ir line number
    'line-number' => env('SMSIR_LINE_NUMBER', 'Your Sms.ir Line Number'),
    // ======================================================================
    // set true if you want log to the database
    'db-log' => env('SMSIR_DB_LOG', false),
    // if you don't want to include admin panel routes set this to false
    'panel-routes' => false,
    /* Admin Panel Title */
    'title' => 'مدیریت پیامک ها',
    // How many log you want to show in sms-admin panel ?
    'in-page' => env('SMSIR_PANEL_IN_PAGE', true),
];
