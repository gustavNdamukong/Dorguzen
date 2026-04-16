<?php

use Dorguzen\Core\DGZ_Container;
use Dorguzen\Core\DGZ_Request;
use Dorguzen\Core\DGZ_Response;
use Dorguzen\Core\DGZ_JsonFormatter;
use Dorguzen\Core\DGZ_Application;
use Dorguzen\Core\DGZ_Validator;
use Dorguzen\Core\DGZ_DBAdapter;
use Dorguzen\Core\DGZ_DB_Singleton;
use Dorguzen\Core\Database\Migrations\MigrationLockRepository;
use Dorguzen\Core\Events\EventService;
use Dorguzen\Core\Events\EventDispatcher;
use Dorguzen\Core\Events\ListenerResolver;
use Dorguzen\Core\Queues\QueueManager;
use Dorguzen\Core\Queues\Drivers\SyncQueue;
use Dorguzen\Core\Queues\Drivers\DatabaseQueue;
use Dorguzen\Core\Database\Graph\DGZ_Neo4jClient;
use Dorguzen\Config\Config;

// -------------------------------------------------------------------------
// Bootstrap models — only the generic ones Dorguzen ships with
// -------------------------------------------------------------------------
use Dorguzen\Models\Users;
use Dorguzen\Models\Logs;
use Dorguzen\Models\Password_reset;
use Dorguzen\Models\ContactFormMessage;
use Dorguzen\Models\BaseSettings;
use Dorguzen\Models\Refresh_tokens;
use Dorguzen\Services\AuthService;
use Dorguzen\Services\AdminService;
use Dorguzen\Services\FeedbackService;


/**
 * Dorguzen Application Bootstrap
 *
 * Initialises the DI container, registers core services, and makes them
 * globally accessible via helper functions defined in bootstrap/helpers.php.
 */

//--------------------------------------------------------------------------------------
// Define the container & make it globally accessible
//--------------------------------------------------------------------------------------
$container = new DGZ_Container();
$GLOBALS['container'] = $container;
//--------------------------------------------------------------------------------------


//--------------------------------------------------------------------------------------
// Register the EventService
//--------------------------------------------------------------------------------------
$container->singleton(EventService::class, function ($c) {
    return new EventService(
        new EventDispatcher(
            new ListenerResolver(
                config('events'),
                $c
            ),
            new QueueManager(
                $c->get(Config::class), $c)
        )
    );
});
//--------------------------------------------------------------------------------------


//--------------------------------------------------------------------------------------
// Bind queue drivers
//--------------------------------------------------------------------------------------
$container->singleton(SyncQueue::class, fn () => new SyncQueue());
$container->singleton(DatabaseQueue::class, function ($c) {
    return new DatabaseQueue($c->get(DGZ_DBAdapter::class));
});
$container->singleton(QueueManager::class, function ($c) {
    return new QueueManager($c->get(Config::class), $c);
});
//--------------------------------------------------------------------------------------


//--------------------------------------------------------------------------------------
// Register app models as singletons
//--------------------------------------------------------------------------------------
$container->singleton(Users::class,            fn($c) => new Users($c->get(Config::class)));
$container->singleton(Logs::class,             fn($c) => new Logs($c->get(Config::class)));
$container->singleton(Password_reset::class,   fn($c) => new Password_reset($c->get(Config::class)));
$container->singleton(ContactFormMessage::class, fn($c) => new ContactFormMessage($c->get(Config::class)));
$container->singleton(BaseSettings::class,     fn($c) => new BaseSettings($c->get(Config::class)));
$container->singleton(Refresh_tokens::class,   fn($c) => new Refresh_tokens($c->get(Config::class)));

// Services
$container->singleton(AuthService::class, fn($c) => new AuthService(
    $c->get(Users::class),
    $c->get(Logs::class),
    $c->get(Password_reset::class),
));

$container->singleton(AdminService::class, fn($c) => new AdminService(
    $c->get(Users::class),
    $c->get(Logs::class),
    $c->get(BaseSettings::class),
    $c->get(ContactFormMessage::class),
));

$container->singleton(FeedbackService::class, fn($c) => new FeedbackService(
    $c->get(ContactFormMessage::class),
));
//--------------------------------------------------------------------------------------


//--------------------------------------------------------------------------------------
// Register Neo4j if installed
//--------------------------------------------------------------------------------------
if (class_exists(\Laudis\Neo4j\ClientBuilder::class)) {
    $container->singleton(
        DGZ_Neo4jClient::class,
        fn ($c) => new DGZ_Neo4jClient($c->get(Config::class))
    );
}
//--------------------------------------------------------------------------------------


//--------------------------------------------------------------------------------------
// Start sessions
//--------------------------------------------------------------------------------------
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
//--------------------------------------------------------------------------------------


//--------------------------------------------------------------------------------------
// container() helper — globally resolve any class from the DI container
//--------------------------------------------------------------------------------------
if (!function_exists('container')) {
    function container(?string $abstract = null, array $parameters = [])
    {
        global $container;

        if (!$container) {
            $container = new DGZ_Container();
        }

        if ($abstract === null || $abstract === '') {
            return $container;
        }

        static $resolving = [];

        if (isset($resolving[$abstract])) {
            error_log("⚠️  Circular dependency detected while resolving: {$abstract}");
            return null;
        }

        $resolving[$abstract] = true;

        try {
            $object = $container->get($abstract, $parameters);

            if (!is_object($object)) {
                throw new Exception("container() failed to resolve object for {$abstract}");
            }

            return $object;
        } finally {
            unset($resolving[$abstract]);
        }
    }
}
//--------------------------------------------------------------------------------------


//--------------------------------------------------------------------------------------
// Bind request / response / application / validator / DB adapter
//--------------------------------------------------------------------------------------
$container->set(DGZ_Request::class, function() {
    static $request;
    if (!$request) {
        $request = new DGZ_Request();
    }
    return $request;
});

$container->set(DGZ_Response::class, function() {
    return new DGZ_Response([], 200, new DGZ_JsonFormatter());
});

$container->set(DGZ_Application::class, function() {
    return new DGZ_Application();
});

$container->set(DGZ_Validator::class, function() {
    return new DGZ_Validator();
});

$container->set(DGZ_DBAdapter::class, function () {
    return DGZ_DB_Singleton::getInstance();
});

$container->set(
    MigrationLockRepository::class,
    fn ($c) => new MigrationLockRepository($c->get(DGZ_DBAdapter::class))
);
//--------------------------------------------------------------------------------------


//--------------------------------------------------------------------------------------
// Session management & authentication gate
//--------------------------------------------------------------------------------------
if (!(isset($_SESSION['authenticated']))) {
    if (!((isset($_SESSION['_Guest'])) && ($_SESSION['_Guest'] == 'visitor'))) {
        $_SESSION['_Guest'] = 'visitor';
    }
}

if (isset($_SESSION['authenticated'])) {
    $timelimit = 7200; // 2 hours
    $now = time();

    if (!isset($_COOKIE['rem_me'])) {
        if ((isset($_SESSION['start'])) && ($now > $_SESSION['start'] + $timelimit)) {
            $_SESSION = array();
            if (isset($_COOKIE[session_name()])) {
                ob_end_clean();
                setcookie(session_name(), '', time() - 86400, '/');
            }
            session_destroy();
        } else {
            $_SESSION['start'] = time();
        }
    } else {
        $username = $_COOKIE['rem_me'];
        $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
        setcookie('rem_me', $username, [
            'expires'  => time() + 345600,
            'path'     => '/',
            'domain'   => '',
            'secure'   => $isSecure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        $_SESSION['start'] = time();
    }
}
//--------------------------------------------------------------------------------------
