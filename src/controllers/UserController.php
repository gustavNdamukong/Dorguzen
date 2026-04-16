<?php

namespace Dorguzen\Controllers;

use Dorguzen\Core\DGZ_View;
use Dorguzen\Core\DGZ_Controller;
use Dorguzen\Services\AdminService;

class UserController extends DGZ_Controller
{
    public function __construct(private AdminService $adminService)
    {
        parent::__construct();
    }

    public function getDefaultAction()
    {
        return 'defaultAction';
    }

    public function defaultAction()
    {
        $this->dashboard();
    }

    public function dashboard()
    {
        $allUsers = $this->adminService->getAllUsers();
        $view = DGZ_View::getAdminView('adminHome', $this, 'html');
        $this->setPageTitle('Dashboard');
        $this->setLayoutDirectory('admin');
        $this->setLayoutView('adminLayout');
        $view->show($allUsers);
    }
}
