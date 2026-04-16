<?php

namespace Dorguzen\Core;


/**
 * Base class for all admin-area HTML views.
 *
 * Extends DGZ_HtmlView with an access guard that fires as soon as the view
 * receives its controller context (via setContext()). If the current session
 * does not belong to an authenticated admin user, the visitor is immediately
 * redirected to the login page — no HTML is rendered.
 *
 * USAGE
 * -----
 * Replace `extends DGZ_HtmlView` with `extends DGZ_AdminHtmlView` in every
 * view file under views/admin/. No other changes are needed — the auth check
 * is fully automatic.
 *
 *   class camcom_open_orders extends DGZ_AdminHtmlView { ... }
 *
 * The guard checks two things:
 *   1. The session 'authenticated' token matches 'Let Go-{appName}'
 *      (set by AuthController::doLogin() on successful login)
 *   2. The session 'user_type' is one of: admin, admin_gen, super_admin
 *
 * NOTE ON ARCHITECTURE
 * --------------------
 * This class is a pragmatic improvement over copy-pasting auth HTML into every
 * admin view. The architecturally more correct solution would have been to enforce 
 * this admin check at the level of defined routes using middleware — a guard
 * on the admin route group that rejects non-admin requests before the controller
 * is even invoked. You can choose to do that, and then just have your admin views
 * extend DGZ_HtmlView directly. But having a separate directory for admin views 
 * (views/admin) helps visually distinguish your admin backend CMS from the rest 
 * of your application views, and makes it easily understandable and readable.
 */
abstract class DGZ_AdminHtmlView extends DGZ_HtmlView
{
    /** User type values that are permitted to access admin views. */
    private const ADMIN_TYPES = ['admin', 'admin_gen', 'super_admin'];


    /**
     * Overrides setContext() to inject the controller AND immediately run the
     * admin access guard. The guard has access to config (for the appName token)
     * because the controller is already set by the time it runs.
     */
    public function setContext(DGZ_Controller &$pageController): void
    {
        parent::setContext($pageController);
        $this->guardAdminAccess();
    }


    /**
     * Verify the session belongs to an authenticated admin.
     * Redirects to the login page and exits if the check fails.
     */
    private function guardAdminAccess(): void
    {
        $appName       = $this->controller->config->getConfig()['appName'] ?? '';
        $expectedToken = 'Let Go-' . $appName;

        $isAuthenticated = isset($_SESSION['authenticated'])
            && $_SESSION['authenticated'] === $expectedToken;

        $isAdmin = isset($_SESSION['user_type'])
            && in_array($_SESSION['user_type'], self::ADMIN_TYPES, strict: true);

        if ($isAuthenticated && $isAdmin) {
            return;
        }

        // Not authorised — redirect to login and stop all further execution.
        $loginUrl = $this->controller->config->getFileRootPath() . 'auth/login';
        header('Location: ' . $loginUrl);
        exit;
    }
}
