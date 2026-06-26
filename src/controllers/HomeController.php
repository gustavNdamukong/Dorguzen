<?php

namespace Dorguzen\Controllers;

use Dorguzen\Core\DGZ_View;
use Dorguzen\Core\DGZ_Controller;

class HomeController extends DGZ_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getDefaultAction()
    {
        return 'defaultAction';
    }

    public function defaultAction()
    {
        $view = DGZ_View::getView('home', $this, 'html');
        $this->setLayoutDirectory($this->config->getConfig()['layoutDirectory']);
        $this->setLayoutView($this->config->getConfig()['defaultLayout']);
        $this->setPageTitle('Home');
        $this->setImageSlider(true);
        $view->show();
    }


    /**
     * Permanent (301) redirect from "/home" to the canonical site root ("/").
     *
     * The homepage is rendered by defaultAction() at the site root ("/"). A legacy
     * "/home" route also points at the homepage, which means the SAME page would be
     * reachable at two different URLs ("/" and "/home"). Search engines treat those as
     * duplicate content and split the page's ranking signals between them.
     *
     * To avoid that, the "/home" route is wired to this method instead of rendering the
     * page again: it sends a "301 Moved Permanently" response telling browsers and search
     * engines that the real address is the canonical root. getHomePage() returns the
     * correct absolute base URL for the current environment (local vs live), so this works
     * the same in development and production. Any old bookmarks/links to /home still work.
     */
    public function homeRedirect()
    {
        // Send a 301 (permanent) so the legacy "/home" URL consolidates into the canonical
        // root. getHomePage() returns the correct absolute base URL for this environment.
        $this->redirectTo($this->config->getHomePage(), 301);
    }
}
