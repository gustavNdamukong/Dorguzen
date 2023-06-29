<?php

namespace controllers;



use DGZ_library\DGZ_Validate;
use Users;
use BaseSettings;
use ContactFormMessage;
use DGZ_library\DGZ_View;
use DGZ_library\DGZ_Messenger;
use Logs;


class AdminController extends \DGZ_library\DGZ_Controller  {



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
        $view = \DGZ_library\DGZ_View::getAdminView('adminHome', $this, 'html');
        $this->setPageTitle('User dashboard');

        $view->show();
    }



    public function dashboard()
    {
        $view = DGZ_View::getAdminView('adminHome', $this, 'html');
        $this->setPageTitle('Admin');
        $this->setLayoutDirectory('admin');
        $this->setLayoutView('adminLayout');
        $view->show();
    }


    /**
     * Get vital settings for the application that have been specified by the administrator through the admin dashboard,
     * and stored in the DB settings table. Note that BaseSettings refers to settings in the DB, while the settings class is
     * just a file in settngs/Settings.php containing other minor settings for the app like DB connection credentials etc
     *
     */
    public function getBaseSettings()
    {
        $settings = new BaseSettings();
        $allSettings = $settings->getAll('settings_id');
        return $allSettings;
    }






    public function manageUsers()
    {
        $view = DGZ_View::getAdminView('manageUsers', $this, 'html');
        $this->setLayoutDirectory('admin');
        $this->setLayoutView('adminLayout');
        $view->show();
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
        $user_type = $fn = $ln = $un = $phone_number = $newUserPw = $econfirm = false;
        $fail = "";

        $val = new DGZ_Validate(); 

        if(isset($_POST['new_user_type']))
        {
            $user_type = $val->fix_string($_POST['new_user_type']);
        }

        if(isset($_POST['new_user_fn']))
        {
            $fn = $val->fix_string($_POST['new_user_fn']);
        }

        if(isset($_POST['new_user_ln']))
        {
            $ln = $val->fix_string($_POST['new_user_ln']);
        }

        if(isset($_POST['new_user_un']))
        {
            $email = $val->fix_string($_POST['new_user_un']);
        }

        if(isset($_POST['new_user_phone']))
        {
            $phone_number = $val->fix_string($_POST['new_user_phone']);
        }

        if (isset($_POST['new_user_pwd']))
        {
            $password = $val->fix_string($_POST['new_user_pwd']);
        }

        if (isset($_POST['new_user_pwd_confirm']))
        {
            $econfirm = $val->fix_string($_POST['new_user_pwd_confirm']);
        }


        $fail .= $val->validate_firstname($fn);
        $fail .= $val->validate_surname($ln);
        $fail .= $val->validate_email($email);

        $fail .= $val->validate_password($password);

        if ($password !== $econfirm)
        {
            $fail .= "Both passwords did not match!\n";
        }

        //Make sure email is not already registered in the system
        if ($this->isDuplicateEmail($email))
        {
            $fail .= "That email address is already registered on this system";
        }

        if ($fail == "")
        {
            $user_model = new Users();
            $created = $user_model->timeNow();

            $userCreated = $user_model->createUser($user_type, $fn, $ln, $email, $phone_number, $password, $created);

            if ($userCreated === 1062)
            {
                $this->addErrors('That email address is already registered on this system');
                $this->redirect('admin', 'createUser');
            }
            elseif($userCreated == true)
            {
                $this->addSuccess('The new admin was created!', 'Great');
                $this->redirect('admin', 'manageUsers');
                exit();
            }
            elseif($userCreated == false)
            {
                $this->addErrors('The user could not be created. Try again later!', 'Sorry!');
                $this->redirect('admin', 'manageUsers');
                exit();
            }

        }
        else
        {
            $this->addErrors($fail);
            $this->postBack($_POST);
            $this->redirect('admin', 'createUser');
        }
    }


    public function isDuplicateEmail($email)
    {
        $users = new Users();
        
        $query = "SELECT * FROM users WHERE users_email = '$email'";

        $user = $users->query($query);

        if ($user)
        {
            return true;
        }
    
        else
        {
            return false;
        }
    }







    public function editUser()
    {
        if ((isset($_GET['userId'])) && ($_GET['edit'] == 0))
        {
            $userId = $_GET['userId'];
            $user = new Users();
            $userForEdit = $user->getUserById($userId);

            $view = DGZ_View::getAdminView('editUser', $this, 'html');
            $this->setLayoutDirectory('admin');
            $this->setLayoutView('adminLayout');
            $view->show($userForEdit, $userId);
        }
        elseif((isset($_GET['edit'])) && ($_GET['edit'] == 1))
        {
            $userType = $fn = $ln = $un = $new_user_phone = $newUserPw = false;
            $fail = "";

            $userId = $_POST['userId'];
            $user = new Users();

            $userForEdit = $user->getUserById($userId);

            $val = new DGZ_Validate(); 

            if(isset($_POST['new_user_type']))
            {
                $userType = $val->fix_string($_POST['new_user_type']);
            }

            if(isset($_POST['new_user_fn']))
            {
                $fn = $val->fix_string($_POST['new_user_fn']);
            }

            if(isset($_POST['new_user_ln']))
            {
                $ln = $val->fix_string($_POST['new_user_ln']);
            }

            if(isset($_POST['new_user_un']))
            {
                $email = $val->fix_string($_POST['new_user_un']);
            }

            if(isset($_POST['new_user_phone']))
            {
                $new_user_phone = $val->fix_string($_POST['new_user_phone']);
            }

            if (isset($_POST['new_user_pwd']))
            {
                $password = $val->fix_string($_POST['new_user_pwd']);
            }

            $fail .= $val->validate_firstname($fn);
            $fail .= $val->validate_surname($ln);
            $fail .= $val->validate_email($email);

            $fail .= $val->validate_password($password);

            if ($fail == "")
            {
                $data = [
                    'users_type' =>  $userType,
                    'users_email' => $email,
                    'users_pass' => $password,
                    'users_first_name' => $fn,
                    'users_last_name' => $ln,
                    'users_phone_number' => $new_user_phone
                ];

                $where = ['users_id' => $userId];
                $updated = $user->updateObject($data, $where);

                if ($updated)
                {
                    $this->addSuccess('The user was successfully updated!', 'Hooray');
                    $this->redirect('admin', 'manageUsers');
                }
            }
            else
            {
                $this->addErrors($fail);
                $view = DGZ_View::getAdminView('editUser', $this, 'html');
                $view->show($userForEdit, $userId);
            }
        }
    }







    public function adminUserChangePw()
    {
        if ((isset($_GET['userId'])) && ($_GET['change'] == 0))
        {
            $userId = $_GET['userId'];
            $user = new Users();
            $userForEdit = $user->getUserById($userId);

            $view = DGZ_View::getAdminView('adminUserChangePw', $this, 'html');
            $this->setLayoutDirectory('admin');
            $this->setLayoutView('adminLayout');
            $view->show($userForEdit, $userId);
        }
        elseif(
            (isset($_GET['change'])) && 
            ($_GET['change'] == 1)
         ) { 
            $un = $newUserPw = false;
            $fail = "";

            $userId = $_POST['userId'];
            $user = new Users();

            $userForEdit = $user->getUserById($userId);

            $val = new DGZ_Validate();


            if (isset($_POST['new_user_un'])) {
                $email = $val->fix_string($_POST['new_user_un']);
            }

            if (isset($_POST['new_user_pwd'])) {
                $password = $val->fix_string($_POST['new_user_pwd']);
            }

            $fail .= $val->validate_email($email);

            $fail .= $val->validate_password($password);

            if ($fail == "") {
                $data = [
                    'users_email' => $email, 'users_pass' => $password
                ];

                $where = ['users_id' => $userId];

                $updated = $user->update($data, $where);

                if ($updated) {
                    $userForEdit = $user->getUserById($userId);

                    $_SESSION['email'] = $userForEdit[0]['users_email'];
                    $_SESSION['pass'] = $userForEdit[0]['pass'];

                    $this->addSuccess('Your details were successfully updated!', 'Hooray');
                    $this->redirect('admin', 'dashboard');
                    exit();
                }
            }
            else {
                $this->addErrors($fail);
                $view = DGZ_View::getView('adminUserChangePw', $this, 'html');
                $view->show($userForEdit, $userId);
            }
        }
    }








    public function deleteUser()
    {
        $user = new Users();
        $userId = $_GET['userId'];
        $whereClause = ['users_id' => $userId];
        $deleted = $user->deleteWhere($whereClause);

        if ($deleted)
        {
            $this->addSuccess('The user was successfully deleted!');
            $this->redirect('admin', 'manageUsers');
            exit();
        }
    }






    public function contactMessages()
    {
        $message = new ContactFormMessage();
        $contactMessages = $message->getAll('contactformmessage_date DESC');

        $view = DGZ_View::getAdminView('manageContactMessages', $this, 'html');
        $this->setLayoutDirectory('admin');
        $this->setLayoutView('adminLayout');
        $view->show($contactMessages);
    }





    public function deleteContactMessage($contactformmessage_id)
    {
        if ((isset($_GET['contactformmessage_id'])) && ($_GET['contactformmessage_id'] != "")) {
            $recId = $_GET['contactformmessage_id'];
            $contactFormClass = new ContactFormMessage();

            $whereClause = ['contactformmessage_id' => $recId];
            $deleted = $contactFormClass->deleteWhere($whereClause);
            if ($deleted) {
                $this->addSuccess('The contact message was successfully deleted');
                $this->redirect('admin', 'contactMessages');
                exit();
            }
        }

        $this->addErrors('Something went wrong, check the ID of the record you are trying to delete', 'Error');
        $this->redirect('admin', 'contactMessages');
    }






    public function baseSettings()
    {
        if (
            (!isset($_GET['change'])) || 
            (isset($_GET['change']) && $_GET['change'] == 0)
        )
        {
            $settings= new BaseSettings();
            $baseSettings = $settings->getAll();

            $view = DGZ_View::getAdminView('manageSettings', $this, 'html');
            $this->setLayoutDirectory('admin');
            $this->setLayoutView('adminLayout');
            $view->show($baseSettings);
        }
        elseif((isset($_GET['change'])) && ($_GET['change'] == 1)) {
            $settings= new BaseSettings();
            $table = $settings->getTable();

            foreach ($_POST as $field => $value)
            {
                $sql = "UPDATE ".$table." SET settings_value = '$value' WHERE settings_name = '$field'";
                $settings->query($sql);
            }

            $this->addSuccess('Your settings have been saved!', 'Great!');
            $this->redirect('admin', 'dashboard');
            exit();
        }
    }


    /**
     * Display the logs for admin users to see
     */
    public function log()
    {
        $users = new Users();
        if (
            (isset($_SESSION['custo_id'])) &&
            ($users->isAdmin($_SESSION['custo_id']))
        )
        {
            $logs = new Logs();
            $advanced_logs = $logs->getAll('logs_created DESC', 10);

            $view = \DGZ_library\DGZ_View::getAdminView('logs', $this, 'html');
            $view->show($logs);
        }
        else
        {
            $controller = 'admin';
            $method = 'log';
            $this->addWarning('Please login to access the logs');
            $this->redirect('admin', 'login', ['c' => $controller, 'm' => $method]);
        }
    }

    
    /**
     * Filter logs by runtime errors only
     */
    public function log_errors_only()
    {
        $logs = new Logs();
        $runtime_error_logs = $logs->getRunTimeErrors('logs_created DESC');
        $result = $this->getPaginationMarkers($runtime_error_logs);
        extract($result);

        $view = \DGZ_library\DGZ_View::getAdminView('logs_errors_only', $this, 'html');
        $view->show($runtime_error_logs, $totalRecs, $max_no_perpage, $no_pages, $pageNum, $first_item_onpage, $last_item_onpage);
    }


    /**
     * Filter logs by runtime errors only
     */
    public function logAdminLogins()
    {
        $logs = new Logs();
        $runtime_error_logs = $logs->getAdminLoginData('logs_created DESC');

        $view = \DGZ_library\DGZ_View::getAdminView('logs_admin_logins', $this, 'html');
        $view->show($runtime_error_logs);
    }
}

