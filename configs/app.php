<?php

return [

    'appName'         => env('APP_NAME', 'dorguzen'),
    'appBusinessName' => env('APP_BUSINESS_NAME', 'Dorguzen Framework'),
    'appSlogan'       => env('APP_SLOGAN', 'Your Rapid Web Development Toolkit'),
    'appURL'          => env('APP_URL', 'https://www.dorguzen.com'),

    'layoutDirectory' => env('LAYOUT_DIR', 'seoMaster'),
    'defaultLayout'   => env('DEFAULT_LAYOUT', 'seoMasterLayout'),

    'localUrl'          => env('LOCAL_URL', 'http://localhost/dorguzen/'),
    'liveUrl'           => env('LIVE_URL', 'https://www.dorguzen.com/'),
    'liveUrlSecure'     => env('LIVE_URL_SECURE', 'https://www.dorguzen.com/'),
    'fileRootPathLocal' => env('FILE_ROOT_PATH_LOCAL', '/dorguzen/'),
    'fileRootPathLive'  => env('FILE_ROOT_PATH_LIVE', '/'),

    'live' => env('APP_ENV', 'local') === 'production' ? 'true' : 'false',

    'allow_registration' => env('ALLOW_REGISTRATION', true),

    'locale'          => env('APP_LOCALE', 'en'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    'sliderType' => 'slider',

    'maxFileUploadSize' => 10240000000,
    'defaultImageDir'   => 'images/',
    'emailImageDir'     => 'assets/images/email_images/',
    'audioUploadDir'    => 'docs/audios/',
    'videoUploadDir'    => 'docs/videos/',

    'site_contact_tel'    => env('SITE_TEL', ''),
    'site_postal_address' => env('SITE_ADDRESS', ''),

    'appEmail'        => env('APP_EMAIL', 'your@email.com'),
    'appEmailOther'   => env('APP_EMAIL_OTHER', 'your@email.com'),
    'localHeaderFrom' => env('APP_EMAIL', 'your@email.com'),
    'liveHeaderFrom'  => env('APP_EMAIL', 'your@email.com'),
    'headerReply-To'  => env('APP_EMAIL', 'your@email.com'),

    'jwt-secret-key'      => env('APP_JWT_SECRET', 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'),
    'encoding_algorithm'  => env('APP_JWT_ENCODING', 'HS256'),

    // Paths that are exempt from CSRF validation (prefix-matched).
    // Set APP_API_CSRF_EXCEPTION='/api/' in .env to exempt all API routes.
    'csrf_except' => array_filter([
        env('APP_API_CSRF_EXCEPTION'),
    ]),

    'queue_driver' => env('QUEUE_DRIVER', 'sync'),

    'modules' => [
        'seo'      => env('MODULES_SEO_STATUS', 'on'),
        'payments' => env('MODULES_PAYMENTS_STATUS', 'off'),
        'sms'      => env('MODULES_SMS_STATUS', 'off'),
    ],

    'permissions' => [
        'seo'          => ['admin', 'admin_gen', 'super_admin'],
        'payments'     => ['admin_gen', 'super_admin'],
        'manage_users' => ['admin', 'admin_gen', 'super_admin'],
        'settings'     => ['super_admin'],
    ],
];
