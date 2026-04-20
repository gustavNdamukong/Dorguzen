<?php return array (
  'app' => 
  array (
    'appName' => 'dorguzen',
    'appBusinessName' => 'Dorguzen Framework',
    'appSlogan' => 'Your Rapid Web Development Toolkit',
    'appURL' => 'http://localhost/dorguzen',
    'layoutDirectory' => 'seoMaster',
    'defaultLayout' => 'seoMasterLayout',
    'localUrl' => 'http://localhost/dorguzen/',
    'liveUrl' => 'https://www.dorguzen.com/',
    'liveUrlSecure' => 'https://www.dorguzen.com/',
    'fileRootPathLocal' => '/dorguzen/',
    'fileRootPathLive' => '/',
    'live' => 'false',
    'allow_registration' => true,
    'locale' => 'en',
    'fallback_locale' => 'en',
    'sliderType' => 'slider',
    'maxFileUploadSize' => 10240000000,
    'defaultImageDir' => 'images/',
    'emailImageDir' => 'assets/images/email_images/',
    'audioUploadDir' => 'docs/audios/',
    'videoUploadDir' => 'docs/videos/',
    'site_contact_tel' => '',
    'site_postal_address' => '',
    'appEmail' => 'your@email.com',
    'appEmailOther' => 'your@email.com',
    'localHeaderFrom' => 'your@email.com',
    'liveHeaderFrom' => 'your@email.com',
    'headerReply-To' => 'your@email.com',
    'jwt-secret-key' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
    'encoding_algorithm' => 'HS256',
    'csrf_except' => 
    array (
      0 => '/api/',
    ),
    'queue_driver' => 'sync',
    'modules' => 
    array (
      'seo' => 'on',
      'payments' => 'off',
      'sms' => 'off',
    ),
    'permissions' => 
    array (
      'seo' => 
      array (
        0 => 'admin',
        1 => 'admin_gen',
        2 => 'super_admin',
      ),
      'payments' => 
      array (
        0 => 'admin_gen',
        1 => 'super_admin',
      ),
      'manage_users' => 
      array (
        0 => 'admin',
        1 => 'admin_gen',
        2 => 'super_admin',
      ),
      'settings' => 
      array (
        0 => 'super_admin',
      ),
    ),
  ),
  'database' => 
  array (
    'DBcredentials' => 
    array (
      'username' => 'root',
      'pwd' => 'root',
      'db' => 'dorguzapp',
      'host' => '127.0.0.1',
      'connectionType' => 'sqlite',
      'port' => 3306,
      'key' => 'takeThisWith@PinchOfSalt',
      'sqlite_path' => ':memory:',
    ),
    'Neo4jCredentials' => 
    array (
      'uri' => 'bolt://localhost:7687',
      'username' => 'neo4j',
      'password' => '',
    ),
  ),
  'events' => 
  array (
    'Dorguzen\\Events\\UserRegistered' => 
    array (
      0 => 'Dorguzen\\Listeners\\SendWelcomeEmail',
      1 => 'Dorguzen\\Listeners\\LogUserRegistration',
    ),
    'Dorguzen\\Events\\UserLoggedIn' => 
    array (
      0 => 'Dorguzen\\Listeners\\LogUserLogin',
    ),
    'Dorguzen\\Events\\UserLoggedOut' => 
    array (
      0 => 'Dorguzen\\Listeners\\LogUserLogout',
    ),
    'Dorguzen\\Events\\UserSubscribed' => 
    array (
      0 => 'Dorguzen\\Listeners\\SendSubscriptionConfirmation',
    ),
    'Dorguzen\\Events\\ContactFormSubmitted' => 
    array (
      0 => 'Dorguzen\\Listeners\\SendContactConfirmation',
    ),
  ),
  'modules' => 
  array (
    'ModuleConfigExample' => 
    array (
    ),
  ),
  'logging' => 
  array (
    'channels' => 
    array (
      'default' => 
      array (
        'driver' => 'db',
        'format' => 'text',
        'path' => 'storage/logs',
        'min_level' => 'debug',
        'filename_prefix' => 'dgz',
      ),
      'payments' => 
      array (
        'driver' => 'file',
        'format' => 'json',
        'path' => 'storage/logs',
        'min_level' => 'warning',
      ),
      'security' => 
      array (
        'driver' => 'both',
        'format' => 'json',
        'path' => 'storage/logs',
        'min_level' => 'error',
      ),
    ),
  ),
  'ConfigDELETE' => 
  array (
  ),
);
