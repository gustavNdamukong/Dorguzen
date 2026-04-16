<?php

namespace Dorguzen\Controllers;

use Dorguzen\Core\DGZ_View;
use Dorguzen\Core\DGZ_Validate;
use Dorguzen\Core\DGZ_Controller;
use Dorguzen\Services\AdminService;

class AdminController extends DGZ_Controller
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
        $view = DGZ_View::getAdminView('adminHome', $this, 'html');
        $this->setPageTitle('Dashboard');
        $this->setLayoutDirectory('admin');
        $this->setLayoutView('adminLayout');
        $view->show([]);
    }

    public function manageUsers()
    {
        $view = DGZ_View::getAdminView('manageUsers', $this, 'html');
        $this->setLayoutDirectory('admin');
        $this->setLayoutView('adminLayout');
        $view->show($this->adminService->buildManageUsersPayload());
    }

    public function createUser()
    {
        $view = DGZ_View::getAdminView('createUser', $this, 'html');
        $this->setLayoutDirectory('admin');
        $this->setLayoutView('adminLayout');
        $view->show();
    }

    public function doCreateUser()
    {
        $val = new DGZ_Validate();

        $user_type    = isset($_POST['new_user_type'])        ? $val->fix_string($_POST['new_user_type'])        : '';
        $fn           = isset($_POST['new_user_fn'])          ? $val->fix_string($_POST['new_user_fn'])          : '';
        $ln           = isset($_POST['new_user_ln'])          ? $val->fix_string($_POST['new_user_ln'])          : '';
        $email        = isset($_POST['new_user_un'])          ? $val->fix_string($_POST['new_user_un'])          : '';
        $phone_number = isset($_POST['new_user_phone'])       ? $val->fix_string($_POST['new_user_phone'])       : '';
        $password     = isset($_POST['new_user_pwd'])         ? $val->fix_string($_POST['new_user_pwd'])         : '';
        $econfirm     = isset($_POST['new_user_pwd_confirm']) ? $val->fix_string($_POST['new_user_pwd_confirm']) : '';

        $fail = $this->adminService->validateCreateUserInput($fn, $ln, $email, $password, $econfirm);

        if ($fail === '') {
            $result = $this->adminService->createUser($user_type, $fn, $ln, $email, $phone_number, $password);

            if ($result === 1062) {
                $this->addErrors('That email address is already registered on this system');
                $this->redirect('admin', 'createUser');
            } elseif ($result) {
                $this->addSuccess('The new user was created!', 'Great');
                $this->redirect('admin', 'manageUsers');
            } else {
                $this->addErrors('The user could not be created. Try again later!', 'Sorry!');
                $this->redirect('admin', 'manageUsers');
            }
        } else {
            $this->addErrors($fail);
            $this->postBack($_POST);
            $this->redirect('admin', 'createUser');
        }
    }

    public function editUser()
    {
        if (isset($_GET['userId']) && $_GET['edit'] == 0) {
            $userId      = (int) $_GET['userId'];
            $userForEdit = $this->adminService->getUserById($userId);

            $view = DGZ_View::getAdminView('editUser', $this, 'html');
            $this->setLayoutDirectory('admin');
            $this->setLayoutView('adminLayout');
            $view->show(['user' => $userForEdit, 'userId' => $userId]);

        } elseif (isset($_GET['edit']) && $_GET['edit'] == 1) {
            $val = new DGZ_Validate();
            $userId = (int) $_POST['userId'];

            $userForEdit  = $this->adminService->getUserById($userId);
            $userType     = isset($_POST['new_user_type'])  ? $val->fix_string($_POST['new_user_type'])  : '';
            $fn           = isset($_POST['new_user_fn'])    ? $val->fix_string($_POST['new_user_fn'])    : '';
            $ln           = isset($_POST['new_user_ln'])    ? $val->fix_string($_POST['new_user_ln'])    : '';
            $email        = isset($_POST['new_user_un'])    ? $val->fix_string($_POST['new_user_un'])    : '';
            $phone_number = isset($_POST['new_user_phone']) ? $val->fix_string($_POST['new_user_phone']) : '';
            $password     = isset($_POST['new_user_pwd'])   ? $val->fix_string($_POST['new_user_pwd'])   : '';

            $fail = $this->adminService->validateEditUserInput($fn, $ln, $email, $password);

            if ($fail === '') {
                $updated = $this->adminService->updateUser($userId, [
                    'users_type'         => $userType,
                    'users_email'        => $email,
                    'users_pass'         => $password,
                    'users_first_name'   => $fn,
                    'users_last_name'    => $ln,
                    'users_phone_number' => $phone_number,
                ]);

                if ($updated) {
                    $this->addSuccess('The user was successfully updated!', 'Hooray');
                    $this->redirect('admin', 'manageUsers');
                }
            } else {
                $this->addErrors($fail);
                $view = DGZ_View::getAdminView('editUser', $this, 'html');
                $this->setLayoutDirectory('admin');
                $this->setLayoutView('adminLayout');
                $view->show(['user' => $userForEdit, 'userId' => $userId]);
            }
        }
    }

    public function adminUserChangePw()
    {
        if (isset($_GET['userId']) && $_GET['change'] == 0) {
            $userId      = (int) $_GET['userId'];
            $userForEdit = $this->adminService->getUserById($userId);

            $view = DGZ_View::getAdminView('adminUserChangePw', $this, 'html');
            $this->setLayoutDirectory('admin');
            $this->setLayoutView('adminLayout');
            $view->show(['user' => $userForEdit, 'userId' => $userId]);

        } elseif (isset($_GET['change']) && $_GET['change'] == 1) {
            $val = new DGZ_Validate();
            $userId = (int) $_POST['userId'];

            $userForEdit = $this->adminService->getUserById($userId);
            $email       = isset($_POST['new_user_un'])  ? $val->fix_string($_POST['new_user_un'])  : '';
            $password    = isset($_POST['new_user_pwd']) ? $val->fix_string($_POST['new_user_pwd']) : '';

            $fail = $this->adminService->validateChangePasswordInput($email, $password);

            if ($fail === '') {
                $updatedUser = $this->adminService->updateUserPassword($userId, $email, $password);

                if ($updatedUser) {
                    $_SESSION['email'] = $updatedUser['users_email'];
                    $this->addSuccess('Password successfully updated!', 'Hooray');
                    $this->redirect('admin', 'dashboard');
                }
            } else {
                $this->addErrors($fail);
                $view = DGZ_View::getAdminView('adminUserChangePw', $this, 'html');
                $this->setLayoutDirectory('admin');
                $this->setLayoutView('adminLayout');
                $view->show(['user' => $userForEdit, 'userId' => $userId]);
            }
        }
    }

    public function deleteUser()
    {
        $userId  = (int) $_GET['userId'];
        $deleted = $this->adminService->deleteUser($userId);

        if ($deleted) {
            $this->addSuccess('The user was successfully deleted!');
        } else {
            $this->addErrors('Could not delete the user. Please try again.');
        }
        $this->redirect('admin', 'manageUsers');
    }

    public function contactMessages()
    {
        $view = DGZ_View::getAdminView('manageContactMessages', $this, 'html');
        $this->setLayoutDirectory('admin');
        $this->setLayoutView('adminLayout');
        $view->show($this->adminService->buildContactMessagesPayload());
    }

    public function deleteContactMessage()
    {
        if (isset($_GET['contactformmessage_id']) && $_GET['contactformmessage_id'] !== '') {
            $recId   = (int) $_GET['contactformmessage_id'];
            $deleted = $this->adminService->deleteContactMessage($recId);

            if ($deleted) {
                $this->addSuccess('The contact message was deleted');
                $this->redirect('admin', 'contactMessages');
                return;
            }
        }
        $this->addErrors('Could not delete the message. Check the record ID.');
        $this->redirect('admin', 'contactMessages');
    }

    public function baseSettings()
    {
        if (!isset($_GET['change']) || $_GET['change'] == 0) {
            $view = DGZ_View::getAdminView('manageSettings', $this, 'html');
            $this->setLayoutDirectory('admin');
            $this->setLayoutView('adminLayout');
            $view->show(['baseSettings' => $this->adminService->getAllBaseSettings()]);
        } elseif (isset($_GET['change']) && $_GET['change'] == 1) {
            $this->adminService->updateBaseSettings($_POST);
            $this->addSuccess('Settings saved!', 'Great!');
            $this->redirect('admin', 'dashboard');
        }
    }

    public function log()
    {
        $view = DGZ_View::getAdminView('logs', $this, 'html');
        $this->setLayoutDirectory('admin');
        $this->setLayoutView('adminLayout');
        $view->show($this->adminService->buildLogPayload());
    }

    public function log_errors_only()
    {
        $runtimeErrorLogs = $this->adminService->getRuntimeErrorLogs();
        $pagination       = $this->getPaginationMarkers($runtimeErrorLogs);

        $view = DGZ_View::getAdminView('logs_errors_only', $this, 'html');
        $this->setLayoutDirectory('admin');
        $this->setLayoutView('adminLayout');
        $view->show($this->adminService->buildErrorsLogPayload($runtimeErrorLogs, $pagination));
    }

    public function logAdminLogins()
    {
        $view = DGZ_View::getAdminView('logs_admin_logins', $this, 'html');
        $this->setLayoutDirectory('admin');
        $this->setLayoutView('adminLayout');
        $view->show($this->adminService->buildAdminLoginsPayload());
    }
}
