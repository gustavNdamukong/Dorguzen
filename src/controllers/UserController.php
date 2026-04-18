<?php

namespace Dorguzen\Controllers;

use Dorguzen\Core\DGZ_View;
use Dorguzen\Core\DGZ_Controller;
use Dorguzen\Core\DGZ_Validate;
use Dorguzen\Services\AdminService;
use Dorguzen\Services\AuthService;

class UserController extends DGZ_Controller
{
    public function __construct(
        private AdminService $adminService,
        private AuthService  $authService,
    ) {
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
        $view = DGZ_View::getView('dashboard', $this, 'html');
        $this->setPageTitle('Dashboard');
        $this->setLayoutDirectory('seoMaster');
        $this->setLayoutView('seoMasterLayout');
        $view->show();
    }

    public function changePw()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $view = DGZ_View::getView('changepw', $this, 'html');
            $this->setPageTitle('Change Password');
            $this->setLayoutDirectory('seoMaster');
            $this->setLayoutView('seoMasterLayout');
            $view->show();
            return;
        }

        // POST — process the form
        $val            = new DGZ_Validate();
        $oldPassword    = isset($_POST['old_password'])     ? $val->fix_string($_POST['old_password'])     : '';
        $newPassword    = isset($_POST['new_password'])     ? $val->fix_string($_POST['new_password'])     : '';
        $confPassword   = isset($_POST['conf_new_password']) ? $val->fix_string($_POST['conf_new_password']) : '';

        if (empty($oldPassword) || empty($newPassword) || empty($confPassword)) {
            $this->addErrors('Please fill in all password fields.');
            $this->redirect('user', 'changePw');
            return;
        }

        if ($newPassword !== $confPassword) {
            $this->addErrors('Your new passwords do not match.');
            $this->redirect('user', 'changePw');
            return;
        }

        $fail = $this->authService->validateLoginInput($_SESSION['email'], $newPassword);
        if ($fail !== '') {
            $this->addErrors($fail);
            $this->redirect('user', 'changePw');
            return;
        }

        // Verify the current password is correct before allowing a change
        $verified = $this->authService->authenticateUser($_SESSION['email'], $oldPassword);
        if (!$verified) {
            $this->addErrors('Your current password is incorrect.');
            $this->redirect('user', 'changePw');
            return;
        }

        $updated = $this->authService->resetUserPassword(
            (string) $_SESSION['custo_id'],
            $_SESSION['email'],
            $newPassword
        );

        if ($updated) {
            $this->addSuccess('Your password has been updated successfully.', 'Done!');
            $this->redirect('user', 'dashboard');
        } else {
            $this->addErrors('Something went wrong. Please try again.');
            $this->redirect('user', 'changePw');
        }
    }
}
