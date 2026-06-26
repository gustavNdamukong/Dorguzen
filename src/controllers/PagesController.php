<?php

namespace Dorguzen\Controllers;

use Dorguzen\Core\DGZ_View;
use Dorguzen\Core\DGZ_Controller;

/**
 * Static content pages (Terms & Conditions, etc.).
 */
class PagesController extends DGZ_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getDefaultAction(): string
    {
        return 'terms';
    }

    /**
     * GET /terms
     */
    public function terms(): void
    {
        $view = DGZ_View::getView('terms', $this, 'html');
        $this->setPageTitle('Terms & Conditions');
        $view->show([]);
    }

    /**
     * GET /privacy
     */
    public function privacy(): void
    {
        $view = DGZ_View::getView('privacy', $this, 'html');
        $this->setPageTitle('Privacy & Cookie Policy');
        $view->show([]);
    }
}
